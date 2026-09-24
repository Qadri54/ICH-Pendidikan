<?php

namespace App\Services\Auth;

use App\Models\Otp;
use App\Models\User;
use App\Services\WhatsApp\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public const EXPIRY_MINUTES = 10;
    public const RESEND_COOLDOWN_SECONDS = 60;
    public const MAX_SENDS_PER_24_HOURS = 4;
    public const SEND_WINDOW_HOURS = 24;
    public const UNVERIFIED_MAX_DAYS = 30;

    public function __construct(
        private WhatsAppService $whatsAppService
    ) {}

    /**
     * Cek apakah user masih memiliki kuota pengiriman OTP dalam jendela 24 jam (maksimal 4 kali).
     */
    public function canSendOtp(User $user): bool
    {
        return $this->getRemainingDailySends($user) > 0;
    }

    /**
     * Hitung sisa kuota pengiriman OTP user dalam periode 24 jam berjalan (0 s.d. 4).
     */
    public function getRemainingDailySends(User $user): int
    {
        $otp = Otp::where('user_id', $user->user_id)->first();

        if (! $otp || ! $otp->send_window_started_at) {
            return self::MAX_SENDS_PER_24_HOURS;
        }

        // Jika sudah lewat 24 jam sejak pengiriman pertama pada window ini, kuota kembali penuh (4)
        if ($otp->send_window_started_at->lte(now()->subHours(self::SEND_WINDOW_HOURS))) {
            return self::MAX_SENDS_PER_24_HOURS;
        }

        return max(0, self::MAX_SENDS_PER_24_HOURS - (int) $otp->send_count);
    }

    /**
     * Waktu berakhirnya pembatasan 24 jam (jika kuota 4x sudah habis).
     */
    public function getDailyLimitResetAt(User $user): ?Carbon
    {
        $otp = Otp::where('user_id', $user->user_id)->first();

        if (! $otp || ! $otp->send_window_started_at) {
            return null;
        }

        $resetAt = $otp->send_window_started_at->copy()->addHours(self::SEND_WINDOW_HOURS);

        return $resetAt->isFuture() ? $resetAt : null;
    }

    /**
     * Format sisa waktu tunggu 24 jam secara human-readable (misal: "23 jam 45 menit").
     */
    public function getFormattedResetWaitTime(User $user): string
    {
        $resetAt = $this->getDailyLimitResetAt($user);
        if (! $resetAt) {
            return 'beberapa saat';
        }

        $totalMinutes = max(1, (int) ceil(now()->diffInSeconds($resetAt) / 60));
        $hours        = intdiv($totalMinutes, 60);
        $minutes      = $totalMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} jam {$minutes} menit";
        }

        if ($hours > 0) {
            return "{$hours} jam";
        }

        return "{$minutes} menit";
    }

    /**
     * Cek apakah user memiliki kode OTP yang masih aktif (belum lewat 10 menit / belum expired).
     */
    public function hasActiveOtp(User $user): bool
    {
        $otp = Otp::where('user_id', $user->user_id)->first();

        return $otp !== null && ! $otp->isExpired();
    }

    /**
     * Hitung sisa detik masa berlaku kode OTP (dari durasi 10 menit).
     */
    public function getRemainingExpirySeconds(User $user): int
    {
        $otp = Otp::where('user_id', $user->user_id)->first();
        if (! $otp || ! $otp->expires_at || $otp->isExpired()) {
            return 0;
        }

        return max(0, (int) ceil(now()->diffInSeconds($otp->expires_at, false)));
    }

    /**
     * Format sisa masa berlaku kode OTP (10 menit) secara human-readable.
     */
    public function getFormattedExpiryRemaining(User $user): string
    {
        $seconds = $this->getRemainingExpirySeconds($user);
        if ($seconds <= 0) {
            return '0 menit';
        }

        $minutes    = intdiv($seconds, 60);
        $remSeconds = $seconds % 60;

        if ($minutes > 0 && $remSeconds > 0) {
            return "{$minutes} menit {$remSeconds} detik";
        }

        if ($minutes > 0) {
            return "{$minutes} menit";
        }

        return "{$remSeconds} detik";
    }

    /**
     * Buat atau perbarui kode OTP 6 digit di tabel `otps` untuk user terkait
     * dan kirimkan melalui WhatsApp Gateway (Fonnte) jika kuota 4x/24 jam masih tersedia.
     *
     * Jika $forceNew = false dan kode OTP masih aktif (belum 10 menit), kode OTP lama
     * akan dipertahankan tanpa mengurangi kuota Fonnte.
     */
    public function generateAndSend(User $user, bool $forceNew = false): ?Otp
    {
        $existingOtp = Otp::where('user_id', $user->user_id)->first();

        // Pertahankan kode OTP yang masih aktif (< 10 menit) jika bukan request kirim ulang paksa
        if (! $forceNew && $existingOtp && ! $existingOtp->isExpired()) {
            return $existingOtp;
        }

        // Cek apakah jendela 24 jam masih aktif atau sudah reset
        $isWindowExpired = ! $existingOtp
            || ! $existingOtp->send_window_started_at
            || $existingOtp->send_window_started_at->lte(now()->subHours(self::SEND_WINDOW_HOURS));

        if (! $isWindowExpired && (int) $existingOtp->send_count >= self::MAX_SENDS_PER_24_HOURS) {
            Log::warning('OTP: batas pengiriman 4x dalam 24 jam tercapai untuk user', [
                'user_id'    => $user->user_id,
                'send_count' => $existingOtp->send_count,
            ]);

            return null;
        }

        $newSendCount    = $isWindowExpired ? 1 : ((int) $existingOtp->send_count + 1);
        $windowStartedAt = $isWindowExpired ? now() : $existingOtp->send_window_started_at;
        $code            = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $otp = Otp::updateOrCreate(
            ['user_id' => $user->user_id],
            [
                'otp_code'               => $code,
                'send_count'             => $newSendCount,
                'send_window_started_at' => $windowStartedAt,
                'expires_at'             => now()->addMinutes(self::EXPIRY_MINUTES),
            ]
        );

        // Pastikan timestamp updated_at diperbarui untuk perhitungan cooldown 60 detik
        $otp->touch();

        $remainingSends = max(0, self::MAX_SENDS_PER_24_HOURS - $newSendCount);

        $message = "*[IQRA' Creative House]*\n\n"
            . "Halo *{$user->name}*,\n"
            . "Kode verifikasi OTP akun Anda adalah:\n\n"
            . "*{$code}*\n\n"
            . "Kode ini berlaku selama " . self::EXPIRY_MINUTES . " menit.\n"
            . "Kuota pengiriman OTP hari ini: {$newSendCount}/" . self::MAX_SENDS_PER_24_HOURS . " (sisa {$remainingSends}x dalam 24 jam).\n"
            . "Jangan berikan kode ini kepada siapa pun demi keamanan akun Anda.";

        if (! empty($user->no_hp)) {
            $sent = $this->whatsAppService->send($user->no_hp, $message);
            if (! $sent) {
                Log::warning('OTP WhatsApp gagal dikirim ke user', [
                    'user_id' => $user->user_id,
                    'no_hp'   => $user->no_hp,
                    'error'   => $this->whatsAppService->getLastError(),
                ]);
            }
        }

        return $otp;
    }

    /**
     * Verifikasi kode OTP yang diinput oleh user.
     *
     * @return array{success: bool, message: string}
     */
    public function verifyCode(User $user, string $inputCode): array
    {
        $otp = Otp::where('user_id', $user->user_id)->first();

        if (! $otp) {
            return [
                'success' => false,
                'message' => 'Kode OTP tidak ditemukan. Silakan klik tombol Kirim Ulang Kode.',
            ];
        }

        if ($otp->isExpired()) {
            return [
                'success' => false,
                'message' => 'Kode OTP telah kedaluwarsa. Silakan klik tombol Kirim Ulang Kode untuk mendapatkan kode baru.',
            ];
        }

        if (! hash_equals($otp->otp_code, trim($inputCode))) {
            return [
                'success' => false,
                'message' => 'Kode OTP yang Anda masukkan tidak sesuai.',
            ];
        }

        $user->update([
            'verified'          => true,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        $otp->delete();

        return [
            'success' => true,
            'message' => 'Akun Anda berhasil diverifikasi! Selamat datang di IQRA\' Creative House.',
        ];
    }

    /**
     * Hitung sisa detik cooldown sebelum user dapat meminta kirim ulang OTP.
     */
    public function getRemainingCooldownSeconds(User $user): int
    {
        $otp = Otp::where('user_id', $user->user_id)->first();
        if (! $otp || ! $otp->updated_at) {
            return 0;
        }

        $availableAt = $otp->updated_at->copy()->addSeconds(self::RESEND_COOLDOWN_SECONDS);
        if (now()->greaterThanOrEqualTo($availableAt)) {
            return 0;
        }

        return (int) ceil(now()->diffInSeconds($availableAt, false));
    }

    /**
     * Cek apakah user yang belum terverifikasi sudah melewati batas 30 hari sejak created_at.
     */
    public function isAccountExpiredForVerification(User $user): bool
    {
        if ($user->verified) {
            return false;
        }

        if (! $user->created_at) {
            return false;
        }

        return $user->created_at->lte(now()->subDays(self::UNVERIFIED_MAX_DAYS));
    }

    /**
     * Hapus semua akun yang belum terverifikasi (verified = false) dan telah melewati 30 hari sejak created_at.
     */
    public function pruneExpiredUnverifiedUsers(): int
    {
        $cutoff = now()->subDays(self::UNVERIFIED_MAX_DAYS);

        $expiredUsers = User::where('verified', false)
            ->where('created_at', '<=', $cutoff)
            ->get();

        $deletedCount = 0;

        foreach ($expiredUsers as $user) {
            $user->otp()?->delete();
            $user->role()?->delete();
            $user->delete();
            $deletedCount++;
        }

        if ($deletedCount > 0) {
            Log::info("OTP Cleanup: {$deletedCount} akun belum terverifikasi (> 30 hari) telah dihapus.");
        }

        return $deletedCount;
    }

    /**
     * Samarkan nomor HP untuk ditampilkan di halaman verifikasi OTP (misal: 0821****0940).
     */
    public function maskPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '-';
        }

        $len = strlen($phone);
        if ($len <= 7) {
            return $phone;
        }

        return substr($phone, 0, 4) . str_repeat('*', max(3, $len - 8)) . substr($phone, -4);
    }
}
