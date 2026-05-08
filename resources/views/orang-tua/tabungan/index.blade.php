<x-mobile-layout title="Tabungan" page-title="Tabungan">

    @if($tabunganData->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card p-8 text-center">
            <x-ich-icon name="piggy" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-sm text-ich-ink-900 mb-1">Belum Ada Data Tabungan</p>
            <p class="font-sans text-xs text-ich-ink-400">
                Data tabungan anak Anda belum tersedia. Hubungi pihak sekolah untuk informasi lebih lanjut.
            </p>
        </div>
    @else
        <div class="space-y-6">
        @foreach($tabunganData as $data)
            @php $student = $data['student']; @endphp

            <div>
                <h2 class="font-display font-bold text-base text-ich-ink-900 mb-3">
                    {{ $student->nama_siswa }}
                </h2>

                @if($data['passbooks']->isEmpty())
                    <div class="bg-white rounded-xl shadow-ich-card px-5 py-4 text-center">
                        <p class="font-sans text-sm text-ich-ink-400">
                            Belum ada buku tabungan untuk anak ini.
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                    @foreach($data['passbooks'] as $item)
                        @php
                            $pb  = $item['passbook'];
                            $txs = $item['transactions'];
                        @endphp

                        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                            {{-- Header buku --}}
                            <div class="bg-ich-teal px-5 py-4">
                                <p class="text-white/70 text-xs font-sans">{{ $pb->ledger->ledger_name }}</p>
                                <p class="text-2xl font-display font-bold text-white mt-0.5">
                                    Rp {{ number_format($pb->current_balance, 0, ',', '.') }}
                                </p>
                                <p class="text-white/60 text-xs font-sans mt-1">
                                    Dibuka {{ $pb->opening_date->translatedFormat('d F Y') }}
                                </p>
                            </div>

                            {{-- Riwayat transaksi --}}
                            @if($txs->isNotEmpty())
                                <div class="divide-y divide-ich-line">
                                    @foreach($txs as $trx)
                                        @php $isDeposit = $trx->transaction_type === 'deposit'; @endphp
                                        <div class="px-5 py-3 flex items-center justify-between">
                                            <div>
                                                <p class="font-ui font-semibold text-sm text-ich-ink-900">
                                                    {{ $isDeposit ? 'Setoran' : 'Penarikan' }}
                                                </p>
                                                <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                                    {{ $trx->transaction_date->translatedFormat('d F Y') }}
                                                    @if($trx->description)
                                                        · {{ $trx->description }}
                                                    @endif
                                                </p>
                                            </div>
                                            <span class="font-ui font-bold text-sm
                                                {{ $isDeposit ? 'text-[#009966]' : 'text-ich-error' }}">
                                                {{ $isDeposit ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-5 py-4 text-center">
                                    <p class="font-sans text-xs text-ich-ink-400">Belum ada riwayat transaksi.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                    </div>
                @endif
            </div>

        @endforeach
        </div>
    @endif

</x-mobile-layout>
