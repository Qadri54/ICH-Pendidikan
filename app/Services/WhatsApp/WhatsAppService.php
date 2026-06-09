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

        try {
            $baseUrl = $this->getGatewayUrl();

            $response = Http::timeout(15)->post("{$baseUrl}/api/send", [
                'number'  => $phone,
                'message' => $message,
            ]);

            if ($response->failed()) {
                Log::error('WhatsApp: gagal kirim', [
                    'phone'  => $phone,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            Log::info('WhatsApp: pesan terkirim', ['phone' => $phone]);
            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp: gagal kirim pesan', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getSessionStatus(): array
    {
        $baseUrl = $this->getGatewayUrl();

        try {
            $response = Http::timeout(5)->get("{$baseUrl}/api/status");
            return $response->json() ?? ['status' => 'unknown'];
        } catch (\Throwable) {
            return ['status' => 'disconnected'];
        }
    }

    public function getQrCode(): ?string
    {
        $baseUrl = $this->getGatewayUrl();

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

    private function getGatewayUrl(): string
    {
        return WhatsAppSetting::getValue('self_hosted_url')
            ?? config('services.whatsapp.gateway_url', 'http://localhost:3000');
    }
}
