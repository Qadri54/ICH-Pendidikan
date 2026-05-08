<x-main-layout title="Buat Ledger Tabungan">

    <div class="mb-6">
        <a href="{{ route('admin.tabungan.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Buat Ledger Tabungan</h1>
    </div>

    <div class="max-w-md bg-white rounded-xl shadow-ich-card p-6">
        <form method="POST" action="{{ route('admin.tabungan.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ledger <span class="text-ich-error">*</span></label>
                <input type="text" name="ledger_name" value="{{ old('ledger_name') }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none @error('ledger_name') border-ich-error @else border-ich-teal @enderror">
                @error('ledger_name') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Guru PJ <span class="text-ich-error">*</span></label>
                <select name="teacher_id"
                        class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($guru as $g)
                        <option value="{{ $g->teacher_id }}" {{ old('teacher_id') == $g->teacher_id ? 'selected' : '' }}>
                            {{ $g->user?->name ?? 'Guru #'.$g->teacher_id }}
                        </option>
                    @endforeach
                </select>
                @error('teacher_id') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tahun Akademik</label>
                    <input type="date" name="academic_year" value="{{ old('academic_year') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Buka</label>
                    <input type="date" name="opening_date" value="{{ old('opening_date', now()->format('Y-m-d')) }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Saldo Awal (Rp)</label>
                <input type="number" name="opening_balance" value="{{ old('opening_balance', 0) }}" min="0"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                               rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.tabungan.index') }}"
                   class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm
                          rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

</x-main-layout>
