<x-main-layout title="Daftar Kelas">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Daftar Kelas</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Total: {{ $kelas->total() }} kelas</p>
        </div>
        <a href="{{ route('admin.kelas.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-ich-green text-white
                  font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
            + Tambah Kelas
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-ich-green text-white">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold">Nama Kelas</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Ruangan</th>
                    <th class="px-4 py-3 text-center font-ui font-bold">Jumlah Siswa</th>
                    <th class="px-4 py-3 text-center font-ui font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($kelas as $k)
                    <tr class="hover:bg-[#F5F6FA]">
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $k->nama_kelas }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $k->nama_ruangan }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 bg-[#F4F7FC] text-ich-teal font-ui font-bold text-xs rounded-full">
                                {{ $k->students_count }} siswa
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.kelas.edit', $k) }}"
                                   class="px-2.5 py-1 bg-[#FEF5DC] text-[#E09F17] font-ui font-bold text-xs rounded hover:bg-ich-yellow hover:text-white transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.kelas.destroy', $k) }}"
                                      onsubmit="return confirm('Hapus kelas {{ $k->nama_kelas }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="px-2.5 py-1 bg-[#FEE2E2] text-ich-error font-ui font-bold text-xs rounded hover:bg-ich-error hover:text-white transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                            Belum ada data kelas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $kelas->links() }}</div>

</x-main-layout>
