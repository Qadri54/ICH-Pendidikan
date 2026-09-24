<?php

namespace App\Http\Middleware;

use App\Services\Auth\OtpService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    public function __construct(
        private OtpService $otpService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user->verified) {
            return $next($request);
        }

        // Jika akun belum diverifikasi lebih dari 30 hari sejak created_at, hapus akun
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

        // Izinkan akses ke halaman verifikasi OTP dan proses logout
        if ($request->routeIs('otp.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('otp.notice');
    }
}
