<x-main-layout title="Tambah Kelas">

    <div class="mb-6">
        <a href="{{ route('admin.kelas.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Tambah Kelas</h1>
    </div>

    <div class="max-w-md bg-white rounded-xl shadow-ich-card p-6">
        <form method="POST" action="{{ route('admin.kelas.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Kelas <span class="text-ich-error">*</span></label>
                <input type="text" name="nama_kelas" value="{{ old('nama_kelas') }}" placeholder="contoh: Kelas 1A"
                       class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal-dark
                              @error('nama_kelas') border-ich-error @else border-ich-teal @enderror">
                @error('nama_kelas') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ruangan <span class="text-ich-error">*</span></label>
                <input type="text" name="nama_ruangan" value="{{ old('nama_ruangan') }}" placeholder="contoh: Ruang Melati"
                       class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal-dark
                              @error('nama_ruangan') border-ich-error @else border-ich-teal @enderror">
                @error('nama_ruangan') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Wali Kelas</label>
                <select name="homeroom_teacher_id"
                        class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                               focus:outline-none focus:border-ich-teal-dark
                               @error('homeroom_teacher_id') border-ich-error @else border-ich-teal @enderror">
                    <option value="">-- Belum Ditentukan --</option>
                    @foreach($guru as $g)
                        <option value="{{ $g->teacher_id }}" {{ old('homeroom_teacher_id') == $g->teacher_id ? 'selected' : '' }}>
                            {{ $g->user?->name ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('homeroom_teacher_id') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                               rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.kelas.index') }}"
                   class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm
                          rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

</x-main-layout>
