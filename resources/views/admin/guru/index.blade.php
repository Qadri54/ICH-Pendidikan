@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Daftar Guru">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Daftar Guru</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Guru Kelas & Guru Ngaji</p>
        </div>
        @if(! $isReadOnly)
            <a href="{{ route('admin.guru.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-ich-green text-white
                      font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                + Tambah Guru
            </a>
        @endif
    </div>

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama / NIP..."
               class="flex-1 max-w-xs h-10 px-3.5 bg-white border border-ich-line rounded-ich-md
                      font-sans text-sm focus:outline-none focus:border-ich-teal">
        <button type="submit"
                class="h-10 px-4 bg-ich-teal text-white font-ui font-bold text-sm rounded-ich-md hover:bg-ich-teal-dark">
            Cari
        </button>
    </form>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-ich-green text-white">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold">Tipe</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Nama</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">NIP</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Mata Pelajaran</th>
                    <th class="px-4 py-3 text-center font-ui font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($guru as $g)
                    <tr class="hover:bg-[#F5F6FA]">
                        <td class="px-4 py-3">
                            @if($g->tipe === 'Guru Kelas')
                                <span class="px-2 py-1 bg-[#E8F5EA] text-ich-green font-ui font-bold text-xs rounded-full">Guru Kelas</span>
                            @else
                                <span class="px-2 py-1 bg-[#EDE9FE] text-[#8B5CF6] font-ui font-bold text-xs rounded-full">Guru Ngaji</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $g->nama }}</td>
                        <td class="px-4 py-3 text-ich-ink-500">{{ $g->NIP ?: '-' }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $g->info }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                @if(! $isReadOnly)
                                    <a href="{{ route('admin.guru.edit', $g->id) }}"
                                       class="px-2.5 py-1 bg-[#FEF5DC] text-[#E09F17] font-ui font-bold text-xs rounded hover:bg-ich-yellow hover:text-white transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.guru.destroy', $g->id) }}"
                                          onsubmit="return confirm('Hapus guru {{ $g->nama }}?')">
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
                        <td colspan="5" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                            Belum ada data guru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

</x-main-layout>
