<x-main-layout title="Buku Tabungan Siswa">

    <div class="mb-6">
        <a href="{{ route('guru.tabungan.show', $passbook->ledger) }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali ke {{ $passbook->ledger->ledger_name }}</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">
            {{ $passbook->student?->nama_siswa ?? '-' }}
        </h1>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Info + Form --}}
        <div class="space-y-4">
            <div class="bg-ich-green rounded-xl shadow-ich-card p-6 text-white">
                <p class="text-white/70 text-xs font-sans mb-1">Saldo Terkini</p>
                <p class="text-3xl font-display font-bold">
                    Rp {{ number_format($passbook->current_balance, 0, ',', '.') }}
                </p>
                <div class="mt-3 space-y-1.5 text-xs text-white/70 font-sans">
                    <div>Siswa: <span class="text-white font-semibold">{{ $passbook->student?->nama_siswa }}</span></div>
                    <div>Ledger: <span class="text-white font-semibold">{{ $passbook->ledger->ledger_name }}</span></div>
                </div>
            </div>

            @if($passbook->ledger->status === 'Active')
                <div x-data="{ tab: 'deposit' }" class="bg-white rounded-xl shadow-ich-card p-5">
                    <div class="flex gap-2 mb-4">
                        <button @click="tab = 'deposit'" type="button"
                                :class="tab === 'deposit' ? 'bg-ich-green text-white' : 'bg-[#F5F6FA] text-ich-ink-600'"
                                class="flex-1 h-9 rounded-lg font-ui font-bold text-sm transition-colors">
                            Setor
                        </button>
                        <button @click="tab = 'withdraw'" type="button"
                                :class="tab === 'withdraw' ? 'bg-ich-error text-white' : 'bg-[#F5F6FA] text-ich-ink-600'"
                                class="flex-1 h-9 rounded-lg font-ui font-bold text-sm transition-colors">
                            Tarik
                        </button>
                    </div>

                    <form x-show="tab === 'deposit'"
                          method="POST"
                          action="{{ route('guru.tabungan.passbook.deposit', $passbook) }}"
                          class="space-y-3">
                        @csrf
                        <div>
                            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Jumlah Setoran</label>
                            <input type="number" name="amount" min="1000" placeholder="Nominal"
                                   class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm
                                          focus:outline-none focus:border-ich-teal">
                        </div>
                        <div>
                            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Tanggal</label>
                            <input type="date" name="transaction_date" value="{{ now()->format('Y-m-d') }}"
                                   class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm
                                          focus:outline-none focus:border-ich-teal">
                        </div>
                        <div>
                            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Keterangan</label>
                            <input type="text" name="description" placeholder="Opsional"
                                   class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm
                                          focus:outline-none focus:border-ich-teal">
                        </div>
                        <button type="submit"
                                class="w-full h-10 bg-ich-green text-white font-ui font-bold text-sm
                                       rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                            Catat Setoran
                        </button>
                    </form>

                    <form x-show="tab === 'withdraw'"
                          method="POST"
                          action="{{ route('guru.tabungan.passbook.withdraw', $passbook) }}"
                          class="space-y-3">
                        @csrf
                        <div>
                            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Jumlah Penarikan</label>
                            <input type="number" name="amount" min="1000"
                                   max="{{ $passbook->current_balance }}"
                                   placeholder="Maks Rp {{ number_format($passbook->current_balance, 0, ',', '.') }}"
                                   class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm
                                          focus:outline-none focus:border-ich-teal">
                        </div>
                        <div>
                            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Tanggal</label>
                            <input type="date" name="transaction_date" value="{{ now()->format('Y-m-d') }}"
                                   class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm
                                          focus:outline-none focus:border-ich-teal">
                        </div>
                        <div>
                            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Keterangan</label>
                            <input type="text" name="description" placeholder="Opsional"
                                   class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm
                                          focus:outline-none focus:border-ich-teal">
                        </div>
                        <button type="submit"
                                class="w-full h-10 bg-ich-error text-white font-ui font-bold text-sm
                                       rounded-ich-lg shadow-ich-btn hover:opacity-90 transition-opacity">
                            Catat Penarikan
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-[#F5F6FA] rounded-xl p-4 text-center">
                    <p class="font-ui font-bold text-sm text-ich-ink-400">Ledger sudah ditutup</p>
                    <p class="font-sans text-xs text-ich-ink-300 mt-1">Transaksi tidak dapat dilakukan.</p>
                </div>
            @endif
        </div>

        {{-- Riwayat Transaksi --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="px-5 py-4 border-b border-ich-line">
                <h2 class="font-ui font-bold text-ich-ink-900">Riwayat Transaksi</h2>
                <p class="text-xs text-ich-ink-400 mt-0.5">{{ $transactions->count() }} transaksi</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#F5F6FA]">
                        <tr>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">No. Transaksi</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Tanggal</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Jenis</th>
                            <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Nominal</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ich-line">
                        @forelse($transactions as $trx)
                            @php $isDeposit = $trx->transaction_type === 'deposit'; @endphp
                            <tr class="hover:bg-[#F5F6FA] transition-colors">
                                <td class="px-4 py-3 font-sans text-xs text-ich-ink-400">{{ $trx->transaction_number }}</td>
                                <td class="px-4 py-3 font-sans text-ich-ink-600">
                                    {{ $trx->transaction_date->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-ui font-bold
                                        {{ $isDeposit ? 'bg-[#D1FAE5] text-[#009966]' : 'bg-[#FEE2E2] text-ich-error' }}">
                                        {{ $isDeposit ? 'Setoran' : 'Penarikan' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-ui font-semibold
                                           {{ $isDeposit ? 'text-[#009966]' : 'text-ich-error' }}">
                                    {{ $isDeposit ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-ich-ink-500 font-sans text-xs">
                                    {{ $trx->description ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                    Belum ada transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-main-layout>
