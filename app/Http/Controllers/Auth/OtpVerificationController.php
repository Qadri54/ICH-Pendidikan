<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    public function __construct(
        private OtpService $otpService
    ) {}

    /**
     * Tampilkan halaman verifikasi kode OTP.
     * Kode OTP tetap dipertahankan saat refresh halaman / pindah halaman selama belum lewat 10 menit.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->verified) {
            return redirect()->intended(route('dashboard'));
        }

        if ($this->otpService->isAccountExpiredForVerification($user)) {
            $user->otp()?->delete();
            $user->role()?->delete();
            $user->delete();

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dihapus karena tidak diverifikasi dalam waktu 30 hari. Silakan daftar kembali.');
        }

        $otp        = $user->otp;
        $wasExpired = $otp && $otp->isExpired();

        // Hanya generate kode OTP baru jika belum pernah ada atau sudah lebih dari 10 menit (expired)
        if ((! $otp || $wasExpired) && $this->otpService->canSendOtp($user)) {
            $otp = $this->otpService->generateAndSend($user, forceNew: true);

            if ($wasExpired && ! session()->has('status')) {
                session()->now(
                    'status',
                    'Kode OTP sebelumnya telah melewati batas 10 menit. Kode OTP baru telah dikirimkan ke WhatsApp Anda.'
                );
            }
        }

        $maskedPhone          = $this->otpService->maskPhone($user->no_hp);
        $cooldownSeconds      = $this->otpService->getRemainingCooldownSeconds($user);
        $expirySeconds        = $this->otpService->getRemainingExpirySeconds($user);
        $remainingDailySends  = $this->otpService->getRemainingDailySends($user);
        $maxDailySends        = OtpService::MAX_SENDS_PER_24_HOURS;
        $resetWaitTime        = $this->otpService->getFormattedResetWaitTime($user);
        $expiresAtIso         = $otp?->expires_at?->toIso8601String();
        $daysRemaining        = max(1, 30 - (int) floor(($user->created_at ?? now())->diffInDays(now())));

        return view('auth.verify-otp', compact(
            'user',
            'maskedPhone',
            'cooldownSeconds',
            'expirySeconds',
            'remainingDailySends',
            'maxDailySends',
            'resetWaitTime',
            'expiresAtIso',
            'daysRemaining'
        ));
    }

    /**
     * Proses verifikasi kode OTP 6 digit yang dimasukkan user.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'digits:6'],
        ], [
            'otp_code.required' => 'Silakan masukkan 6 digit kode OTP.',
            'otp_code.digits'   => 'Kode OTP harus terdiri dari 6 digit angka.',
        ]);

        $user   = $request->user();
        $result = $this->otpService->verifyCode($user, $request->otp_code);

        if (! $result['success']) {
            return back()
                ->withErrors(['otp_code' => $result['message']])
                ->withInput();
        }

        $targetRoute = $user->role?->role_name === 'Orang Tua' ? 'beranda' : 'dashboard';

        return redirect()->route($targetRoute)
            ->with('success', $result['message']);
    }

    /**
     * Generate ulang kode OTP baru dan kirim kembali ke WhatsApp user (maksimal 4x per 24 jam).
     * Mendukung pembaruan nomor WhatsApp jika user salah menginput nomor HP saat registrasi.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->verified) {
            return redirect()->intended(route('dashboard'));
        }

        $phoneUpdated = false;
        if ($request->filled('no_hp') && trim((string) $request->no_hp) !== (string) $user->no_hp) {
            $validated = $request->validate([
                'no_hp' => [
                    'required',
                    'numeric',
                    'digits_between:10,15',
                    Rule::unique('users', 'no_hp')->ignore($user->user_id, 'user_id'),
                    'regex:/^(\+62|0)[0-9]{9,11}$/',
                ],
            ], [
                'no_hp.required'       => 'Nomor WhatsApp wajib diisi.',
                'no_hp.numeric'        => 'Nomor WhatsApp hanya boleh berisi angka.',
                'no_hp.digits_between' => 'Nomor WhatsApp harus terdiri dari 10 hingga 15 digit.',
                'no_hp.unique'         => 'Nomor WhatsApp tersebut sudah digunakan oleh akun lain.',
                'no_hp.regex'          => 'Format nomor WhatsApp tidak valid (gunakan awalan 08 atau +62).',
            ]);

            $user->update(['no_hp' => $validated['no_hp']]);
            $phoneUpdated = true;
        }

        if (! $this->otpService->canSendOtp($user)) {
            $waitTime = $this->otpService->getFormattedResetWaitTime($user);

            return back()->with(
                'error',
                "Batas pengiriman kode OTP maksimal " . OtpService::MAX_SENDS_PER_24_HOURS . " kali dalam 24 jam telah tercapai. Silakan coba kembali setelah {$waitTime}."
            );
        }

        if (! $phoneUpdated) {
            $remaining = $this->otpService->getRemainingCooldownSeconds($user);
            if ($remaining > 0) {
                return back()->with('error', "Mohon tunggu {$remaining} detik sebelum meminta kode OTP baru.");
            }
        }

        // Paksa generate kode OTP baru karena user menekan tombol Kirim Ulang OTP
        $this->otpService->generateAndSend($user, forceNew: true);
        $sisaKuota = $this->otpService->getRemainingDailySends($user);

        $prefixMessage = $phoneUpdated
            ? "Nomor WhatsApp berhasil diperbarui dan kode OTP baru telah dikirimkan."
            : "Kode OTP baru telah dikirimkan ke WhatsApp Anda.";

        return back()->with(
            'status',
            "{$prefixMessage} (Sisa kuota kirim hari ini: {$sisaKuota} dari " . OtpService::MAX_SENDS_PER_24_HOURS . ")"
        );
    }
}
