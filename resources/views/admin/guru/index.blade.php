@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Daftar Guru">
<div x-data="{
    showCreate: {{ $errors->any() && old('_modal') === 'create' ? 'true' : 'false' }},
    showEdit: {{ $errors->any() && old('_modal') === 'edit' ? 'true' : 'false' }},
    showDelete: false,
    tipe: '{{ old('tipe_guru', 'Guru') }}',
    editId: '{{ old('_edit_id', '') }}',
    editTipe: '',
    editName: '{{ old('name', '') }}',
    editNIP: '{{ old('NIP', '') }}',
    editNoHp: '{{ old('no_hp', '') }}',
    editHireDate: '{{ old('hire_date', '') }}',
    editSubject: '',
    deleteId: null,
    deleteName: '',
    openEdit(g) {
        this.editId = g.id;
        this.editTipe = g.tipe;
        this.editName = g.nama;
        this.editNIP = g.NIP || '';
        this.editNoHp = g.no_hp || '';
        this.editHireDate = g.hire_date || '';
        this.editSubject = g.subject || '';
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
            <div class="w-11 h-11 rounded-xl bg-ich-green-surface flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Daftar Guru</h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">Kelola data guru</p>
            </div>
        </div>
        @if(! $isReadOnly)
            <button @click="showCreate = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-ich-green text-white
                           font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                + Tambah Guru
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIP..."
               class="flex-1 max-w-xs h-10 px-3.5 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none focus:border-ich-teal">
        <button type="submit" class="h-10 px-4 bg-ich-teal text-white font-ui font-bold text-sm rounded-ich-md hover:bg-ich-teal-dark">Cari</button>
    </form>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-ich-surface">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">NIP</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">No HP</th>
                    <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($guru as $g)
                    <tr class="hover:bg-ich-surface transition-colors">
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $g->nama }}</td>
                        <td class="px-4 py-3 text-ich-ink-500">{{ $g->NIP ?: '-' }}</td>
                        <td class="px-4 py-3 text-ich-ink-500">{{ $g->no_hp ?: '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                @if(! $isReadOnly)
                                    <button @click="openEdit({{ Js::from(['id' => $g->id, 'tipe' => $g->tipe, 'nama' => $g->nama, 'NIP' => $g->NIP, 'no_hp' => $g->no_hp, 'hire_date' => $g->hire_date, 'subject' => $g->subject]) }})"
                                            class="px-2.5 py-1 bg-ich-warning-soft text-ich-warning font-ui font-bold text-xs rounded hover:bg-ich-yellow hover:text-white transition-colors">
                                        Edit
                                    </button>
                                    <button @click="openDelete('{{ $g->id }}', '{{ $g->nama }}')"
                                            class="px-2.5 py-1 bg-ich-error-soft text-ich-error font-ui font-bold text-xs rounded hover:bg-ich-error hover:text-white transition-colors">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-ich-ink-300 font-sans">Belum ada data guru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Modal Create --}}
    <x-admin-modal show="showCreate" title="Tambah Guru" maxWidth="2xl">
        <form method="POST" action="{{ route('admin.guru.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create">

            <input type="hidden" name="tipe_guru" value="Guru">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Lengkap <span class="text-ich-error">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('name') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">NIP</label>
                    <input type="text" name="NIP" value="{{ old('NIP') }}" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Email <span class="text-ich-error">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('email') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">No HP <span class="text-ich-error">*</span></label>
                    <input type="tel" name="no_hp" value="{{ old('no_hp') }}" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('no_hp') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Password <span class="text-ich-error">*</span></label>
                    <input type="password" name="password" minlength="8" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('password') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Bergabung</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date') }}" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">Simpan</button>
                <button type="button" @click="showCreate = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

    {{-- Modal Edit --}}
    <x-admin-modal show="showEdit" title="Edit Guru" maxWidth="xl">
        <form method="POST" :action="'{{ route('admin.guru.update', ':id') }}'.replace(':id', editId)" class="space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="_modal" value="edit">
            <input type="hidden" name="_edit_id" :value="editId">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editName" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">No HP</label>
                    <input type="tel" name="no_hp" x-model="editNoHp" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">NIP</label>
                    <input type="text" name="NIP" x-model="editNIP" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Bergabung</label>
                    <input type="date" name="hire_date" x-model="editHireDate" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">Perbarui</button>
                <button type="button" @click="showEdit = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

    {{-- Modal Delete --}}
    <x-admin-modal show="showDelete" title="Konfirmasi Hapus" maxWidth="sm">
        <p class="text-sm text-ich-ink-600 mb-4">Yakin ingin menghapus guru <strong x-text="deleteName"></strong>?</p>
        <form method="POST" :action="'{{ route('admin.guru.destroy', ':id') }}'.replace(':id', deleteId)">
            @csrf @method('DELETE')
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-ich-error text-white font-ui font-bold text-sm rounded-ich-lg hover:opacity-90 transition-opacity">Hapus</button>
                <button type="button" @click="showDelete = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

</div>
</x-main-layout>
