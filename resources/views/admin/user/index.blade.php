@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Manajemen User">
<div x-data="{
    showCreate: {{ $errors->any() && old('_modal') === 'create' ? 'true' : 'false' }},
    showEdit: {{ $errors->any() && old('_modal') === 'edit' ? 'true' : 'false' }},
    showDelete: false,
    editId: '{{ old('_edit_id', '') }}',
    editName: '{{ old('name', '') }}',
    editEmail: '{{ old('email', '') }}',
    editNoHp: '{{ old('no_hp', '') }}',
    editRole: '{{ old('role_name', '') }}',
    deleteId: null,
    deleteName: '',
    openEdit(u) {
        this.editId = u.user_id;
        this.editName = u.name;
        this.editEmail = u.email;
        this.editNoHp = u.no_hp || '';
        this.editRole = u.role_name || '';
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
            <div class="w-11 h-11 rounded-xl bg-ich-blue-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Manajemen User</h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">Total: {{ $users->total() }} akun</p>
            </div>
        </div>
        @if(! $isReadOnly)
            <button @click="showCreate = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-ich-green text-white
                           font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                + Tambah User
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">{{ session('error') }}</div>
    @endif

    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..."
               class="flex-1 min-w-[180px] max-w-xs h-10 px-3.5 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none focus:border-ich-teal">
        <select name="role" class="h-10 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none focus:border-ich-teal">
            <option value="">Semua Role</option>
            @foreach($roles as $r)
                <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
        </select>
        <button type="submit" class="h-10 px-4 bg-ich-teal text-white font-ui font-bold text-sm rounded-ich-md hover:bg-ich-teal-dark transition-colors">Cari</button>
        @if(request()->hasAny(['search', 'role']))
            <a href="{{ route('admin.user.index') }}" class="h-10 px-4 flex items-center bg-white border border-ich-line text-ich-ink-500 font-ui text-sm rounded-ich-md hover:bg-gray-50 transition-colors">Reset</a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-ich-surface">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Email</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">No HP</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Role</th>
                    <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($users as $u)
                    @php
                        $roleName = $u->role?->role_name ?? 'Tidak ada';
                        $roleColor = match($roleName) {
                            'Admin'                        => 'bg-ich-error-soft text-ich-error',
                            'Guru', 'Guru Ngaji'           => 'bg-ich-green-surface text-ich-green',
                            'Orang Tua'                    => 'bg-ich-info-soft text-ich-teal',
                            'Kepala Sekolah','Kepala Yayasan' => 'bg-ich-purple-soft text-ich-purple',
                            default                        => 'bg-gray-100 text-ich-ink-500',
                        };
                    @endphp
                    <tr class="hover:bg-ich-surface transition-colors">
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-ich-ink-500">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $u->no_hp ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 font-ui font-bold text-xs rounded-full {{ $roleColor }}">{{ $roleName }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                @if(! $isReadOnly)
                                    <button @click="openEdit({{ Js::from(['user_id' => $u->user_id, 'name' => $u->name, 'email' => $u->email, 'no_hp' => $u->no_hp, 'role_name' => $roleName]) }})"
                                            class="px-2.5 py-1 bg-ich-warning-soft text-ich-warning font-ui font-bold text-xs rounded hover:bg-ich-yellow hover:text-white transition-colors">
                                        Edit
                                    </button>
                                    @if($u->user_id !== auth()->id())
                                        <button @click="openDelete('{{ $u->user_id }}', '{{ $u->name }}')"
                                                class="px-2.5 py-1 bg-ich-error-soft text-ich-error font-ui font-bold text-xs rounded hover:bg-ich-error hover:text-white transition-colors">
                                            Hapus
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-ich-ink-300 font-sans">Belum ada data user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>

    {{-- Modal Create --}}
    <x-admin-modal show="showCreate" title="Tambah User" maxWidth="md">
        <form method="POST" action="{{ route('admin.user.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create">

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama <span class="text-ich-error">*</span></label>
                <input type="text" name="name" value="{{ old('_modal') === 'create' ? old('name') : '' }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                @error('name') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Email <span class="text-ich-error">*</span></label>
                <input type="email" name="email" value="{{ old('_modal') === 'create' ? old('email') : '' }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                @error('email') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">No HP <span class="text-ich-error">*</span></label>
                <input type="tel" name="no_hp" value="{{ old('_modal') === 'create' ? old('no_hp') : '' }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                @error('no_hp') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Role <span class="text-ich-error">*</span></label>
                <select name="role_name" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">-- Pilih Role --</option>
                    @foreach($createRoles as $r)
                        <option value="{{ $r }}" {{ old('role_name') === $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
                @error('role_name') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Password <span class="text-ich-error">*</span></label>
                <input type="password" name="password" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                @error('password') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Konfirmasi Password <span class="text-ich-error">*</span></label>
                <input type="password" name="password_confirmation" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">Simpan</button>
                <button type="button" @click="showCreate = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

    {{-- Modal Edit --}}
    <x-admin-modal show="showEdit" title="Edit User" maxWidth="md">
        <form method="POST" :action="'{{ route('admin.user.update', ':id') }}'.replace(':id', editId)" class="space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="_modal" value="edit">
            <input type="hidden" name="_edit_id" :value="editId">

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama <span class="text-ich-error">*</span></label>
                <input type="text" name="name" x-model="editName" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                @if(old('_modal') === 'edit') @error('name') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror @endif
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Email <span class="text-ich-error">*</span></label>
                <input type="email" name="email" x-model="editEmail" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                @if(old('_modal') === 'edit') @error('email') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror @endif
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">No HP</label>
                <input type="tel" name="no_hp" x-model="editNoHp" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Role <span class="text-ich-error">*</span></label>
                <select name="role_name" x-model="editRole" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @foreach($createRoles as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Password Baru <span class="text-ich-ink-300 font-normal">(kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">Perbarui</button>
                <button type="button" @click="showEdit = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

    {{-- Modal Delete --}}
    <x-admin-modal show="showDelete" title="Konfirmasi Hapus" maxWidth="sm">
        <p class="text-sm text-ich-ink-600 mb-4">Yakin ingin menghapus user <strong x-text="deleteName"></strong>?</p>
        <form method="POST" :action="'{{ route('admin.user.destroy', ':id') }}'.replace(':id', deleteId)">
            @csrf @method('DELETE')
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-ich-error text-white font-ui font-bold text-sm rounded-ich-lg hover:opacity-90 transition-opacity">Hapus</button>
                <button type="button" @click="showDelete = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

</div>
</x-main-layout>
