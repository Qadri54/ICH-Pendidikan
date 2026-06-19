<x-mobile-layout title="Tabungan {{ $student->nama_siswa }}" page-title="Tabungan">

    <div class="mb-4">
        <a href="{{ route('tabungan') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">&larr; Kembali</a>
    </div>

    {{-- Student header --}}
    <div class="bg-white rounded-xl shadow-ich-card p-5 mb-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-ich-warning-soft flex items-center justify-center shrink-0">
                <span class="font-display font-bold text-lg text-ich-warning">
                    {{ strtoupper(substr($student->nama_siswa, 0, 1)) }}
                </span>
            </div>
            <div>
                <p class="font-ui font-bold text-sm text-ich-ink-900">{{ $student->nama_siswa }}</p>
                <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                    {{ $student->classRoom?->nama_kelas ?? 'Belum ada kelas' }}
                    @if($student->NIS) · NIS: {{ $student->NIS }} @endif
                </p>
            </div>
        </div>
    </div>

    @if($passbooks->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card px-5 py-6 text-center">
            <x-ich-icon name="piggy" :size="36" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-sm text-ich-ink-600">Belum ada buku tabungan</p>
            <p class="font-sans text-xs text-ich-ink-400 mt-1">
                Buku tabungan akan dibuka oleh guru kelas.
            </p>
        </div>
    @else
        <div class="space-y-4">
        @foreach($passbooks as $item)
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
                                    {{ $isDeposit ? 'text-ich-success' : 'text-ich-error' }}">
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

</x-mobile-layout>
