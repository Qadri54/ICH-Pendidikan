<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller {
    public function __construct(
        private OtpService $otpService
    ) {}

    /**
     * Display the registration view.
     */
    public function create(): View {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_hp' => [
                'required',
                'numeric',
                'digits_between:10,15',
                'unique:users,no_hp',
                'regex:/^(\+62|0)[0-9]{9,11}$/',  // Format Indonesia
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'no_hp'    => $request->no_hp,
            'verified' => false,
        ]);

        // Role disimpan di tabel roles dengan FK user_id (hasOne)
        $user->role()->create(['role_name' => 'Orang Tua']);

        event(new Registered($user));

        Auth::login($user);

        // Generate kode OTP di tabel otps dan kirim ke WhatsApp user
        $this->otpService->generateAndSend($user);

        return redirect()->route('otp.notice')
            ->with('status', 'Kode verifikasi OTP telah dikirimkan ke nomor WhatsApp Anda.');
    }
}
