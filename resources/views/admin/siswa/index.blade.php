@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Daftar Siswa">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Daftar Siswa</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Total: {{ $siswa->total() }} siswa</p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama / NIS..."
               class="flex-1 max-w-xs h-10 px-3.5 bg-white border border-ich-line rounded-ich-md
                      font-sans text-sm text-ich-ink-900 placeholder:text-ich-ink-300
                      focus:outline-none focus:border-ich-teal">
        <select name="kelas"
                class="h-10 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none focus:border-ich-teal">
            <option value="">Semua Kelas</option>
            @foreach($kelas as $k)
                <option value="{{ $k->class_id }}" {{ request('kelas') == $k->class_id ? 'selected' : '' }}>
                    {{ $k->nama_kelas }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="h-10 px-4 bg-ich-teal text-white font-ui font-bold text-sm rounded-ich-md hover:bg-ich-teal-dark transition-colors">
            Cari
        </button>
        @if(request()->hasAny(['search','kelas']))
            <a href="{{ route('admin.siswa.index') }}"
               class="h-10 px-4 flex items-center bg-white border border-ich-line text-ich-ink-500
                      font-ui text-sm rounded-ich-md hover:bg-gray-50 transition-colors">
                Reset
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-ich-green text-white">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold">NIS</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Nama Siswa</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Kelas</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">JK</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Nama Ayah</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Nama Ibu</th>
                    <th class="px-4 py-3 text-center font-ui font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($siswa as $s)
                    <tr class="hover:bg-[#F5F6FA]">
                        <td class="px-4 py-3 font-sans text-ich-ink-500">{{ $s->NIS }}</td>
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $s->nama_siswa }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-[#E8F5EA] text-ich-green font-ui font-bold text-xs rounded-full">
                                {{ $s->classRoom?->nama_kelas ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $s->jenis_kelamin }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $s->nama_ayah }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $s->nama_ibu }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.siswa.show', $s) }}"
                                   class="px-2.5 py-1 bg-[#F4F7FC] text-ich-teal font-ui font-bold text-xs rounded hover:bg-ich-teal hover:text-white transition-colors">
                                    Detail
                                </a>
                                @if(! $isReadOnly)
                                    <a href="{{ route('admin.siswa.edit', $s) }}"
                                       class="px-2.5 py-1 bg-[#FEF5DC] text-[#E09F17] font-ui font-bold text-xs rounded hover:bg-ich-yellow hover:text-white transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.siswa.destroy', $s) }}"
                                          onsubmit="return confirm('Hapus siswa {{ $s->nama_siswa }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="px-2.5 py-1 bg-[#FEE2E2] text-ich-error font-ui font-bold text-xs rounded hover:bg-ich-error hover:text-white transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                            Belum ada data siswa.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">{{ $siswa->links() }}</div>

</x-main-layout>
