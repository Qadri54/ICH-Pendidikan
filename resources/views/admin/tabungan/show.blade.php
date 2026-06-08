@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Detail Ledger Tabungan">
<div x-data="{
    showCreate: {{ $errors->any() && old('_modal') === 'create' ? 'true' : 'false' }},
}">

    <div class="mb-6">
        <a href="{{ route('admin.tabungan.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">{{ $tabungan->ledger_name }}</h1>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-[#FEE2E2] text-ich-error rounded-lg text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-xl shadow-ich-card p-6 space-y-3">
            <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3">Info Ledger</h2>
            @foreach([
                ['Guru PJ',      $tabungan->teacher?->user?->name ?? '-'],
                ['Th. Akademik', $tabungan->academic_year ? \Carbon\Carbon::parse($tabungan->academic_year)->format('Y') : '-'],
                ['Saldo Awal',   'Rp '.number_format($tabungan->opening_balance, 0, ',', '.')],
                ['Total Saldo',  'Rp '.number_format($tabungan->total_balance, 0, ',', '.')],
                ['Status',       $tabungan->status],
            ] as [$label, $value])
                <div class="flex gap-3 py-1.5 border-b border-ich-line last:border-0">
                    <div class="w-28 font-ui font-bold text-xs text-ich-ink-400 shrink-0">{{ $label }}</div>
                    <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="px-5 py-4 border-b border-ich-line flex items-center justify-between">
                <h2 class="font-ui font-bold text-ich-ink-900">Daftar Tabungan Siswa</h2>
                @if(! $isReadOnly && $tabungan->status === 'Active')
                    <button @click="showCreate = true"
                            class="flex items-center gap-1.5 px-4 py-2 bg-ich-green text-white
                                   font-ui font-bold text-xs rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark">
                        <x-ich-icon name="plus" :size="14" color="white"/>
                        Buka Buku Tabungan
                    </button>
                @endif
            </div>
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F6FA]">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Siswa</th>
                        <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Saldo</th>
                        <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($tabungan->passbooks as $pb)
                        <tr class="hover:bg-[#F5F6FA]">
                            <td class="px-4 py-3 font-sans text-ich-ink-900">
                                {{ $pb->student?->nama_siswa ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-ui font-semibold text-ich-green">
                                Rp {{ number_format($pb->current_balance, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.tabungan.passbook.show', $pb) }}"
                                   class="text-xs font-ui font-bold text-ich-teal hover:underline">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-ich-ink-300 font-sans">
                                Belum ada buku tabungan siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    {{-- Modal Buka Buku Tabungan --}}
    <x-admin-modal show="showCreate" title="Buka Buku Tabungan" maxWidth="md">
        <form method="POST" action="{{ route('admin.tabungan.passbook.store', $tabungan) }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create">

            <div class="px-3 py-2 bg-[#F4F7FC] rounded-ich-md text-sm text-ich-teal font-ui font-semibold">
                Ledger: {{ $tabungan->ledger_name }}
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Siswa <span class="text-ich-error">*</span></label>
                <select name="student_id" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($students as $s)
                        <option value="{{ $s->student_id }}" {{ old('student_id') == $s->student_id ? 'selected' : '' }}>
                            {{ $s->nama_siswa }}
                            @if($s->classRoom) ({{ $s->classRoom->nama_kelas }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('student_id') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                @if($students->isEmpty())
                    <p class="text-ich-ink-400 text-xs mt-1">Semua siswa sudah memiliki buku tabungan di ledger ini.</p>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Buka <span class="text-ich-error">*</span></label>
                    <input type="date" name="opening_date" value="{{ old('opening_date', now()->format('Y-m-d')) }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('opening_date') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Saldo Awal (Rp)</label>
                    <input type="number" name="opening_balance" value="{{ old('opening_balance', 0) }}" min="0"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" {{ $students->isEmpty() ? 'disabled' : '' }}
                        class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Buka Buku Tabungan
                </button>
                <button type="button" @click="showCreate = false"
                        class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </x-admin-modal>

</div>
</x-main-layout>
