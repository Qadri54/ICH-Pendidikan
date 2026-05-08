<x-guest-layout>

{{--
  Mobile  (< lg): full-screen hero bg + green overlay, white text labels
  Desktop (≥ lg): no bg, dark text labels — the guest layout provides the white right panel
--}}
<div class="relative flex flex-col min-h-screen lg:min-h-0 lg:static">

    {{-- Mobile-only: hero photo background --}}
    <div class="lg:hidden absolute inset-0 bg-cover bg-center bg-no-repeat"
         style="background-image: url('{{ asset('images/hero-students.jpg') }}')"></div>
    {{-- Mobile-only: #51B059 green overlay at 60% --}}
    <div class="lg:hidden absolute inset-0" style="background:#51B059;opacity:0.6"></div>

    {{-- Content container --}}
    <div class="relative z-10 lg:static flex flex-col flex-1 lg:flex-none
                px-5 pt-5 pb-6 lg:px-0 lg:pt-0 lg:pb-0 gap-3.5 overflow-y-auto">

        {{-- Mobile header: back button + Daftar title --}}
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
                Daftar
            </div>
        </div>
        <div class="lg:hidden font-sans text-[12px] text-white mb-1" style="opacity:.95">
            Buat akun baru untuk orang tua siswa
        </div>

        {{-- Desktop header --}}
        <div class="hidden lg:block mb-2">
            <h2 class="font-special font-bold text-[28px] text-ich-ink-900 leading-tight">Daftar</h2>
            <p class="font-sans text-[13px] text-ich-ink-400 mt-1">
                Buat akun baru untuk orang tua siswa
            </p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-3.5">
            @csrf

            {{-- Nama --}}
            <div class="flex flex-col gap-1.5">
                <label for="name"
                       class="font-ui font-bold text-[12px] text-white lg:text-ich-ink-600 mobile-label-glow">
                    Nama
                </label>
                <div class="h-[46px] bg-white border-2 rounded-ich-lg flex items-center px-3.5 gap-2.5 shadow-ich-lift
                            {{ $errors->has('name') ? 'border-ich-error' : 'border-ich-teal' }}">
                    <svg class="w-[22px] h-[22px] shrink-0 {{ $errors->has('name') ? 'text-ich-error' : 'text-ich-teal' }}"
                         fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0 2c-5.33 0-8 2.67-8 4v1h16v-1c0-1.33-2.67-4-8-4z"/>
                    </svg>
                    <input id="name" name="name" type="text"
                           value="{{ old('name') }}"
                           placeholder="Masukkan nama..."
                           required autofocus autocomplete="name"
                           class="flex-1 border-0 outline-none ring-0 bg-transparent
                                  font-sans font-semibold text-[13px] text-ich-ink-900
                                  placeholder:text-ich-ink-300 focus:outline-none focus:ring-0">
                </div>
                @error('name')
                    <p class="font-sans text-[11px] text-red-200 lg:text-ich-error">{{ $message }}</p>
                @enderror
            </div>

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
                           required autocomplete="username"
                           class="flex-1 border-0 outline-none ring-0 bg-transparent
                                  font-sans font-semibold text-[13px] text-ich-ink-900
                                  placeholder:text-ich-ink-300 focus:outline-none focus:ring-0">
                </div>
                @error('email')
                    <p class="font-sans text-[11px] text-red-200 lg:text-ich-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- No HP --}}
            <div class="flex flex-col gap-1.5">
                <label for="no_hp"
                       class="font-ui font-bold text-[12px] text-white lg:text-ich-ink-600 mobile-label-glow">
                    No Hp
                </label>
                <div class="h-[46px] bg-white border-2 rounded-ich-lg flex items-center px-3.5 gap-2.5 shadow-ich-lift
                            {{ $errors->has('no_hp') ? 'border-ich-error' : 'border-ich-teal' }}">
                    <svg class="w-[22px] h-[22px] shrink-0 {{ $errors->has('no_hp') ? 'text-ich-error' : 'text-ich-teal' }}"
                         fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 5a2 2 0 0 1 2-2h3.28a1 1 0 0 1 .95.68l1.45 4.35a1 1 0 0 1-.23 1.02
                                 l-1.88 1.88a16 16 0 0 0 6.68 6.68l1.88-1.88a1 1 0 0 1 1.02-.23
                                 l4.35 1.45a1 1 0 0 1 .68.95V19a2 2 0 0 1-2 2A18 18 0 0 1 3 5z"/>
                    </svg>
                    <input id="no_hp" name="no_hp" type="tel"
                           value="{{ old('no_hp') }}"
                           placeholder="Masukkan No Hp..."
                           required
                           class="flex-1 border-0 outline-none ring-0 bg-transparent
                                  font-sans font-semibold text-[13px] text-ich-ink-900
                                  placeholder:text-ich-ink-300 focus:outline-none focus:ring-0">
                </div>
                @error('no_hp')
                    <p class="font-sans text-[11px] text-red-200 lg:text-ich-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="flex flex-col gap-1.5">
                <label for="password"
                       class="font-ui font-bold text-[12px] text-white lg:text-ich-ink-600 mobile-label-glow">
                    Password
                </label>
                <div class="h-[46px] bg-white border-2 rounded-ich-lg flex items-center px-3.5 gap-2.5 shadow-ich-lift
                            {{ $errors->has('password') ? 'border-ich-error' : 'border-ich-teal' }}">
                    <svg class="w-[22px] h-[22px] shrink-0 {{ $errors->has('password') ? 'text-ich-error' : 'text-ich-teal' }}"
                         fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="5" y="11" width="14" height="10" rx="2" stroke-linejoin="round"/>
                        <path stroke-linecap="round" d="M8 11V7a4 4 0 0 1 8 0v4"/>
                        <circle cx="12" cy="16" r="1.5" fill="currentColor" stroke="none"/>
                    </svg>
                    <input id="password" name="password" type="password"
                           placeholder="Masukkan password..."
                           required autocomplete="new-password"
                           class="flex-1 border-0 outline-none ring-0 bg-transparent
                                  font-sans font-semibold text-[13px] text-ich-ink-900
                                  placeholder:text-ich-ink-300 focus:outline-none focus:ring-0">
                </div>
                @error('password')
                    <p class="font-sans text-[11px] text-red-200 lg:text-ich-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div class="flex flex-col gap-1.5">
                <label for="password_confirmation"
                       class="font-ui font-bold text-[12px] text-white lg:text-ich-ink-600 mobile-label-glow">
                    Konfirmasi Password
                </label>
                <div class="h-[46px] bg-white border-2 rounded-ich-lg flex items-center px-3.5 gap-2.5 shadow-ich-lift
                            {{ $errors->has('password_confirmation') ? 'border-ich-error' : 'border-ich-teal' }}">
                    <svg class="w-[22px] h-[22px] shrink-0 text-ich-teal"
                         fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="5" y="11" width="14" height="10" rx="2" stroke-linejoin="round"/>
                        <path stroke-linecap="round" d="M8 11V7a4 4 0 0 1 8 0v4"/>
                        <circle cx="12" cy="16" r="1.5" fill="currentColor" stroke="none"/>
                    </svg>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           placeholder="Konfirmasi password..."
                           required autocomplete="new-password"
                           class="flex-1 border-0 outline-none ring-0 bg-transparent
                                  font-sans font-semibold text-[13px] text-ich-ink-900
                                  placeholder:text-ich-ink-300 focus:outline-none focus:ring-0">
                </div>
                @error('password_confirmation')
                    <p class="font-sans text-[11px] text-red-200 lg:text-ich-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Buat Akun --}}
            <button type="submit"
                    class="mt-1.5 w-full h-[46px] bg-ich-yellow text-white font-sans font-bold text-[14px]
                           rounded-ich-lg border-none cursor-pointer flex items-center justify-center
                           shadow-ich-btn hover:bg-ich-yellow-dark transition-colors">
                Buat Akun
            </button>

        </form>

        {{-- Link ke login --}}
        <div class="text-center font-sans text-[12px] text-white lg:text-ich-ink-500 mt-0.5">
            Sudah Punya Akun?
            <a href="{{ route('login') }}"
               class="font-bold text-white lg:text-ich-teal underline">
                Masuk
            </a>
        </div>

        {{-- Mobile-only: spacer + copyright --}}
        <div class="lg:hidden flex-1"></div>
        <div class="lg:hidden text-center font-sans text-[10px] text-white py-2" style="opacity:.9">
            Copyright &copy; {{ date('Y') }} IQRA' CREATIVE GROUP.
        </div>

    </div>
</div>

</x-guest-layout>
