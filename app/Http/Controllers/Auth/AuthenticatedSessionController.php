<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        private OtpService $otpService
    ) {}

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user && ! $user->verified) {
            // Jika akun tidak diverifikasi dalam 30 hari sejak created_at, hapus akun
            if ($this->otpService->isAccountExpiredForVerification($user)) {
                $user->otp()?->delete();
                $user->role()?->delete();
                $user->delete();

                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Akun Anda telah dihapus karena tidak diverifikasi dalam waktu 30 hari sejak pendaftaran. Silakan buat akun baru.');
            }

            // Jika kode OTP sebelumnya masih aktif (belum lewat 10 menit), pertahankan kode tersebut
            // tanpa men-generate ulang agar hemat kuota Fonnte
            if ($this->otpService->hasActiveOtp($user)) {
                $sisaWaktu = $this->otpService->getFormattedExpiryRemaining($user);

                return redirect()->route('otp.notice')
                    ->with('status', "Akun Anda belum terverifikasi. Kode OTP Anda sebelumnya masih berlaku ({$sisaWaktu}) — silakan masukkan kode tersebut.");
            }

            // Jika kode OTP belum ada atau sudah lewat 10 menit, generate ulang jika kuota 4x/24 jam masih tersedia
            if ($this->otpService->canSendOtp($user)) {
                $this->otpService->generateAndSend($user);
                $sisaKuota = $this->otpService->getRemainingDailySends($user);

                return redirect()->route('otp.notice')
                    ->with('status', "Akun Anda belum terverifikasi. Kode OTP baru telah dikirimkan ke WhatsApp Anda (Sisa kuota kirim hari ini: {$sisaKuota} dari " . OtpService::MAX_SENDS_PER_24_HOURS . ").");
            }

            $waitTime = $this->otpService->getFormattedResetWaitTime($user);

            return redirect()->route('otp.notice')
                ->with('error', "Akun Anda belum terverifikasi. Batas pengiriman OTP (maksimal " . OtpService::MAX_SENDS_PER_24_HOURS . " kali dalam 24 jam) telah tercapai. Silakan tunggu {$waitTime}.");
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
