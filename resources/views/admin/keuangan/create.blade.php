<x-main-layout title="Buat Tagihan SPP">

    <div class="mb-6">
        <a href="{{ route('admin.keuangan.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Buat Tagihan SPP</h1>
    </div>

    <div class="max-w-md bg-white rounded-xl shadow-ich-card p-6">
        <form method="POST" action="{{ route('admin.keuangan.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Siswa <span class="text-ich-error">*</span></label>
                <select name="student_id"
                        class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                               focus:outline-none @error('student_id') border-ich-error @else border-ich-teal @enderror">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswa as $s)
                        <option value="{{ $s->student_id }}" {{ old('student_id') == $s->student_id ? 'selected' : '' }}>
                            {{ $s->nama_siswa }} — {{ $s->classRoom?->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                @error('student_id') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Bulan/Tahun <span class="text-ich-error">*</span></label>
                <input type="date" name="tanggal_tahun" value="{{ old('tanggal_tahun') }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                @error('tanggal_tahun') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Jumlah (Rp) <span class="text-ich-error">*</span></label>
                <input type="number" name="jumlah" value="{{ old('jumlah', 500000) }}" min="0"
                       class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none @error('jumlah') border-ich-error @else border-ich-teal @enderror">
                @error('jumlah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Jatuh Tempo <span class="text-ich-error">*</span></label>
                <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo') }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                @error('jatuh_tempo') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Status</label>
                <select name="status"
                        class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="Belum Bayar">Belum Bayar</option>
                    <option value="Lunas">Lunas</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                               rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.keuangan.index') }}"
                   class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm
                          rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

</x-main-layout>
