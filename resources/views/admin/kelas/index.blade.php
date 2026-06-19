@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Daftar Kelas">
<div x-data="{
    showCreate: {{ $errors->any() && old('_modal') === 'create' ? 'true' : 'false' }},
    showEdit: {{ $errors->any() && old('_modal') === 'edit' ? 'true' : 'false' }},
    showDelete: false,
    editId: '{{ old('_edit_id', '') }}',
    editNama: '{{ old('nama_kelas', '') }}',
    editRuangan: '{{ old('nama_ruangan', '') }}',
    editGuru: '{{ old('homeroom_teacher_id', '') }}',
    deleteId: null,
    deleteName: '',
    openEdit(k) {
        this.editId = k.class_id;
        this.editNama = k.nama_kelas;
        this.editRuangan = k.nama_ruangan;
        this.editGuru = k.homeroom_teacher_id || '';
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
            <div class="w-11 h-11 rounded-xl bg-ich-purple-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Daftar Kelas</h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">Total: {{ $kelas->total() }} kelas</p>
            </div>
        </div>
        @if(! $isReadOnly)
            <button @click="showCreate = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-ich-green text-white
                           font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                + Tambah Kelas
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-ich-surface">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Kelas</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Ruangan</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Wali Kelas</th>
                    <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Jumlah Siswa</th>
                    <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($kelas as $k)
                    <tr class="hover:bg-ich-surface transition-colors">
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $k->nama_kelas }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $k->nama_ruangan }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">
                            {{ $k->homeroomTeacher?->user?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 bg-ich-info-soft text-ich-teal font-ui font-bold text-xs rounded-full">
                                {{ $k->students_count }} siswa
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                @if(! $isReadOnly)
                                    <button @click="openEdit({{ Js::from(['class_id' => $k->class_id, 'nama_kelas' => $k->nama_kelas, 'nama_ruangan' => $k->nama_ruangan, 'homeroom_teacher_id' => $k->homeroom_teacher_id]) }})"
                                            class="px-2.5 py-1 bg-ich-warning-soft text-ich-warning font-ui font-bold text-xs rounded hover:bg-ich-yellow hover:text-white transition-colors">
                                        Edit
                                    </button>
                                    <button @click="openDelete('{{ $k->class_id }}', '{{ $k->nama_kelas }}')"
                                            class="px-2.5 py-1 bg-ich-error-soft text-ich-error font-ui font-bold text-xs rounded hover:bg-ich-error hover:text-white transition-colors">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                            Belum ada data kelas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $kelas->links() }}</div>

    {{-- Modal Create --}}
    <x-admin-modal show="showCreate" title="Tambah Kelas" maxWidth="md">
        <form method="POST" action="{{ route('admin.kelas.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create">

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Kelas <span class="text-ich-error">*</span></label>
                <input type="text" name="nama_kelas" value="{{ old('_modal') === 'create' ? old('nama_kelas') : '' }}" placeholder="contoh: Kelas 1A"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal-dark">
                @error('nama_kelas') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ruangan <span class="text-ich-error">*</span></label>
                <input type="text" name="nama_ruangan" value="{{ old('_modal') === 'create' ? old('nama_ruangan') : '' }}" placeholder="contoh: Ruang Melati"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal-dark">
                @error('nama_ruangan') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Wali Kelas</label>
                <select name="homeroom_teacher_id"
                        class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">-- Belum Ditentukan --</option>
                    @foreach($guru as $g)
                        <option value="{{ $g->teacher_id }}" {{ old('homeroom_teacher_id') == $g->teacher_id ? 'selected' : '' }}>
                            {{ $g->user?->name ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Simpan
                </button>
                <button type="button" @click="showCreate = false"
                        class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </x-admin-modal>

    {{-- Modal Edit --}}
    <x-admin-modal show="showEdit" title="Edit Kelas" maxWidth="md">
        <form method="POST" :action="'{{ route('admin.kelas.update', ':id') }}'.replace(':id', editId)" class="space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="_modal" value="edit">
            <input type="hidden" name="_edit_id" :value="editId">

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Kelas <span class="text-ich-error">*</span></label>
                <input type="text" name="nama_kelas" x-model="editNama"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal-dark">
                @if(old('_modal') === 'edit') @error('nama_kelas') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror @endif
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ruangan <span class="text-ich-error">*</span></label>
                <input type="text" name="nama_ruangan" x-model="editRuangan"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal-dark">
                @if(old('_modal') === 'edit') @error('nama_ruangan') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror @endif
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Wali Kelas</label>
                <select name="homeroom_teacher_id" x-model="editGuru"
                        class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">-- Belum Ditentukan --</option>
                    @foreach($guru as $g)
                        <option value="{{ $g->teacher_id }}">{{ $g->user?->name ?? '-' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Perbarui
                </button>
                <button type="button" @click="showEdit = false"
                        class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </x-admin-modal>

    {{-- Modal Delete --}}
    <x-admin-modal show="showDelete" title="Konfirmasi Hapus" maxWidth="sm">
        <p class="text-sm text-ich-ink-600 mb-4">Yakin ingin menghapus kelas <strong x-text="deleteName"></strong>?</p>
        <form method="POST" :action="'{{ route('admin.kelas.destroy', ':id') }}'.replace(':id', deleteId)">
            @csrf @method('DELETE')
            <div class="flex gap-3">
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-error text-white font-ui font-bold text-sm rounded-ich-lg hover:opacity-90 transition-opacity">
                    Hapus
                </button>
                <button type="button" @click="showDelete = false"
                        class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </x-admin-modal>

</div>
</x-main-layout>
