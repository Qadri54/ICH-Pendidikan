<section>
    <div class="mb-5">
        <h2 class="text-lg font-display font-bold text-ich-ink-900">Informasi Profil</h2>
        <p class="text-sm text-ich-ink-600 mt-0.5">Perbarui nama dan alamat email akun Anda.</p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                Nama <span class="text-ich-error">*</span>
            </label>
            <input id="name" name="name" type="text"
                   value="{{ old('name', $user->name) }}"
                   required autofocus autocomplete="name"
                   class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                          focus:outline-none focus:border-ich-teal-dark
                          @error('name') border-ich-error @else border-ich-teal @enderror">
            @error('name')
                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                Email <span class="text-ich-error">*</span>
            </label>
            <input id="email" name="email" type="email"
                   value="{{ old('email', $user->email) }}"
                   required autocomplete="username"
                   class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                          focus:outline-none focus:border-ich-teal-dark
                          @error('email') border-ich-error @else border-ich-teal @enderror">
            @error('email')
                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-ich-lg">
                    <p class="text-sm text-yellow-800">
                        Email Anda belum diverifikasi.
                        <button form="send-verification"
                                class="underline font-semibold hover:text-yellow-900">
                            Kirim ulang email verifikasi
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-sm font-medium text-green-700">
                            Link verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <label for="no_hp" class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                No. HP
            </label>
            <input id="no_hp" name="no_hp" type="tel"
                   value="{{ old('no_hp', $user->no_hp) }}"
                   autocomplete="tel"
                   placeholder="Contoh: 08123456789"
                   class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                          focus:outline-none focus:border-ich-teal-dark
                          @error('no_hp') border-ich-error @else border-ich-teal @enderror">
            @error('no_hp')
                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                    class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                           rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm font-semibold text-ich-green">
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
