@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Daftar Siswa">
<div x-data="{
    showCreate: {{ $errors->any() && old('_modal') === 'create' ? 'true' : 'false' }},
    showEdit: {{ $errors->any() && old('_modal') === 'edit' ? 'true' : 'false' }},
    showDetail: false,
    showDelete: false,
    editId: '{{ old('_edit_id', '') }}',
    e: {
        nama_siswa: '{{ old('nama_siswa', '') }}',
        NIS: '{{ old('NIS', '') }}',
        class_id: '{{ old('class_id', '') }}',
        jenis_kelamin: '{{ old('jenis_kelamin', 'L') }}',
        tanggal_lahir: '{{ old('tanggal_lahir', '') }}',
        tempat_lahir: '{{ old('tempat_lahir', '') }}',
        nama_ayah: '{{ old('nama_ayah', '') }}',
        nama_ibu: '{{ old('nama_ibu', '') }}',
        status: '{{ old('status', 'aktif') }}',
    },
    detail: {},
    deleteId: null,
    deleteName: '',
    openEdit(s) {
        this.editId = s.student_id;
        this.e = {
            nama_siswa: s.nama_siswa,
            NIS: s.NIS || '',
            class_id: s.class_id || '',
            jenis_kelamin: s.jenis_kelamin || 'L',
            tanggal_lahir: s.tanggal_lahir || '',
            tempat_lahir: s.tempat_lahir || '',
            nama_ayah: s.nama_ayah || '',
            nama_ibu: s.nama_ibu || '',
            status: s.status || 'aktif',
        };
        this.showEdit = true;
    },
    openDetail(s) {
        this.detail = s;
        this.showDetail = true;
    },
    openDelete(id, name) {
        this.deleteId = id;
        this.deleteName = name;
        this.showDelete = true;
    }
}">

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-ich-blue-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Daftar Siswa</h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">Total: {{ $siswa->total() }} siswa</p>
            </div>
        </div>
        @if(! $isReadOnly)
            <button @click="showCreate = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-ich-green text-white
                           font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                + Tambah Siswa
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIS..."
               class="flex-1 max-w-xs h-10 px-3.5 bg-white border border-ich-line rounded-ich-md font-sans text-sm text-ich-ink-900 placeholder:text-ich-ink-300 focus:outline-none focus:border-ich-teal">
        <select name="kelas" class="h-10 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none focus:border-ich-teal">
            <option value="">Semua Kelas</option>
            @foreach($kelas as $k)
                <option value="{{ $k->class_id }}" {{ request('kelas') == $k->class_id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
        <select name="status" class="h-10 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none focus:border-ich-teal">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="alumni" {{ request('status') === 'alumni' ? 'selected' : '' }}>Alumni</option>
            <option value="keluar" {{ request('status') === 'keluar' ? 'selected' : '' }}>Keluar</option>
        </select>
        <button type="submit" class="h-10 px-4 bg-ich-teal text-white font-ui font-bold text-sm rounded-ich-md hover:bg-ich-teal-dark transition-colors">Cari</button>
        @if(request()->hasAny(['search','kelas','status']))
            <a href="{{ route('admin.siswa.index') }}" class="h-10 px-4 flex items-center bg-white border border-ich-line text-ich-ink-500 font-ui text-sm rounded-ich-md hover:bg-gray-50 transition-colors">Reset</a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-ich-surface">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">NIS</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">JK</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Status</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Ayah</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Ibu</th>
                    <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($siswa as $s)
                    <tr class="hover:bg-ich-surface transition-colors">
                        <td class="px-4 py-3 font-sans text-ich-ink-500">{{ $s->NIS }}</td>
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $s->nama_siswa }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-ich-green-surface text-ich-green font-ui font-bold text-xs rounded-full">{{ $s->classRoom?->nama_kelas ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $s->jenis_kelamin }}</td>
                        <td class="px-4 py-3">
                            <x-pill :tone="match($s->status) { 'aktif' => 'success', 'alumni' => 'info', 'keluar' => 'error', default => 'warning' }">
                                {{ ucfirst($s->status) }}
                            </x-pill>
                        </td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $s->nama_ayah }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $s->nama_ibu }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openDetail({{ Js::from(['NIS' => $s->NIS, 'nama_siswa' => $s->nama_siswa, 'kelas' => $s->classRoom?->nama_kelas ?? '-', 'jenis_kelamin' => $s->jenis_kelamin, 'tanggal_lahir' => $s->tanggal_lahir ? \Carbon\Carbon::parse($s->tanggal_lahir)->format('d M Y') : '-', 'tempat_lahir' => $s->tempat_lahir, 'nama_ayah' => $s->nama_ayah, 'nama_ibu' => $s->nama_ibu, 'status' => ucfirst($s->status)]) }})"
                                        class="px-2.5 py-1 bg-ich-info-soft text-ich-teal font-ui font-bold text-xs rounded hover:bg-ich-teal hover:text-white transition-colors">
                                    Detail
                                </button>
                                @if(! $isReadOnly)
                                    <button @click="openEdit({{ Js::from(['student_id' => $s->student_id, 'nama_siswa' => $s->nama_siswa, 'NIS' => $s->NIS, 'class_id' => $s->class_id, 'jenis_kelamin' => $s->jenis_kelamin, 'tanggal_lahir' => $s->tanggal_lahir?->format('Y-m-d'), 'tempat_lahir' => $s->tempat_lahir, 'nama_ayah' => $s->nama_ayah, 'nama_ibu' => $s->nama_ibu, 'status' => $s->status]) }})"
                                            class="px-2.5 py-1 bg-ich-warning-soft text-ich-warning font-ui font-bold text-xs rounded hover:bg-ich-yellow hover:text-white transition-colors">
                                        Edit
                                    </button>
                                    <button @click="openDelete('{{ $s->student_id }}', '{{ $s->nama_siswa }}')"
                                            class="px-2.5 py-1 bg-ich-error-soft text-ich-error font-ui font-bold text-xs rounded hover:bg-ich-error hover:text-white transition-colors">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-ich-ink-300 font-sans">Belum ada data siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $siswa->links() }}</div>

    {{-- Modal Create --}}
    <x-admin-modal show="showCreate" title="Tambah Siswa Baru" maxWidth="2xl">
        <form method="POST" action="{{ route('admin.siswa.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Siswa <span class="text-ich-error">*</span></label>
                    <input type="text" name="nama_siswa" value="{{ old('_modal') === 'create' ? old('nama_siswa') : '' }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal-dark">
                    @error('nama_siswa') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">NIS</label>
                    <input type="text" name="NIS" value="{{ old('_modal') === 'create' ? old('NIS') : '' }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Kelas <span class="text-ich-error">*</span></label>
                    <select name="class_id" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->class_id }}" {{ old('class_id') == $k->class_id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    @error('class_id') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Jenis Kelamin <span class="text-ich-error">*</span></label>
                    <select name="jenis_kelamin" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                        <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Lahir <span class="text-ich-error">*</span></label>
                    <input type="date" name="tanggal_lahir" value="{{ old('_modal') === 'create' ? old('tanggal_lahir') : '' }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('tanggal_lahir') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tempat Lahir <span class="text-ich-error">*</span></label>
                    <input type="text" name="tempat_lahir" value="{{ old('_modal') === 'create' ? old('tempat_lahir') : '' }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('tempat_lahir') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ayah <span class="text-ich-error">*</span></label>
                    <input type="text" name="nama_ayah" value="{{ old('_modal') === 'create' ? old('nama_ayah') : '' }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('nama_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ibu <span class="text-ich-error">*</span></label>
                    <input type="text" name="nama_ibu" value="{{ old('_modal') === 'create' ? old('nama_ibu') : '' }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('nama_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Akun Orang Tua (opsional)</label>
                <select name="user_id" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">-- Tidak ditautkan --</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->user_id }}" {{ old('user_id') == $p->user_id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->email }})</option>
                    @endforeach
                </select>
                <p class="text-ich-ink-400 text-xs mt-1">Tautkan siswa ke akun orang tua yang sudah terdaftar.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">Simpan</button>
                <button type="button" @click="showCreate = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

    {{-- Modal Edit --}}
    <x-admin-modal show="showEdit" title="Edit Siswa" maxWidth="2xl">
        <form method="POST" :action="'{{ route('admin.siswa.update', ':id') }}'.replace(':id', editId)" class="space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="_modal" value="edit">
            <input type="hidden" name="_edit_id" :value="editId">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Siswa <span class="text-ich-error">*</span></label>
                    <input type="text" name="nama_siswa" x-model="e.nama_siswa" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">NIS <span class="text-ich-error">*</span></label>
                    <input type="text" name="NIS" x-model="e.NIS" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Kelas <span class="text-ich-error">*</span></label>
                    <select name="class_id" x-model="e.class_id" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->class_id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Jenis Kelamin <span class="text-ich-error">*</span></label>
                    <select name="jenis_kelamin" x-model="e.jenis_kelamin" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" x-model="e.tanggal_lahir" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" x-model="e.tempat_lahir" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ayah</label>
                    <input type="text" name="nama_ayah" x-model="e.nama_ayah" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ibu</label>
                    <input type="text" name="nama_ibu" x-model="e.nama_ibu" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Status <span class="text-ich-error">*</span></label>
                <select name="status" x-model="e.status" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="aktif">Aktif</option>
                    <option value="alumni">Alumni</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">Perbarui</button>
                <button type="button" @click="showEdit = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

    {{-- Modal Detail --}}
    <x-admin-modal show="showDetail" title="Detail Siswa" maxWidth="md">
        <div class="space-y-3">
            <template x-for="[label, key] in [['NIS','NIS'],['Nama Siswa','nama_siswa'],['Kelas','kelas'],['Jenis Kelamin','jenis_kelamin'],['Status','status'],['Tanggal Lahir','tanggal_lahir'],['Tempat Lahir','tempat_lahir'],['Nama Ayah','nama_ayah'],['Nama Ibu','nama_ibu']]">
                <div class="flex items-start gap-4 py-2 border-b border-ich-line last:border-0">
                    <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0" x-text="label"></div>
                    <div class="font-sans text-sm text-ich-ink-900" x-text="detail[key] || '-'"></div>
                </div>
            </template>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="button" @click="showDetail = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Tutup</button>
        </div>
    </x-admin-modal>

    {{-- Modal Delete --}}
    <x-admin-modal show="showDelete" title="Konfirmasi Hapus" maxWidth="sm">
        <p class="text-sm text-ich-ink-600 mb-4">Yakin ingin menghapus siswa <strong x-text="deleteName"></strong>?</p>
        <form method="POST" :action="'{{ route('admin.siswa.destroy', ':id') }}'.replace(':id', deleteId)">
            @csrf @method('DELETE')
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-ich-error text-white font-ui font-bold text-sm rounded-ich-lg hover:opacity-90 transition-opacity">Hapus</button>
                <button type="button" @click="showDelete = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

</div>
</x-main-layout>
