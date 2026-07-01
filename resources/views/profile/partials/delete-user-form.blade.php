<section>
    <div class="mb-5">
        <h2 class="text-lg font-display font-bold text-ich-ink-900">Hapus Akun</h2>
        <p class="text-sm text-ich-ink-600 mt-0.5">
            Akun yang dihapus tidak dapat dipulihkan. Pastikan Anda sudah mengunduh data penting sebelum melanjutkan.
        </p>
    </div>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-6 py-2.5 bg-ich-error text-white font-ui font-bold text-sm
               rounded-ich-lg hover:bg-red-700 transition-colors">
        Hapus Akun Saya
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-display font-bold text-ich-ink-900">
                Yakin ingin menghapus akun ini?
            </h2>
            <p class="mt-2 text-sm text-ich-ink-600">
                Semua data akun akan dihapus secara permanen dan tidak dapat dipulihkan.
                Masukkan password Anda untuk konfirmasi.
            </p>

            <div class="mt-5">
                <label for="password" class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                    Password
                </label>
                <input id="password" name="password" type="password"
                       placeholder="Masukkan password Anda" minlength="8"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg
                              font-sans text-sm focus:outline-none focus:border-ich-teal-dark">
                @error('password', 'userDeletion')
                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600
                               font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-error text-white font-ui font-bold text-sm
                               rounded-ich-lg hover:bg-red-700 transition-colors">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
