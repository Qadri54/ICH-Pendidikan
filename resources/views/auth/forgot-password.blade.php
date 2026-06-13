<x-guest-layout>

<div class="relative flex flex-col min-h-screen lg:min-h-0 lg:static">

    {{-- Mobile-only: hero photo background --}}
    <div class="lg:hidden absolute inset-0 bg-cover bg-center bg-no-repeat"
         style="background-image: url('{{ asset('images/hero-students.jpg') }}')"></div>
    <div class="lg:hidden absolute inset-0" style="background: var(--ich-green-soft); opacity: 0.45"></div>

    {{-- Content container --}}
    <div class="relative z-10 lg:static flex flex-col flex-1 lg:flex-none
                px-5 pt-5 pb-6 lg:px-0 lg:pt-0 lg:pb-0 gap-4">

        {{-- Mobile header: back button + title --}}
        <div class="lg:hidden flex items-center gap-3 mt-2">
            <a href="{{ route('login') }}"
               class="w-9 h-9 rounded-[10px] flex items-center justify-center no-underline flex-shrink-0"
               style="background:rgba(255,255,255,0.2)">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                     stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="font-special font-bold text-[28px] text-white leading-tight"
                 style="text-shadow:0 4px 4px rgba(0,0,0,0.25)">
                Lupa Password
            </div>
        </div>
        <div class="lg:hidden font-sans text-[12px] text-white mb-1" style="opacity:.95">
            Masukkan email untuk menerima link reset password
        </div>

        {{-- Desktop header --}}
        <div class="hidden lg:block mb-2">
            <h2 class="font-special font-bold text-[28px] text-ich-ink-900 leading-tight">Lupa Password</h2>
            <p class="font-sans text-[13px] text-ich-ink-400 mt-1">
                Masukkan email untuk menerima link reset password
            </p>
        </div>

        {{-- Flash status --}}
        <x-auth-session-status
            class="text-white lg:text-ich-ink-600 text-[12px] text-center"
            :status="session('status')"/>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
            @csrf

            {{-- Email --}}
            <div class="flex flex-col gap-1.5">
                <label for="email"
                       class="font-ui font-bold text-[12px] text-white lg:text-ich-ink-600 mobile-label-glow">
                    Email
                </label>
                <div class="h-[46px] bg-white border-2 rounded-ich-lg flex items-center px-3.5 gap-2.5 shadow-ich-lift
                            {{ $errors->has('email') ? 'border-ich-error' : 'border-ich-teal' }}">
                    <svg class="w-[22px] h-[22px] shrink-0 {{ $errors->has('email') ? 'text-ich-error' : 'text-ich-teal' }}"
                         fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8
                                 M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/>
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
                    <p class="font-sans text-[11px] text-red-200 lg:text-ich-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Kirim --}}
            <button type="submit"
                    class="mt-1 w-full h-[46px] bg-ich-teal text-white font-sans font-bold text-[14px]
                           rounded-ich-lg border-none cursor-pointer flex items-center justify-center
                           shadow-ich-btn hover:bg-ich-teal-dark transition-colors">
                Kirim Link Reset
            </button>

        </form>

        {{-- Link ke login --}}
        <div class="text-center font-sans text-[12px] text-white lg:text-ich-ink-500 mt-1">
            Ingat password Anda?
            <a href="{{ route('login') }}"
               class="font-bold text-white lg:text-ich-teal underline">
                Masuk
            </a>
        </div>

        {{-- Mobile-only: spacer + copyright --}}
        <div class="lg:hidden flex-1"></div>
        <div class="lg:hidden text-center font-sans text-[10px] text-white py-3" style="opacity:.9">
            Copyright &copy; {{ date('Y') }} IQRA' CREATIVE GROUP. All Rights Reserved.
        </div>

    </div>
</div>

</x-guest-layout>
