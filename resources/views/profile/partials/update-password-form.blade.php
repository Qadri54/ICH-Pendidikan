<section>
    <div class="mb-5">
        <h2 class="text-lg font-display font-bold text-ich-ink-900">Perbarui Password</h2>
        <p class="text-sm text-ich-ink-600 mt-0.5">Gunakan password yang panjang dan acak untuk menjaga keamanan akun.</p>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password"
                   class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                Password Saat Ini <span class="text-ich-error">*</span>
            </label>
            <input id="update_password_current_password" name="current_password" type="password"
                   autocomplete="current-password"
                   class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                          focus:outline-none focus:border-ich-teal-dark
                          @error('current_password', 'updatePassword') border-ich-error @else border-ich-teal @enderror">
            @error('current_password', 'updatePassword')
                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password"
                   class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                Password Baru <span class="text-ich-error">*</span>
            </label>
            <input id="update_password_password" name="password" type="password"
                   autocomplete="new-password"
                   class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                          focus:outline-none focus:border-ich-teal-dark
                          @error('password', 'updatePassword') border-ich-error @else border-ich-teal @enderror">
            @error('password', 'updatePassword')
                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation"
                   class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                Konfirmasi Password Baru <span class="text-ich-error">*</span>
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   autocomplete="new-password"
                   class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm
                          focus:outline-none focus:border-ich-teal-dark">
            @error('password_confirmation', 'updatePassword')
                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                    class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                           rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                Simpan Password
            </button>

            @if (session('status') === 'password-updated')
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
