<x-mobile-layout title="SPP Bulanan" page-title="SPP Bulanan">

    <div class="mb-4">
        <a href="{{ route('pembayaran') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">&larr; Kembali</a>
    </div>

    @if($data->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card p-6 text-center">
            <p class="font-sans text-sm text-ich-ink-400">Belum ada siswa terdaftar.</p>
        </div>
    @else
        <div class="space-y-3">
        @foreach($data as $item)
            @php
                $student      = $item['student'];
                $invoices     = $item['invoices'];
                $overdueCount = $item['overdueCount'];
                $pendingCount = $item['pendingCount'];
                $unpaidCount  = $item['unpaidCount'];
                $totalUnpaid  = $item['totalUnpaid'];
                $totalActive  = $invoices->count();
            @endphp

            <a href="{{ route('pembayaran.spp.detail', $student->student_id) }}"
               class="block bg-white rounded-xl shadow-ich-card p-5 hover:shadow-md transition-shadow active:scale-[0.98]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-ich-green-surface flex items-center justify-center shrink-0">
                        <span class="font-display font-bold text-lg text-ich-green">
                            {{ strtoupper(substr($student->nama_siswa, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-ui font-bold text-sm text-ich-ink-900 truncate">
                            {{ $student->nama_siswa }}
                        </p>

                        @if($totalActive === 0)
                            <p class="font-sans text-xs text-ich-ink-400 mt-0.5">Tidak ada tagihan aktif</p>
                        @else
                            <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                {{ $totalActive }} tagihan &middot; Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                            </p>
                        @endif

                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @if($overdueCount > 0)
                                <span class="px-2 py-0.5 bg-ich-error-soft text-ich-error text-xs font-ui font-bold rounded-full">
                                    {{ $overdueCount }} terlambat
                                </span>
                            @endif
                            @if($pendingCount > 0)
                                <span class="px-2 py-0.5 bg-ich-purple-soft text-ich-purple text-xs font-ui font-bold rounded-full">
                                    {{ $pendingCount }} menunggu
                                </span>
                            @endif
                            @if($unpaidCount > 0)
                                <span class="px-2 py-0.5 bg-ich-warning-soft text-ich-warning text-xs font-ui font-bold rounded-full">
                                    {{ $unpaidCount }} belum bayar
                                </span>
                            @endif
                            @if($totalActive === 0)
                                <span class="px-2 py-0.5 bg-ich-success-soft text-ich-success text-xs font-ui font-bold rounded-full">
                                    Tidak ada tagihan
                                </span>
                            @endif
                        </div>
                    </div>
                    <x-ich-icon name="chevron_right" :size="20" color="#99A1AF"/>
                </div>
            </a>
        @endforeach
        </div>
    @endif

</x-mobile-layout>
