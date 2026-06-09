<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppSetting;
use App\Support\PhoneFormatter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
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

        $driver = $this->getDriver();

        try {
            return match ($driver) {
                'fonnte'      => $this->sendViaFonnte($phone, $message),
                'self-hosted' => $this->sendViaSelfHosted($phone, $message),
                default       => false,
            };
        } catch (\Throwable $e) {
            Log::error('WhatsApp: gagal kirim pesan', [
                'driver' => $driver,
                'phone'  => $phone,
                'error'  => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getSessionStatus(): array
    {
        $baseUrl = $this->getSelfHostedUrl();

        try {
            $response = Http::timeout(5)->get("{$baseUrl}/api/status");
            return $response->json() ?? ['status' => 'unknown'];
        } catch (\Throwable) {
            return ['status' => 'disconnected'];
        }
    }

    public function getQrCode(): ?string
    {
        $baseUrl = $this->getSelfHostedUrl();

        try {
            $response = Http::timeout(10)->get("{$baseUrl}/api/qr");
            return $response->json('qr') ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function testSend(string $phone): bool
    {
        return $this->send($phone, "Ini adalah pesan uji coba dari ICH Pendidikan.\nJika Anda menerima pesan ini, WhatsApp gateway telah berhasil dikonfigurasi. ✅");
    }

    private function getDriver(): string
    {
        return WhatsAppSetting::getValue('whatsapp_driver')
            ?? config('services.whatsapp.driver', 'fonnte');
    }

    private function getSelfHostedUrl(): string
    {
        return WhatsAppSetting::getValue('self_hosted_url')
            ?? config('services.whatsapp.self_hosted.url', 'http://localhost:3000');
    }

    private function sendViaFonnte(string $phone, string $message): bool
    {
        $token = WhatsAppSetting::getValue('fonnte_token')
            ?? config('services.whatsapp.fonnte.token');

        if (empty($token)) {
            Log::warning('WhatsApp Fonnte: token belum dikonfigurasi.');
            return false;
        }

        $url = config('services.whatsapp.fonnte.url', 'https://api.fonnte.com/send');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post($url, [
            'target'  => $phone,
            'message' => $message,
        ]);

        if ($response->failed()) {
            Log::error('WhatsApp Fonnte: gagal kirim', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        }

        Log::info('WhatsApp Fonnte: pesan terkirim', ['phone' => $phone]);
        return true;
    }

    private function sendViaSelfHosted(string $phone, string $message): bool
    {
        $baseUrl = $this->getSelfHostedUrl();

        $response = Http::timeout(15)->post("{$baseUrl}/api/send", [
            'number'  => $phone,
            'message' => $message,
        ]);

        if ($response->failed()) {
            Log::error('WhatsApp self-hosted: gagal kirim', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        }

        Log::info('WhatsApp self-hosted: pesan terkirim', ['phone' => $phone]);
        return true;
    }
}
