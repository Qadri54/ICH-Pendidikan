<x-main-layout title="Tabungan Siswa">

    <div class="mb-6">
        <h1 class="text-2xl font-display font-bold text-ich-ink-900">Tabungan Siswa</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">Ledger tabungan yang Anda kelola</p>
    </div>

    @if($ledgers->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card p-10 text-center flex flex-col items-center justify-center border-2 border-dashed border-ich-line">
            <div class="w-20 h-20 bg-ich-surface rounded-full flex items-center justify-center mb-5 text-ich-ink-400">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="font-display font-bold text-xl text-ich-ink-900 mb-2">Buku Kas Belum Dibuat</h2>
            <p class="text-sm font-sans text-ich-ink-500 mb-6 max-w-sm mx-auto leading-relaxed">
                Anda belum ditugaskan untuk mengelola tabungan manapun. Admin sekolah perlu membuatkan <strong>Ledger/Buku Kas</strong> untuk Anda terlebih dahulu.
            </p>
            <div class="px-4 py-2 bg-ich-blue-soft text-ich-teal font-ui text-xs font-semibold rounded-lg">
                Silakan hubungi Admin Sekolah
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-ich-surface">
                        <tr>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Ledger</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Periode</th>
                            <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Total Saldo</th>
                            <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Status</th>
                            <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ich-line">
                        @foreach($ledgers as $ledger)
                            @php
                                $isActive = $ledger->status === 'Active';
                            @endphp
                            <tr class="hover:bg-ich-surface transition-colors">
                                <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                    {{ $ledger->ledger_name }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($ledger->classRoom)
                                        <span class="px-2 py-1 bg-ich-blue-soft text-ich-teal font-ui font-bold text-xs rounded-full">{{ $ledger->classRoom->nama_kelas }}</span>
                                    @else
                                        <span class="text-ich-ink-300 text-xs">Semua</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-ich-ink-600">
                                    @if($ledger->academicPeriod)
                                        {{ $ledger->academicPeriod->tahun_ajaran }} — Smt {{ $ledger->academicPeriod->semester }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-ui font-semibold text-ich-green">
                                    Rp {{ number_format($ledger->total_balance, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-ui font-bold
                                        {{ $isActive ? 'bg-ich-success-soft text-ich-success' : 'bg-ich-surface text-ich-ink-400' }}">
                                        {{ $isActive ? 'Aktif' : 'Ditutup' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('guru.tabungan.show', $ledger) }}"
                                       class="text-xs font-ui font-bold text-ich-teal hover:underline">
                                        Kelola →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</x-main-layout>
