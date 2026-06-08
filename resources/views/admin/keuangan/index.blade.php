@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Keuangan SPP">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Keuangan SPP</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Total tagihan berjalan:
                <span class="font-bold text-ich-error">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                · {{ $totalLunas }} sudah lunas
            </p>
        </div>
        @if(! $isReadOnly)
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.keuangan.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-ich-teal text-white
                          font-ui font-bold text-sm rounded-ich-lg hover:opacity-90 transition-colors">
                    + Buat Tagihan Manual
                </a>
                <form method="POST" action="{{ route('admin.keuangan.generate') }}"
                      onsubmit="return confirm('Generate tagihan SPP untuk semua siswa bulan ini?')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-ich-green text-white
                                   font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                        + Generate Tagihan Bulan Ini
                    </button>
                </form>
            </div>
        @endif
    </div>

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama siswa..."
               class="flex-1 max-w-xs h-10 px-3.5 bg-white border border-ich-line rounded-ich-md
                      font-sans text-sm focus:outline-none focus:border-ich-teal">
        <select name="status"
                class="h-10 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none">
            <option value="">Semua Status</option>
            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
            <option value="paid"       {{ request('status') === 'paid'       ? 'selected' : '' }}>Lunas</option>
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
                    <th class="px-4 py-3 text-left font-ui font-bold">Siswa</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Kelas</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Bulan/Tahun</th>
                    <th class="px-4 py-3 text-right font-ui font-bold">Jumlah</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Jatuh Tempo</th>
                    <th class="px-4 py-3 text-center font-ui font-bold">Status</th>
                    <th class="px-4 py-3 text-center font-ui font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($invoices as $inv)
                    @php
                        $statusColor = $inv->status === 'Lunas'
                            ? 'bg-[#D1FAE5] text-[#009966]'
                            : 'bg-[#FEE2E2] text-ich-error';
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
                            <span class="px-2.5 py-1 font-ui font-bold text-xs rounded-full {{ $statusColor }}">
                                {{ $inv->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                @if(! $isReadOnly)
                                    <a href="{{ route('admin.keuangan.edit', $inv) }}"
                                       class="px-2.5 py-1 bg-[#FEF5DC] text-[#E09F17] font-ui font-bold text-xs rounded hover:bg-ich-yellow hover:text-white transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.keuangan.destroy', $inv) }}"
                                          onsubmit="return confirm('Hapus tagihan ini?')">
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
                            Belum ada data tagihan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $invoices->links() }}</div>

</x-main-layout>
