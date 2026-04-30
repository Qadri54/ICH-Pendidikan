<x-main-layout title="Edit Tagihan">

    <div class="mb-6">
        <a href="{{ route('admin.keuangan.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Edit Tagihan SPP</h1>
    </div>

    <div class="max-w-md bg-white rounded-xl shadow-ich-card p-6">
        <div class="mb-4 px-3 py-2 bg-[#F4F7FC] rounded-ich-md text-sm text-ich-teal font-ui font-semibold">
            Siswa: {{ $keuangan->student?->nama_siswa }}
        </div>

        <form method="POST" action="{{ route('admin.keuangan.update', $keuangan) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Jumlah (Rp)</label>
                <input type="number" name="jumlah" value="{{ old('jumlah', $keuangan->jumlah) }}" min="0"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Jatuh Tempo</label>
                <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo', $keuangan->jatuh_tempo) }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Status</label>
                <select name="status"
                        class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="Belum Bayar" {{ $keuangan->status === 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="Lunas"       {{ $keuangan->status === 'Lunas'       ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                               rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Perbarui
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
