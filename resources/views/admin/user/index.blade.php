@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Manajemen User">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Manajemen User</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Total: {{ $users->total() }} akun</p>
        </div>
        @if(! $isReadOnly)
            <a href="{{ route('admin.user.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-ich-green text-white
                      font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                + Tambah User
            </a>
        @endif
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama / email..."
               class="flex-1 min-w-[180px] max-w-xs h-10 px-3.5 bg-white border border-ich-line rounded-ich-md
                      font-sans text-sm focus:outline-none focus:border-ich-teal">
        <select name="role"
                class="h-10 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm
                       focus:outline-none focus:border-ich-teal">
            <option value="">Semua Role</option>
            @foreach($roles as $r)
                <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>
                    {{ $r }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="h-10 px-4 bg-ich-teal text-white font-ui font-bold text-sm rounded-ich-md hover:bg-ich-teal-dark transition-colors">
            Cari
        </button>
        @if(request()->hasAny(['search', 'role']))
            <a href="{{ route('admin.user.index') }}"
               class="h-10 px-4 flex items-center bg-white border border-ich-line text-ich-ink-500
                      font-ui text-sm rounded-ich-md hover:bg-gray-50 transition-colors">
                Reset
            </a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-ich-green text-white">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold">Nama</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Email</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">No HP</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Role</th>
                    <th class="px-4 py-3 text-center font-ui font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($users as $u)
                    @php
                        $roleName = $u->role?->role_name ?? 'Tidak ada';
                        $roleColor = match($roleName) {
                            'Admin'                        => 'bg-[#FEE2E2] text-ich-error',
                            'Guru', 'Guru Ngaji'           => 'bg-[#E8F5EA] text-ich-green',
                            'Orang Tua'                    => 'bg-[#F4F7FC] text-ich-teal',
                            'Kepala Sekolah','Kepala Yayasan' => 'bg-[#EDE9FE] text-[#8B5CF6]',
                            default                        => 'bg-[#F3F4F6] text-ich-ink-500',
                        };
                    @endphp
                    <tr class="hover:bg-[#F5F6FA]">
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-ich-ink-500">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $u->no_hp ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 font-ui font-bold text-xs rounded-full {{ $roleColor }}">
                                {{ $roleName }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                @if(! $isReadOnly)
                                    <a href="{{ route('admin.user.edit', $u) }}"
                                       class="px-2.5 py-1 bg-[#FEF5DC] text-[#E09F17] font-ui font-bold text-xs rounded hover:bg-ich-yellow hover:text-white transition-colors">
                                        Edit
                                    </a>
                                    @if($u->user_id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.user.destroy', $u) }}"
                                              onsubmit="return confirm('Hapus user {{ $u->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="px-2.5 py-1 bg-[#FEE2E2] text-ich-error font-ui font-bold text-xs rounded hover:bg-ich-error hover:text-white transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                            Belum ada data user.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>

</x-main-layout>
