<x-guest-layout>

<div class="relative flex flex-col min-h-screen" x-data="{ show: false }">

    {{-- Hero background --}}
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
         style="background-image: url('{{ asset('images/hero-students.jpg') }}')"></div>

    {{-- Green overlay #51B059 at 55% opacity --}}
    <div class="absolute inset-0" style="background:#51B059;opacity:0.55"></div>

    {{-- Content --}}
    <div class="relative z-10 flex flex-col flex-1 px-5 pt-12 pb-6 gap-4">

        {{-- Logo + heading --}}
        <div class="flex flex-col items-center gap-2.5 mt-5 mb-2">
            <div class="w-[72px] h-[72px] rounded-[18px] bg-white flex items-center justify-center
                        font-display font-extrabold text-[22px] text-ich-green shadow-ich-logo">
                ICH
            </div>
            <div class="font-special font-bold text-[32px] text-white leading-tight"
                 style="text-shadow:0 4px 4px rgba(0,0,0,0.25)">
                Masuk
            </div>
            <div class="font-sans text-[12px] text-white text-center" style="opacity:.95">
                Selamat datang kembali di IQRA' Creative House
            </div>
        </div>

        {{-- Session flash --}}
        <x-auth-session-status class="text-white text-[12px] text-center" :status="session('status')"/>

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
            @csrf

            {{-- Email --}}
            <div class="flex flex-col gap-1.5">
                <label for="email"
                       class="font-ui font-bold text-[12px] text-white"
                       style="text-shadow:0 2px 4px rgba(0,0,0,0.4)">
                    Email
                </label>
                <div class="relative h-[46px] bg-white border-2 border-ich-teal rounded-ich-lg
                            flex items-center px-3.5 gap-2.5 shadow-ich-lift
                            {{ $errors->has('email') ? 'border-ich-error' : 'border-ich-teal' }}">
                    {{-- User icon --}}
                    <svg class="w-[22px] h-[22px] shrink-0 {{ $errors->has('email') ? 'text-ich-error' : 'text-ich-teal' }}"
                         fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0 2c-5.33 0-8 2.67-8 4v1h16v-1c0-1.33-2.67-4-8-4z"/>
                    </svg>
                    <input id="email" name="email" type="email"
                           value="{{ old('email') }}"
                           placeholder="Masukkan email..."
                           required autofocus autocomplete="username"
                           class="flex-1 border-0 outline-none ring-0 bg-transparent
                                  font-sans font-semibold text-[13px] text-ich-ink-900
                                  placeholder:text-ich-ink-300 focus:outline-none focus:ring-0">
                </div>
                @error('email')
                    <p class="font-sans text-[11px] text-red-200">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="flex flex-col gap-1.5">
                <label for="password"
                       class="font-ui font-bold text-[12px] text-white"
                       style="text-shadow:0 2px 4px rgba(0,0,0,0.4)">
                    Password
                </label>
                <div class="relative h-[46px] bg-white border-2 border-ich-teal rounded-ich-lg
                            flex items-center px-3.5 gap-2.5 shadow-ich-lift
                            {{ $errors->has('password') ? 'border-ich-error' : 'border-ich-teal' }}">
                    {{-- Lock icon --}}
                    <svg class="w-[22px] h-[22px] shrink-0 {{ $errors->has('password') ? 'text-ich-error' : 'text-ich-teal' }}"
                         fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="5" y="11" width="14" height="10" rx="2" stroke-linejoin="round"/>
                        <path stroke-linecap="round" d="M8 11V7a4 4 0 0 1 8 0v4"/>
                        <circle cx="12" cy="16" r="1.5" fill="currentColor" stroke="none"/>
                    </svg>
                    <input id="password" name="password"
                           :type="show ? 'text' : 'password'"
                           placeholder="Password"
                           required autocomplete="current-password"
                           class="flex-1 border-0 outline-none ring-0 bg-transparent
                                  font-sans font-semibold text-[13px] text-ich-ink-900
                                  placeholder:text-ich-ink-300 focus:outline-none focus:ring-0">
                    {{-- Eye toggle --}}
                    <button type="button" @click="show = !show"
                            class="shrink-0 bg-transparent border-none cursor-pointer p-0 text-ich-teal">
                        <svg x-show="!show" x-cloak class="w-[18px] h-[18px]"
                             fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg x-show="show" x-cloak class="w-[18px] h-[18px]"
                             fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94
                                     M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19
                                     m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="font-sans text-[11px] text-red-200">{{ $message }}</p>
                @enderror
            </div>

            {{-- Show password + lupa password --}}
            <div class="flex items-center justify-between mx-0.5">
                <label class="flex items-center gap-2 cursor-pointer" @click="show = !show">
                    <span class="w-[18px] h-[18px] rounded-[5px] bg-ich-teal flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                             stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span class="font-sans font-semibold text-[12px] text-white">
                        Perlihatkan Password
                    </span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="font-sans font-bold text-[12px] text-white no-underline"
                       style="opacity:.9">
                        Lupa Password?
                    </a>
                @endif
            </div>

            {{-- Tombol Masuk --}}
            <button type="submit"
                    class="mt-1 w-full h-[46px] bg-ich-teal text-white font-sans font-bold text-[14px]
                           rounded-ich-lg border-none cursor-pointer flex items-center justify-center
                           shadow-ich-btn hover:bg-ich-teal-dark transition-colors">
                Masuk
            </button>

        </form>

        {{-- Belum punya akun --}}
        <div class="text-center font-sans text-[12px] text-white mt-1">Belum Punya Akun?</div>

        <a href="{{ route('register') }}"
           class="w-full h-[46px] bg-ich-yellow text-white font-sans font-bold text-[14px]
                  rounded-ich-lg flex items-center justify-center no-underline
                  shadow-ich-btn hover:bg-ich-yellow-dark transition-colors">
            Buat Akun
        </a>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Copyright --}}
        <div class="text-center font-sans text-[10px] text-white py-3" style="opacity:.9">
            Copyright &copy; {{ date('Y') }} IQRA' CREATIVE GROUP. All Rights Reserved.
        </div>

    </div>
</div>

</x-guest-layout>
