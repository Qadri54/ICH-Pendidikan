<x-main-layout title="Kelola Tabungan">

    <div class="mb-6">
        <a href="{{ route('guru.tabungan.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">{{ $ledger->ledger_name }}</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">
            Tahun Akademik {{ $ledger->academic_year ? \Carbon\Carbon::parse($ledger->academic_year)->format('Y') : '-' }}
            · Total Saldo: <span class="font-semibold text-ich-green">Rp {{ number_format($ledger->total_balance, 0, ',', '.') }}</span>
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-[#FEE2E2] text-ich-error rounded-lg text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F6FA]">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                        <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Saldo Terkini</th>
                        <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($passbooks as $pb)
                        <tr class="hover:bg-[#F5F6FA]">
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                {{ $pb->student?->nama_siswa ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($pb->student?->classRoom)
                                    <span class="px-2 py-1 bg-[#E8F5EA] text-ich-green font-ui font-bold text-xs rounded-full">
                                        {{ $pb->student->classRoom->nama_kelas }}
                                    </span>
                                @else
                                    <span class="text-ich-ink-300 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-ui font-semibold text-ich-green">
                                Rp {{ number_format($pb->current_balance, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('guru.tabungan.passbook.show', $pb) }}"
                                   class="text-xs font-ui font-bold text-ich-teal hover:underline">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                Belum ada buku tabungan siswa di ledger ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-main-layout>
