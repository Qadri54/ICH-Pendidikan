<x-main-layout title="Buka Buku Tabungan">

    <div class="mb-6">
        <a href="{{ route('admin.tabungan.show', $tabungan) }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali ke {{ $tabungan->ledger_name }}</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Buka Buku Tabungan</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">Ledger: {{ $tabungan->ledger_name }}</p>
    </div>

    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-lg">
        <form method="POST" action="{{ route('admin.tabungan.passbook.store', $tabungan) }}"
              class="bg-white rounded-xl shadow-ich-card p-6 space-y-5">
            @csrf

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Siswa <span class="text-ich-error">*</span>
                </label>
                <select name="student_id"
                        class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                               focus:outline-none focus:border-ich-teal
                               @error('student_id') border-ich-error @else border-ich-line @enderror">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($students as $s)
                        <option value="{{ $s->student_id }}" {{ old('student_id') == $s->student_id ? 'selected' : '' }}>
                            {{ $s->nama_siswa }}
                            @if($s->classRoom) ({{ $s->classRoom->nama_kelas }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                @enderror
                @if($students->isEmpty())
                    <p class="text-ich-ink-400 text-xs mt-1">Semua siswa sudah memiliki buku tabungan di ledger ini.</p>
                @endif
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Tanggal Buka <span class="text-ich-error">*</span>
                </label>
                <input type="date" name="opening_date"
                       value="{{ old('opening_date', now()->format('Y-m-d')) }}"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('opening_date') border-ich-error @else border-ich-line @enderror">
                @error('opening_date')
                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Saldo Awal <span class="text-ich-ink-400 font-normal">(opsional, default 0)</span>
                </label>
                <input type="number" name="opening_balance"
                       value="{{ old('opening_balance', 0) }}"
                       min="0"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('opening_balance') border-ich-error @else border-ich-line @enderror">
                @error('opening_balance')
                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.tabungan.show', $tabungan) }}"
                   class="flex-1 h-11 flex items-center justify-center bg-white border-2 border-ich-line
                          text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-ich-surface">
                    Batal
                </a>
                <button type="submit" {{ $students->isEmpty() ? 'disabled' : '' }}
                        class="flex-1 h-11 bg-ich-green text-white font-ui font-bold text-sm
                               rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    Buka Buku Tabungan
                </button>
            </div>
        </form>
    </div>

</x-main-layout>
