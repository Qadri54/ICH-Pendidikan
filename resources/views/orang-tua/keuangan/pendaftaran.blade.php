<x-mobile-layout title="Biaya Pendaftaran" page-title="Biaya Pendaftaran">

    <div class="mb-4">
        <a href="{{ route('pembayaran') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
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

    @if($data->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card p-6 text-center">
            <p class="font-sans text-sm text-ich-ink-400">Belum ada siswa terdaftar.</p>
        </div>
    @else
        <div class="space-y-5">
        @foreach($data as $item)
            @php
                $student   = $item['student'];
                $fee       = $item['fee'];
                $pendingTx = $item['pendingTx'];
            @endphp

            <div>
                <h2 class="font-display font-bold text-base text-ich-ink-900 mb-3">
                    {{ $student->nama_siswa }}
                </h2>

                @if(! $fee)
                    <div class="bg-white rounded-xl shadow-ich-card px-5 py-4">
                        <p class="font-sans text-sm text-ich-ink-400">Biaya pendaftaran belum ditagihkan.</p>
                    </div>
                @else
                    @php
                        $totalPaid = $fee->transactions->where('status', 'approved')->sum('jumlah_bayar');
                        $remaining = max(0, $fee->total_jumlah - $totalPaid);
                        $feeCfg    = match($fee->status) {
                            'paid'         => ['label' => 'Lunas',       'bg' => 'bg-[#D1FAE5]', 'text' => 'text-[#009966]'],
                            'installments' => ['label' => 'Cicilan',     'bg' => 'bg-[#EDE9FE]', 'text' => 'text-[#8B5CF6]'],
                            default        => ['label' => 'Belum Bayar', 'bg' => 'bg-[#FEF5DC]', 'text' => 'text-[#E09F17]'],
                        };
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
                                <p class="font-ui font-bold text-sm text-[#009966]">
                                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="px-4 py-3 text-center">
                                <p class="font-sans text-xs text-ich-ink-400 mb-1">Sisa</p>
                                <p class="font-ui font-bold text-sm {{ $remaining > 0 ? 'text-[#E09F17]' : 'text-[#009966]' }}">
                                    {{ $remaining > 0 ? 'Rp '.number_format($remaining, 0, ',', '.') : 'Lunas' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Status / Form upload --}}
                    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                        @if($fee->status === 'paid')
                            <div class="px-5 py-4 flex items-center gap-2 text-[#009966]">
                                <x-ich-icon name="check_circle" :size="18" color="#009966"/>
                                <span class="font-ui font-semibold text-sm">Biaya pendaftaran telah lunas</span>
                            </div>

                        @elseif($pendingTx)
                            <div class="px-5 py-4 flex items-center gap-2 text-[#E09F17]">
                                <x-ich-icon name="clock" :size="18" color="#E09F17"/>
                                <span class="font-ui font-semibold text-sm">Bukti dikirim, menunggu konfirmasi admin</span>
                            </div>

                        @else
                            <form method="POST"
                                  action="{{ route('pembayaran.pendaftaran', $fee->registration_fee_id) }}"
                                  enctype="multipart/form-data"
                                  class="px-5 py-4 space-y-3">
                                @csrf
                                <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Upload Bukti Transfer</p>

                                @php
                                    $rejectedTx = $fee->transactions->where('status', 'rejected')->last();
                                @endphp
                                @if($rejectedTx?->rejection_reason)
                                    <div class="p-3 bg-[#FEF2F2] rounded-lg">
                                        <p class="font-ui font-bold text-xs text-ich-error mb-1">Pembayaran sebelumnya ditolak:</p>
                                        <p class="font-sans text-xs text-ich-ink-900">{{ $rejectedTx->rejection_reason }}</p>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">
                                            Jumlah Bayar
                                            <span class="font-normal text-ich-ink-400">(maks Rp {{ number_format($remaining, 0, ',', '.') }})</span>
                                        </label>
                                        <input type="number" name="jumlah_bayar"
                                               value="{{ old('jumlah_bayar', $remaining) }}"
                                               max="{{ $remaining }}"
                                               class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                                      font-sans text-sm focus:outline-none focus:border-ich-teal">
                                    </div>
                                    <div>
                                        <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Nama Bank</label>
                                        <input type="text" name="nama_bank" value="{{ old('nama_bank') }}"
                                               placeholder="BCA, BRI, dsb"
                                               class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                                      font-sans text-sm focus:outline-none focus:border-ich-teal">
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Jenis Pembayaran</label>
                                    <select name="payment_category"
                                            class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                                   font-sans text-sm focus:outline-none focus:border-ich-teal">
                                        <option value="full">Lunas Penuh</option>
                                        <option value="installment">Cicilan</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Bukti Transfer</label>
                                    <input type="file" name="gambar_bukti_pembayaran"
                                           accept="image/jpg,image/jpeg,image/png"
                                           class="w-full text-sm text-ich-ink-600 font-sans
                                                  file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                                  file:text-xs file:font-bold file:font-ui
                                                  file:bg-ich-green file:text-white cursor-pointer">
                                </div>

                                <button type="submit"
                                        class="w-full h-10 bg-ich-green text-white font-ui font-bold text-sm
                                               rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                                    Kirim Bukti Pembayaran
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
        </div>
    @endif

</x-mobile-layout>
