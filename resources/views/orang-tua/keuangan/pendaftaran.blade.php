<x-mobile-layout title="Biaya Pendaftaran" page-title="Biaya Pendaftaran">

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
                $student = $item['student'];
                $fee     = $item['fee'];

                if ($fee) {
                    $totalPaid = $fee->transactions->where('status', 'approved')->sum('jumlah_bayar');
                    $remaining = max(0, $fee->total_jumlah - $totalPaid);
                    $feeCfg = match($fee->status) {
                        'paid'         => ['label' => 'Lunas',       'bg' => 'bg-ich-success-soft', 'text' => 'text-ich-success'],
                        'installments' => ['label' => 'Cicilan',     'bg' => 'bg-ich-purple-soft',  'text' => 'text-ich-purple'],
                        default        => ['label' => 'Belum Bayar', 'bg' => 'bg-ich-warning-soft', 'text' => 'text-ich-warning'],
                    };
                }
            @endphp

            <a href="{{ route('pembayaran.pendaftaran.detail', $student->student_id) }}"
               class="block bg-white rounded-xl shadow-ich-card p-5 hover:shadow-md transition-shadow active:scale-[0.98]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-ich-purple-soft flex items-center justify-center shrink-0">
                        <span class="font-display font-bold text-lg text-ich-purple">
                            {{ strtoupper(substr($student->nama_siswa, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-ui font-bold text-sm text-ich-ink-900 truncate">
                            {{ $student->nama_siswa }}
                        </p>

                        @if(! $fee)
                            <p class="font-sans text-xs text-ich-ink-400 mt-0.5">Belum ditagihkan</p>
                        @else
                            <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                Rp {{ number_format($fee->total_jumlah, 0, ',', '.') }}
                                @if($remaining > 0)
                                    &middot; Sisa Rp {{ number_format($remaining, 0, ',', '.') }}
                                @endif
                            </p>
                            <div class="mt-2">
                                <span class="px-2 py-0.5 rounded-full text-xs font-ui font-bold {{ $feeCfg['bg'] }} {{ $feeCfg['text'] }}">
                                    {{ $feeCfg['label'] }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <x-ich-icon name="chevron_right" :size="20" color="#99A1AF"/>
                </div>
            </a>
        @endforeach
        </div>
    @endif

</x-mobile-layout>
