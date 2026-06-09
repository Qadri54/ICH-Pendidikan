@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Tabungan Siswa">
<div x-data="{
    showCreate: {{ $errors->any() && old('_modal') === 'create' ? 'true' : 'false' }},
    showDelete: false,
    deleteId: null,
    deleteName: '',
    openDelete(id, name) {
        this.deleteId = id;
        this.deleteName = name;
        this.showDelete = true;
    }
}">

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-[#FEF5DC] flex items-center justify-center">
                <svg class="w-5 h-5 text-[#E09F17]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Tabungan Siswa</h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">Total: {{ $ledgers->total() }} ledger</p>
            </div>
        </div>
        @if(! $isReadOnly)
            <button @click="showCreate = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-ich-green text-white
                           font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                + Buat Ledger
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-[#FEE2E2] text-ich-error rounded-lg text-sm font-semibold">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#F5F6FA]">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Ledger</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Guru PJ</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Tahun Akademik</th>
                    <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Total Saldo</th>
                    <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Status</th>
                    <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($ledgers as $l)
                    <tr class="hover:bg-[#F5F6FA] transition-colors">
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $l->ledger_name }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $l->teacher?->user?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-ich-ink-500">
                            {{ $l->academic_year ? \Carbon\Carbon::parse($l->academic_year)->format('Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right font-ui font-semibold text-ich-green">
                            Rp {{ number_format($l->total_balance, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 font-ui font-bold text-xs rounded-full
                                {{ $l->status === 'Aktif' ? 'bg-[#D1FAE5] text-[#009966]' : 'bg-[#F3F4F6] text-ich-ink-500' }}">
                                {{ $l->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.tabungan.show', $l) }}"
                                   class="px-2.5 py-1 bg-[#F4F7FC] text-ich-teal font-ui font-bold text-xs rounded hover:bg-ich-teal hover:text-white transition-colors">
                                    Detail
                                </a>
                                @if(! $isReadOnly)
                                    <button @click="openDelete('{{ $l->ledger_id }}', '{{ $l->ledger_name }}')"
                                            class="px-2.5 py-1 bg-[#FEE2E2] text-ich-error font-ui font-bold text-xs rounded hover:bg-ich-error hover:text-white transition-colors">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-ich-ink-300 font-sans">Belum ada data tabungan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $ledgers->links() }}</div>

    {{-- Modal Create --}}
    <x-admin-modal show="showCreate" title="Buat Ledger Tabungan" maxWidth="md">
        <form method="POST" action="{{ route('admin.tabungan.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create">

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ledger <span class="text-ich-error">*</span></label>
                <input type="text" name="ledger_name" value="{{ old('ledger_name') }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                @error('ledger_name') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Guru PJ <span class="text-ich-error">*</span></label>
                <select name="teacher_id" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
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
                <button type="submit" class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">Simpan</button>
                <button type="button" @click="showCreate = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

    {{-- Modal Delete --}}
    <x-admin-modal show="showDelete" title="Konfirmasi Hapus" maxWidth="sm">
        <p class="text-sm text-ich-ink-600 mb-4">Yakin ingin menghapus ledger <strong x-text="deleteName"></strong>?</p>
        <form method="POST" :action="'{{ route('admin.tabungan.destroy', ':id') }}'.replace(':id', deleteId)">
            @csrf @method('DELETE')
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-ich-error text-white font-ui font-bold text-sm rounded-ich-lg hover:opacity-90 transition-opacity">Hapus</button>
                <button type="button" @click="showDelete = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

</div>
</x-main-layout>
