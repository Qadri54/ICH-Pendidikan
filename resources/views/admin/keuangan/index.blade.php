@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Keuangan SPP">
<div x-data="{
    showCreate: {{ $errors->any() && old('_modal') === 'create' ? 'true' : 'false' }},
    showEdit: {{ $errors->any() && old('_modal') === 'edit' ? 'true' : 'false' }},
    showDelete: false,
    editId: '{{ old('_edit_id', '') }}',
    editSiswa: '',
    editStatus: '{{ old('status', '') }}',
    deleteId: null,
    deleteName: '',
    openEdit(inv) {
        this.editId = inv.invoice_id;
        this.editSiswa = inv.nama_siswa;
        this.editStatus = inv.status_raw;
        this.showEdit = true;
    },
    openDelete(id, name) {
        this.deleteId = id;
        this.deleteName = name;
        this.showDelete = true;
    }
}">

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-[#E8F5EA] flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Keuangan SPP</h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">Total tagihan berjalan:
                <span class="font-bold text-ich-error">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                · {{ $totalLunas }} sudah lunas
            </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.keuangan.bukti-pembayaran') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 border border-blue-200
                      font-ui font-bold text-sm rounded-ich-lg hover:bg-blue-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Bukti Pembayaran
                @if(\App\Models\SppPayment::where('status', 'pending')->count() > 0)
                    <span class="ml-1 px-1.5 py-0.5 bg-ich-error text-white text-xs font-bold rounded-full">
                        {{ \App\Models\SppPayment::where('status', 'pending')->count() }}
                    </span>
                @endif
            </a>
            @if(! $isReadOnly)
                <button @click="showCreate = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-ich-teal text-white
                               font-ui font-bold text-sm rounded-ich-lg hover:opacity-90 transition-colors">
                    + Buat Tagihan Manual
                </button>
                <form method="POST" action="{{ route('admin.keuangan.generate') }}"
                      onsubmit="return confirm('Generate tagihan SPP untuk semua siswa bulan ini?')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-ich-green text-white
                                   font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                        + Generate Tagihan Bulan Ini
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..."
               class="flex-1 max-w-xs h-10 px-3.5 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none focus:border-ich-teal">
        <select name="status" class="h-10 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none">
            <option value="">Semua Status</option>
            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
        </select>
        <button type="submit" class="h-10 px-4 bg-ich-teal text-white font-ui font-bold text-sm rounded-ich-md hover:bg-ich-teal-dark">Cari</button>
    </form>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#F5F6FA]">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Siswa</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Bulan/Tahun</th>
                    <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Jumlah</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Jatuh Tempo</th>
                    <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Status</th>
                    <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($invoices as $inv)
                    @php
                        $statusColor = $inv->status === 'Lunas'
                            ? 'bg-[#D1FAE5] text-[#009966]'
                            : 'bg-[#FEE2E2] text-ich-error';
                        $statusRaw = $inv->status === 'Lunas' ? 'paid' : 'unpaid';
                    @endphp
                    <tr class="hover:bg-[#F5F6FA]">
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $inv->student?->nama_siswa ?? '-' }}</td>
                        <td class="px-4 py-3 text-ich-ink-500">{{ $inv->student?->classRoom?->nama_kelas ?? '-' }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">
                            {{ $inv->tanggal_tahun ? \Carbon\Carbon::parse($inv->tanggal_tahun)->translatedFormat('F Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right font-ui font-semibold">
                            Rp {{ number_format($inv->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-ich-ink-500">
                            {{ $inv->jatuh_tempo ? \Carbon\Carbon::parse($inv->jatuh_tempo)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 font-ui font-bold text-xs rounded-full {{ $statusColor }}">{{ $inv->status }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                @if(! $isReadOnly)
                                    <button @click="openEdit({{ Js::from(['invoice_id' => $inv->invoice_id, 'nama_siswa' => $inv->student?->nama_siswa ?? '-', 'status_raw' => $statusRaw]) }})"
                                            class="px-2.5 py-1 bg-[#FEF5DC] text-[#E09F17] font-ui font-bold text-xs rounded hover:bg-ich-yellow hover:text-white transition-colors">
                                        Edit
                                    </button>
                                    <button @click="openDelete('{{ $inv->invoice_id }}', '{{ $inv->student?->nama_siswa ?? '-' }}')"
                                            class="px-2.5 py-1 bg-[#FEE2E2] text-ich-error font-ui font-bold text-xs rounded hover:bg-ich-error hover:text-white transition-colors">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-ich-ink-300 font-sans">Belum ada data tagihan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $invoices->links() }}</div>

    {{-- Modal Create --}}
    <x-admin-modal show="showCreate" title="Buat Tagihan SPP" maxWidth="md">
        <form method="POST" action="{{ route('admin.keuangan.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create">

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Siswa <span class="text-ich-error">*</span></label>
                <select name="student_id" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
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
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Jumlah (Rp) <span class="text-ich-error">*</span></label>
                    <input type="number" name="jumlah" value="{{ old('jumlah', 500000) }}" min="0"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('jumlah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Jatuh Tempo <span class="text-ich-error">*</span></label>
                    <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('jatuh_tempo') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Status</label>
                <select name="status" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="unpaid">Belum Bayar</option>
                    <option value="paid">Lunas</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">Simpan</button>
                <button type="button" @click="showCreate = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

    {{-- Modal Edit --}}
    <x-admin-modal show="showEdit" title="Edit Tagihan" maxWidth="sm">
        <form method="POST" :action="'{{ route('admin.keuangan.update', ':id') }}'.replace(':id', editId)" class="space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="_modal" value="edit">
            <input type="hidden" name="_edit_id" :value="editId">

            <div class="px-3 py-2 bg-[#F4F7FC] rounded-ich-md text-sm text-ich-teal font-ui font-semibold" x-text="'Siswa: ' + editSiswa"></div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Status</label>
                <select name="status" x-model="editStatus" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="unpaid">Belum Bayar</option>
                    <option value="paid">Lunas</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">Perbarui</button>
                <button type="button" @click="showEdit = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

    {{-- Modal Delete --}}
    <x-admin-modal show="showDelete" title="Konfirmasi Hapus" maxWidth="sm">
        <p class="text-sm text-ich-ink-600 mb-4">Yakin ingin menghapus tagihan untuk <strong x-text="deleteName"></strong>?</p>
        <form method="POST" :action="'{{ route('admin.keuangan.destroy', ':id') }}'.replace(':id', deleteId)">
            @csrf @method('DELETE')
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-ich-error text-white font-ui font-bold text-sm rounded-ich-lg hover:opacity-90 transition-opacity">Hapus</button>
                <button type="button" @click="showDelete = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

</div>
</x-main-layout>
