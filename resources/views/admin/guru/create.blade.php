<x-main-layout title="Tambah Guru">

    <div class="mb-6">
        <a href="{{ route('admin.guru.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Tambah Guru</h1>
    </div>

    <div class="max-w-2xl bg-white rounded-xl shadow-ich-card p-6">
        <form method="POST" action="{{ route('admin.guru.store') }}" class="space-y-4" x-data="{ tipe: '{{ old('tipe_guru', 'Guru') }}' }">
            @csrf

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tipe Guru <span class="text-ich-error">*</span></label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe_guru" value="Guru" x-model="tipe"
                               class="text-ich-green focus:ring-ich-green">
                        <span class="font-sans text-sm text-ich-ink-900">Guru Kelas</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe_guru" value="Guru Ngaji" x-model="tipe"
                               class="text-ich-green focus:ring-ich-green">
                        <span class="font-sans text-sm text-ich-ink-900">Guru Ngaji</span>
                    </label>
                </div>
                @error('tipe_guru') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Lengkap <span class="text-ich-error">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none @error('name') border-ich-error @else border-ich-teal @enderror">
                    @error('name') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">NIP</label>
                    <input type="text" name="NIP" value="{{ old('NIP') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
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
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Password <span class="text-ich-error">*</span></label>
                    <input type="password" name="password"
                           class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none @error('password') border-ich-error @else border-ich-teal @enderror">
                    @error('password') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Bergabung</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>

            {{-- Hanya tampil untuk Guru Kelas --}}
            <div x-show="tipe === 'Guru'">
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Mata Pelajaran</label>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="contoh: Matematika"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                               rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.guru.index') }}"
                   class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm
                          rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

</x-main-layout>
