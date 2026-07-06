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

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Kelas</label>
                <select name="class_id"
                        class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">Semua Kelas (tidak dibatasi)</option>
                    @foreach(\App\Models\ClassRoom::orderBy('nama_kelas')->get() as $c)
                        <option value="{{ $c->class_id }}" {{ old('class_id') == $c->class_id ? 'selected' : '' }}>{{ $c->nama_kelas }}</option>
                    @endforeach
                </select>
                <p class="text-ich-ink-400 text-xs mt-1">Jika dipilih, hanya siswa dari kelas ini yang bisa membuka buku tabungan.</p>
                @error('class_id') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Periode Semester <span class="text-ich-error">*</span></label>
                <select name="period_id"
                        class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">-- Pilih Periode --</option>
                    @foreach(\App\Models\AcademicPeriod::orderByDesc('tanggal_mulai')->get() as $p)
                        <option value="{{ $p->period_id }}" {{ old('period_id') == $p->period_id ? 'selected' : '' }}>
                            {{ $p->tahun_ajaran }} — Semester {{ $p->semester }}
                            @if($p->is_active) (Aktif) @endif
                        </option>
                    @endforeach
                </select>
                @error('period_id') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Buka</label>
                <input type="date" name="opening_date" value="{{ old('opening_date', now()->format('Y-m-d')) }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
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
