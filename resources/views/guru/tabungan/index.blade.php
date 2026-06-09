<x-main-layout title="Tabungan Siswa">

    <div class="mb-6">
        <h1 class="text-2xl font-display font-bold text-ich-ink-900">Tabungan Siswa</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">Ledger tabungan yang Anda kelola</p>
    </div>

    @if($ledgers->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card p-10 text-center">
            <p class="font-ui font-bold text-ich-ink-600">Belum ada ledger tabungan yang di-assign ke Anda.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#F5F6FA]">
                        <tr>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Ledger</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Th. Akademik</th>
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
                            <tr class="hover:bg-[#F5F6FA] transition-colors">
                                <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                    {{ $ledger->ledger_name }}
                                </td>
                                <td class="px-4 py-3 text-ich-ink-600">
                                    {{ $ledger->academic_year ? \Carbon\Carbon::parse($ledger->academic_year)->format('Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-ui font-semibold text-ich-green">
                                    Rp {{ number_format($ledger->total_balance, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-ui font-bold
                                        {{ $isActive ? 'bg-[#D1FAE5] text-[#009966]' : 'bg-[#F5F6FA] text-ich-ink-400' }}">
                                        {{ $ledger->status }}
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
