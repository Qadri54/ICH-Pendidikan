<x-mobile-layout title="Pendaftaran {{ $student->nama_siswa }}" page-title="Biaya Pendaftaran">

    <div class="mb-4">
        <a href="{{ route('pembayaran.pendaftaran.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">&larr; Kembali</a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header siswa --}}
    <div class="bg-white rounded-xl shadow-ich-card p-5 mb-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-ich-purple-soft flex items-center justify-center shrink-0">
                <span class="font-display font-bold text-lg text-ich-purple">
                    {{ strtoupper(substr($student->nama_siswa, 0, 1)) }}
                </span>
            </div>
            <div>
                <p class="font-display font-bold text-base text-ich-ink-900">{{ $student->nama_siswa }}</p>
                <p class="font-sans text-xs text-ich-ink-400 mt-0.5">Biaya Pendaftaran</p>
            </div>
        </div>
    </div>

    @if(! $fee)
        <div class="bg-white rounded-xl shadow-ich-card px-5 py-6 text-center">
            <x-ich-icon name="document" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-sm text-ich-ink-900">Belum Ada Tagihan</p>
            <p class="font-sans text-xs text-ich-ink-400 mt-1">Biaya pendaftaran belum ditagihkan oleh admin.</p>
        </div>
    @else
        @php
            $totalPaid  = $fee->transactions->where('status', 'approved')->sum('jumlah_bayar');
            $remaining  = max(0, $fee->total_jumlah - $totalPaid);
            $feeCfg     = match($fee->status) {
                'paid'         => ['label' => 'Lunas',       'bg' => 'bg-ich-success-soft', 'text' => 'text-ich-success'],
                'installments' => ['label' => 'Cicilan',     'bg' => 'bg-ich-purple-soft',  'text' => 'text-ich-purple'],
                default        => ['label' => 'Belum Bayar', 'bg' => 'bg-ich-warning-soft', 'text' => 'text-ich-warning'],
            };
            $rejectedTx   = $fee->transactions->where('status', 'rejected')->last();
            $installments = $fee->installments->sortBy('tanggal_jatuh_tempo');
        @endphp

        {{-- Ringkasan tagihan --}}
        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden mb-3">
            <div class="px-5 py-4 border-b border-ich-line flex items-center justify-between">
                <p class="font-ui font-bold text-sm text-ich-ink-900">Biaya Pendaftaran</p>
                <span class="px-3 py-1 rounded-full text-xs font-ui font-bold {{ $feeCfg['bg'] }} {{ $feeCfg['text'] }}">
                    {{ $feeCfg['label'] }}
                </span>
            </div>
            <div class="grid grid-cols-3 divide-x divide-ich-line">
                <div class="px-4 py-3 text-center">
                    <p class="font-sans text-xs text-ich-ink-400 mb-1">Total Tagihan</p>
                    <p class="font-ui font-bold text-sm text-ich-ink-900">
                        Rp {{ number_format($fee->total_jumlah, 0, ',', '.') }}
                    </p>
                </div>
                <div class="px-4 py-3 text-center">
                    <p class="font-sans text-xs text-ich-ink-400 mb-1">Dibayar</p>
                    <p class="font-ui font-bold text-sm text-ich-success">
                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                    </p>
                </div>
                <div class="px-4 py-3 text-center">
                    <p class="font-sans text-xs text-ich-ink-400 mb-1">Sisa</p>
                    <p class="font-ui font-bold text-sm {{ $remaining > 0 ? 'text-ich-warning' : 'text-ich-success' }}">
                        {{ $remaining > 0 ? 'Rp '.number_format($remaining, 0, ',', '.') : 'Lunas' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Status / Pilihan Pembayaran --}}
        @if($fee->status === 'paid')
            <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                <div class="px-5 py-4 flex items-center gap-2 text-ich-success">
                    <x-ich-icon name="check_circle" :size="18" color="#009966"/>
                    <span class="font-ui font-semibold text-sm">Biaya pendaftaran telah lunas</span>
                </div>
            </div>

        @elseif($pendingTx)
            <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                <div class="px-5 py-4 flex items-center gap-2 text-ich-warning">
                    <x-ich-icon name="clock" :size="18" color="#E09F17"/>
                    <span class="font-ui font-semibold text-sm">Bukti dikirim, menunggu konfirmasi admin</span>
                </div>
            </div>

        @else
            <div x-data="{ mode: null, submitting: false }" class="space-y-3">

                @if($rejectedTx?->rejection_reason)
                    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden p-4">
                        <div class="p-3 bg-[#FEF2F2] rounded-lg">
                            <p class="font-ui font-bold text-xs text-ich-error mb-1">Pembayaran sebelumnya ditolak:</p>
                            <p class="font-sans text-xs text-ich-ink-900">{{ $rejectedTx->rejection_reason }}</p>
                        </div>
                    </div>
                @endif

                {{-- Pilihan metode pembayaran --}}
                <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                    <div class="px-5 py-4 border-b border-ich-line">
                        <p class="font-ui font-bold text-sm text-ich-ink-900">Pilih Metode Pembayaran</p>
                        <p class="font-sans text-xs text-ich-ink-400 mt-0.5">Pilih salah satu metode untuk melanjutkan</p>
                    </div>
                    <div class="p-4 grid grid-cols-2 gap-3">
                        {{-- Pelunasan --}}
                        <button @click="mode = 'full'"
                                :class="mode === 'full'
                                    ? 'border-ich-green bg-ich-green-surface'
                                    : 'border-ich-line bg-white hover:border-ich-ink-300'"
                                class="border-2 rounded-xl p-4 text-center transition-all">
                            <div class="w-10 h-10 rounded-xl bg-ich-success-soft flex items-center justify-center mx-auto mb-2">
                                <x-ich-icon name="check_circle" :size="20" color="#009966"/>
                            </div>
                            <p class="font-ui font-bold text-sm text-ich-ink-900">Pelunasan</p>
                            <p class="font-sans text-xs text-ich-ink-400 mt-1">
                                Rp {{ number_format($remaining, 0, ',', '.') }}
                            </p>
                        </button>

                        {{-- Cicilan --}}
                        <button @click="mode = 'installment'"
                                :class="mode === 'installment'
                                    ? 'border-ich-purple bg-ich-purple-soft/30'
                                    : 'border-ich-line bg-white hover:border-ich-ink-300'"
                                class="border-2 rounded-xl p-4 text-center transition-all">
                            <div class="w-10 h-10 rounded-xl bg-ich-purple-soft flex items-center justify-center mx-auto mb-2">
                                <x-ich-icon name="document" :size="20" color="#8B5CF6"/>
                            </div>
                            <p class="font-ui font-bold text-sm text-ich-ink-900">Cicilan</p>
                            <p class="font-sans text-xs text-ich-ink-400 mt-1">
                                {{ $installments->where('status', '!=', 'paid')->count() }}x cicilan
                            </p>
                        </button>
                    </div>
                </div>

                {{-- Form Pelunasan --}}
                <div x-show="mode === 'full'" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                    <form method="POST"
                          action="{{ route('pembayaran.pendaftaran', $fee->registration_fee_id) }}"
                          enctype="multipart/form-data"
                          @submit="submitting = true"
                          class="px-5 py-4 space-y-3">
                        @csrf
                        <input type="hidden" name="payment_category" value="full">
                        <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">
                            Pelunasan — Rp {{ number_format($remaining, 0, ',', '.') }}
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Jumlah Bayar</label>
                                <input type="number" name="jumlah_bayar"
                                       value="{{ $remaining }}" readonly
                                       class="w-full h-10 px-3 bg-ich-surface border-2 border-ich-line rounded-ich-lg
                                              font-sans text-sm text-ich-ink-600">
                            </div>
                            <div>
                                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Nama Bank</label>
                                <input type="text" name="nama_bank" value="{{ old('nama_bank') }}"
                                       placeholder="BCA, BRI, dsb" required
                                       class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                              font-sans text-sm focus:outline-none focus:border-ich-teal">
                            </div>
                        </div>
                        <div>
                            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Bukti Transfer</label>
                            <input type="file" name="gambar_bukti_pembayaran"
                                   accept="image/jpg,image/jpeg,image/png" required
                                   class="w-full text-sm text-ich-ink-600 font-sans
                                          file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                          file:text-xs file:font-bold file:font-ui
                                          file:bg-ich-green file:text-white cursor-pointer">
                        </div>
                        <button type="submit" :disabled="submitting"
                                class="w-full h-10 bg-ich-green text-white font-ui font-bold text-sm
                                       rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors
                                       disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!submitting">Kirim Bukti Pelunasan</span>
                            <span x-show="submitting" x-cloak class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengirim...
                            </span>
                        </button>
                    </form>
                </div>

                {{-- Daftar Cicilan --}}
                <div x-show="mode === 'installment'" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-3">

                    @foreach($installments as $idx => $installment)
                        @php
                            $instCfg = match($installment->status) {
                                'paid'    => ['label' => 'Lunas',       'bg' => 'bg-ich-success-soft', 'text' => 'text-ich-success'],
                                'overdue' => ['label' => 'Terlambat',   'bg' => 'bg-ich-error-soft',   'text' => 'text-ich-error'],
                                default   => ['label' => 'Belum Bayar', 'bg' => 'bg-ich-warning-soft', 'text' => 'text-ich-warning'],
                            };
                            $canPay = in_array($installment->status, ['unpaid', 'overdue']);
                        @endphp

                        <div x-data="{ open: false, submitting: false }" class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                            <div class="px-5 py-4 flex items-center justify-between"
                                 @if($canPay) @click="open = !open" role="button" @endif>
                                <div>
                                    <p class="font-ui font-bold text-sm text-ich-ink-900">
                                        Cicilan {{ $idx + 1 }}
                                    </p>
                                    <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                        Rp {{ number_format($installment->jumlah, 0, ',', '.') }}
                                        &middot; Jatuh tempo {{ $installment->tanggal_jatuh_tempo->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-ui font-bold {{ $instCfg['bg'] }} {{ $instCfg['text'] }}">
                                        {{ $instCfg['label'] }}
                                    </span>
                                    @if($canPay)
                                        <x-ich-icon name="chevron_down" :size="16" color="#99A1AF"
                                                    ::class="open ? 'rotate-180 transition-transform' : 'transition-transform'"/>
                                    @endif
                                </div>
                            </div>

                            @if($canPay)
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="border-t border-ich-line">
                                    <form method="POST"
                                          action="{{ route('pembayaran.pendaftaran', $fee->registration_fee_id) }}"
                                          enctype="multipart/form-data"
                                          @submit="submitting = true"
                                          class="px-5 py-4 space-y-3">
                                        @csrf
                                        <input type="hidden" name="payment_category" value="installment">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Jumlah Bayar</label>
                                                <input type="number" name="jumlah_bayar"
                                                       value="{{ $installment->jumlah }}" readonly
                                                       class="w-full h-10 px-3 bg-ich-surface border-2 border-ich-line rounded-ich-lg
                                                              font-sans text-sm text-ich-ink-600">
                                            </div>
                                            <div>
                                                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Nama Bank</label>
                                                <input type="text" name="nama_bank" value="{{ old('nama_bank') }}"
                                                       placeholder="BCA, BRI, dsb" required
                                                       class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                                              font-sans text-sm focus:outline-none focus:border-ich-teal">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Bukti Transfer</label>
                                            <input type="file" name="gambar_bukti_pembayaran"
                                                   accept="image/jpg,image/jpeg,image/png" required
                                                   class="w-full text-sm text-ich-ink-600 font-sans
                                                          file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                                          file:text-xs file:font-bold file:font-ui
                                                          file:bg-ich-green file:text-white cursor-pointer">
                                        </div>
                                        <button type="submit" :disabled="submitting"
                                                class="w-full h-10 bg-ich-teal text-white font-ui font-bold text-sm
                                                       rounded-ich-btn shadow-ich-btn hover:opacity-90 transition-opacity
                                                       disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span x-show="!submitting">Kirim Bukti Cicilan {{ $idx + 1 }}</span>
                                            <span x-show="submitting" x-cloak class="flex items-center justify-center gap-2">
                                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Mengirim...
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

</x-mobile-layout>
