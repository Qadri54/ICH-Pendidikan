<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppSetting;
use App\Support\PhoneFormatter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private const BASE_URL = 'https://api.fonnte.com';

    public function isEnabled(): bool
    {
        $dbValue = WhatsAppSetting::getValue('whatsapp_enabled');

        return filter_var(
            $dbValue ?? config('services.whatsapp.enabled'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    public function send(string $phone, string $message): bool
    {
        $phone = PhoneFormatter::toInternational($phone);
        if (! $phone) {
            Log::warning('WhatsApp: nomor HP kosong, pesan tidak dikirim.');
            return false;
        }

        $token = $this->getToken();
        if (empty($token)) {
            Log::error('WhatsApp: FONNTE_TOKEN belum dikonfigurasi.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(15)->post(self::BASE_URL . '/send', [
                'target'      => $phone,
                'message'     => $message,
                'countryCode' => '62',
            ]);

            $body = $response->json();

            if (! ($body['status'] ?? false)) {
                Log::error('WhatsApp: gagal kirim via Fonnte', [
                    'phone'  => $phone,
                    'reason' => $body['reason'] ?? 'unknown',
                ]);
                return false;
            }

            Log::info('WhatsApp: pesan terkirim via Fonnte', ['phone' => $phone]);
            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp: gagal kirim pesan', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function testSend(string $phone): bool
    {
        return $this->send($phone, "Ini adalah pesan uji coba dari ICH Pendidikan.\nJika Anda menerima pesan ini, WhatsApp gateway telah berhasil dikonfigurasi. ✅");
    }

    public function getDeviceStatus(): string
    {
        $token = $this->getToken();
        if (empty($token)) {
            return 'not_configured';
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(5)->post(self::BASE_URL . '/device');

            $body = $response->json();

            return ($body['status'] ?? false) ? 'connected' : 'disconnected';
        } catch (\Throwable) {
            return 'error';
        }
    }

    private function getToken(): string
    {
        return WhatsAppSetting::getValue('fonnte_token')
            ?? config('services.fonnte.token', '');
    }
}
