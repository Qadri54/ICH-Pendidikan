<x-main-layout title="Pendaftaran Siswa">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Pendaftaran Siswa</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">PPDB — Total: {{ $pendaftaran->total() }}</p>
        </div>
    </div>

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama..."
               class="flex-1 max-w-xs h-10 px-3.5 bg-white border border-ich-line rounded-ich-md
                      font-sans text-sm focus:outline-none focus:border-ich-teal">
        <select name="status"
                class="h-10 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none">
            <option value="">Semua Status</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Menunggu</option>
            <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Diterima</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
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
                    <th class="px-4 py-3 text-left font-ui font-bold">Nama Orang Tua</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Email</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Nama Anak</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Jenis</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Tanggal Daftar</th>
                    <th class="px-4 py-3 text-center font-ui font-bold">Status</th>
                    <th class="px-4 py-3 text-center font-ui font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($pendaftaran as $p)
                    @php
                        $statusColor = match($p->status) {
                            'accepted' => 'bg-[#D1FAE5] text-[#009966]',
                            'rejected' => 'bg-[#FEE2E2] text-ich-error',
                            default    => 'bg-[#FEF5DC] text-[#E09F17]',
                        };
                        $statusLabel = match($p->status) {
                            'accepted' => 'Diterima',
                            'rejected' => 'Ditolak',
                            default    => 'Menunggu',
                        };
                    @endphp
                    <tr class="hover:bg-[#F5F6FA]">
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $p->user?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-ich-ink-500">{{ $p->user?->email ?? '-' }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $p->nama_siswa ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php $jenisBg = $p->jenis_pendaftaran === 'TK' ? 'bg-[#EDE9FE] text-[#8B5CF6]' : 'bg-[#FEF5DC] text-[#E09F17]'; @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-ui font-bold {{ $jenisBg }}">
                                {{ $p->jenis_pendaftaran === 'TK' ? 'TK' : 'Mengaji' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-ich-ink-500">{{ $p->created_at?->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 font-ui font-bold text-xs rounded-full {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.pendaftaran.show', $p) }}"
                               class="px-2.5 py-1 bg-[#F4F7FC] text-ich-teal font-ui font-bold text-xs rounded hover:bg-ich-teal hover:text-white transition-colors">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                            Belum ada data pendaftaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $pendaftaran->links() }}</div>

</x-main-layout>
