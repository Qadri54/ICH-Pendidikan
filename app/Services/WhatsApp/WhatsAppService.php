<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppSetting;
use App\Support\PhoneFormatter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private const DEFAULT_BASE_URL = 'https://api.fonnte.com';

    private ?string $lastError = null;

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function isEnabled(): bool
    {
        $dbValue = WhatsAppSetting::getValue('whatsapp_enabled');

        return filter_var(
            ($dbValue !== null && $dbValue !== '') ? $dbValue : config('services.whatsapp.enabled'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    public function send(string $phone, string $message): bool
    {
        $this->lastError = null;

        $phone = PhoneFormatter::toInternational($phone);
        if (! $phone) {
            $this->lastError = 'Nomor HP tidak valid atau kosong.';
            Log::warning('WhatsApp: nomor HP kosong, pesan tidak dikirim.');
            return false;
        }

        $token = $this->getToken();
        if (empty($token)) {
            $this->lastError = 'Token Fonnte belum dikonfigurasi.';
            Log::error('WhatsApp: FONNTE_TOKEN belum dikonfigurasi.');
            return false;
        }

        try {
            $response = $this->postToFonnte('/send', [
                'target'      => $phone,
                'message'     => $message,
                'countryCode' => '62',
            ], 15);

            $body = $response->json();

            if (! ($body['status'] ?? false)) {
                $reason = $body['reason'] ?? 'unknown';
                $this->lastError = "Fonnte menolak pengiriman: {$reason}";
                Log::error('WhatsApp: gagal kirim via Fonnte', [
                    'phone'  => $phone,
                    'reason' => $reason,
                ]);
                return false;
            }

            Log::info('WhatsApp: pesan terkirim via Fonnte', ['phone' => $phone]);
            return true;
        } catch (\Throwable $e) {
            $this->lastError = $e->getMessage();
            Log::error('WhatsApp: gagal kirim pesan', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function testSend(string $phone): bool
    {
        return $this->send(
            $phone,
            "Ini adalah pesan uji coba dari ICH Pendidikan.\nJika Anda menerima pesan ini, WhatsApp gateway telah berhasil dikonfigurasi. ✅"
        );
    }

    public function getDeviceStatus(): string
    {
        return $this->getDeviceInfo()['status'];
    }

    /**
     * Ambil status dan informasi detail device dari API Fonnte.
     *
     * @return array{status: string, device: ?string, name: ?string, quota: ?string, expired: ?string, reason: ?string}
     */
    public function getDeviceInfo(): array
    {
        $token = $this->getToken();
        if (empty($token)) {
            return [
                'status'  => 'not_configured',
                'device'  => null,
                'name'    => null,
                'quota'   => null,
                'expired' => null,
                'reason'  => 'Token belum dikonfigurasi',
            ];
        }

        try {
            $response = $this->postToFonnte('/device', [], 10);
            $body = $response->json();

            if (! is_array($body)) {
                return [
                    'status'  => 'error',
                    'device'  => null,
                    'name'    => null,
                    'quota'   => null,
                    'expired' => null,
                    'reason'  => 'Respons tidak valid dari server Fonnte',
                ];
            }

            if (! ($body['status'] ?? false)) {
                return [
                    'status'  => 'invalid_token',
                    'device'  => null,
                    'name'    => null,
                    'quota'   => null,
                    'expired' => null,
                    'reason'  => $body['reason'] ?? 'Token Fonnte tidak valid',
                ];
            }

            $isConnected = ($body['device_status'] ?? '') === 'connect';

            return [
                'status'  => $isConnected ? 'connected' : 'disconnected',
                'device'  => $body['device'] ?? null,
                'name'    => $body['name'] ?? null,
                'quota'   => isset($body['quota']) ? (string) $body['quota'] : null,
                'expired' => $body['expired'] ?? null,
                'reason'  => null,
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp: gagal memeriksa status device Fonnte', [
                'error' => $e->getMessage(),
            ]);

            return [
                'status'  => 'error',
                'device'  => null,
                'name'    => null,
                'quota'   => null,
                'expired' => null,
                'reason'  => $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim request POST ke API Fonnte dengan penanganan otomatis SSL CA bundle
     * agar tahan terhadap misconfig php.ini (cURL error 77/60) di local maupun server deployment.
     */
    private function postToFonnte(string $endpoint, array $payload = [], int $timeout = 15): \Illuminate\Http\Client\Response
    {
        $url = rtrim((string) config('services.fonnte.url', self::DEFAULT_BASE_URL), '/') . $endpoint;
        $verifySsl = $this->shouldVerifySsl();

        try {
            return Http::withHeaders([
                'Authorization' => $this->getToken(),
            ])
                ->withOptions(['verify' => $verifySsl])
                ->timeout($timeout)
                ->post($url, $payload);
        } catch (\Throwable $e) {
            // Jika di server produksi terjadi kegagalan sertifikat CA lokal (cURL error 60 / 77),
            // lakukan fallback otomatis tanpa verifikasi CA file agar layanan tidak lumpuh.
            if ($verifySsl && (str_contains($e->getMessage(), 'cURL error 77') || str_contains($e->getMessage(), 'cURL error 60'))) {
                Log::warning('WhatsApp: fallback SSL verification karena konfigurasi CA bundle bermasalah', [
                    'error' => $e->getMessage(),
                ]);

                return Http::withHeaders([
                    'Authorization' => $this->getToken(),
                ])
                    ->withoutVerifying()
                    ->timeout($timeout)
                    ->post($url, $payload);
            }

            throw $e;
        }
    }

    private function shouldVerifySsl(): bool
    {
        if (app()->isLocal() || ! config('services.fonnte.verify_ssl', true)) {
            return false;
        }

        $caInfo = ini_get('curl.cainfo') ?: ini_get('openssl.cafile');
        if ($caInfo && ! file_exists($caInfo)) {
            return false;
        }

        return true;
    }

    private function getToken(): string
    {
        $dbToken = WhatsAppSetting::getValue('fonnte_token');

        return filled($dbToken)
            ? trim($dbToken)
            : trim((string) config('services.fonnte.token', ''));
    }
}
