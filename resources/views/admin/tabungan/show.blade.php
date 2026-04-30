<x-main-layout title="Detail Ledger Tabungan">

    <div class="mb-6">
        <a href="{{ route('admin.tabungan.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">{{ $tabungan->ledger_name }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-xl shadow-ich-card p-6 space-y-3">
            <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3">Info Ledger</h2>
            @foreach([
                ['Guru PJ',     $tabungan->teacher?->user?->name ?? '-'],
                ['Th. Akademik', $tabungan->academic_year ? \Carbon\Carbon::parse($tabungan->academic_year)->format('Y') : '-'],
                ['Saldo Awal',  'Rp '.number_format($tabungan->opening_balance, 0, ',', '.')],
                ['Total Saldo', 'Rp '.number_format($tabungan->total_balance, 0, ',', '.')],
                ['Status',      $tabungan->status],
            ] as [$label, $value])
                <div class="flex gap-3 py-1.5 border-b border-ich-line last:border-0">
                    <div class="w-28 font-ui font-bold text-xs text-ich-ink-400 shrink-0">{{ $label }}</div>
                    <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="px-5 py-4 border-b border-ich-line">
                <h2 class="font-ui font-bold text-ich-ink-900">Daftar Tabungan Siswa</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-[#F5F6FA]">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Siswa</th>
                        <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($tabungan->passbooks as $pb)
                        <tr>
                            <td class="px-4 py-3 font-sans text-ich-ink-900">{{ $pb->student?->nama_siswa ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-ui font-semibold text-ich-green">
                                Rp {{ number_format($pb->balance ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-ich-ink-300 font-sans">
                                Belum ada data tabungan siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-main-layout>
