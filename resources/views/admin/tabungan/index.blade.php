<x-main-layout title="Tabungan Siswa">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Tabungan Siswa</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Total: {{ $ledgers->total() }} ledger</p>
        </div>
        <a href="{{ route('admin.tabungan.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-ich-green text-white
                  font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
            + Buat Ledger
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-ich-green text-white">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold">Nama Ledger</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Guru PJ</th>
                    <th class="px-4 py-3 text-left font-ui font-bold">Tahun Akademik</th>
                    <th class="px-4 py-3 text-right font-ui font-bold">Total Saldo</th>
                    <th class="px-4 py-3 text-center font-ui font-bold">Status</th>
                    <th class="px-4 py-3 text-center font-ui font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($ledgers as $l)
                    <tr class="hover:bg-[#F5F6FA]">
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $l->ledger_name }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $l->teacher?->user?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-ich-ink-500">
                            {{ $l->academic_year ? \Carbon\Carbon::parse($l->academic_year)->format('Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right font-ui font-semibold text-ich-green">
                            Rp {{ number_format($l->total_balance, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 font-ui font-bold text-xs rounded-full
                                {{ $l->status === 'Aktif' ? 'bg-[#D1FAE5] text-[#009966]' : 'bg-[#F3F4F6] text-ich-ink-500' }}">
                                {{ $l->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.tabungan.show', $l) }}"
                                   class="px-2.5 py-1 bg-[#F4F7FC] text-ich-teal font-ui font-bold text-xs rounded hover:bg-ich-teal hover:text-white transition-colors">
                                    Detail
                                </a>
                                <form method="POST" action="{{ route('admin.tabungan.destroy', $l) }}"
                                      onsubmit="return confirm('Hapus ledger ini?')">
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
                        <td colspan="6" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                            Belum ada data tabungan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $ledgers->links() }}</div>

</x-main-layout>
