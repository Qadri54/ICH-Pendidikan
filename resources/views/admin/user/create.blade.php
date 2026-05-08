<x-main-layout title="Tambah User">

    <div class="mb-6">
        <a href="{{ route('admin.user.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Tambah User</h1>
    </div>

    <div class="max-w-md bg-white rounded-xl shadow-ich-card p-6">
        <form method="POST" action="{{ route('admin.user.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama <span class="text-ich-error">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none @error('name') border-ich-error @else border-ich-teal @enderror">
                @error('name') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Email <span class="text-ich-error">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none @error('email') border-ich-error @else border-ich-teal @enderror">
                @error('email') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">No HP <span class="text-ich-error">*</span></label>
                <input type="tel" name="no_hp" value="{{ old('no_hp') }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                @error('no_hp') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Role <span class="text-ich-error">*</span></label>
                <select name="role_name"
                        class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r }}" {{ old('role_name') === $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
                @error('role_name') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Password <span class="text-ich-error">*</span></label>
                <input type="password" name="password"
                       class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none @error('password') border-ich-error @else border-ich-teal @enderror">
                @error('password') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Konfirmasi Password <span class="text-ich-error">*</span></label>
                <input type="password" name="password_confirmation"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                               rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.user.index') }}"
                   class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm
                          rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

</x-main-layout>
