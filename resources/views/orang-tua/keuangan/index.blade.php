<x-mobile-layout title="Keuangan" page-title="Keuangan">

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

    @if($keuanganData->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card p-6 text-center">
            <x-ich-icon name="user_circle" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-sm text-ich-ink-900">Belum Ada Siswa Terdaftar</p>
            <p class="font-sans text-xs text-ich-ink-400 mt-1 mb-4">
                Daftarkan anak Anda terlebih dahulu dan tunggu konfirmasi dari sekolah.
            </p>
            <a href="{{ route('pendaftaran') }}"
               class="inline-block px-5 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg">
                Daftar Sekarang
            </a>
        </div>
    @else
        <div class="space-y-6">
        @foreach($keuanganData as $data)
            @php
                $student  = $data['student'];
                $fee      = $data['fee'];
                $pendingTx = $data['pendingRegistrationTx'];
                $invoices  = $data['sppInvoices'];
            @endphp

            {{-- Header anak --}}
            <div>
                <h2 class="font-display font-bold text-base text-ich-ink-900 mb-3">
                    {{ $student->nama_siswa }}
                </h2>

                {{-- ═══ BIAYA PENDAFTARAN ═══ --}}
                @if($fee)
                    @php
                        $feeCfg = match($fee->status) {
                            'paid'         => ['label'=>'Lunas',    'bg'=>'bg-[#D1FAE5]','text'=>'text-[#009966]'],
                            'installments' => ['label'=>'Cicilan',  'bg'=>'bg-[#EDE9FE]','text'=>'text-[#8B5CF6]'],
                            default        => ['label'=>'Belum Bayar','bg'=>'bg-[#FEF5DC]','text'=>'text-[#E09F17]'],
                        };
                    @endphp
                    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden mb-3">
                        @php
                            $totalPaid = $fee->transactions->where('status', 'approved')->sum('jumlah_bayar');
                            $remaining = max(0, $fee->total_jumlah - $totalPaid);
                        @endphp
                        <div class="px-5 py-4 flex items-center justify-between border-b border-ich-line">
                            <div>
                                <p class="font-ui font-bold text-sm text-ich-ink-900">Biaya Pendaftaran</p>
                                <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                    Total: Rp {{ number_format($fee->total_jumlah, 0, ',', '.') }}
                                </p>
                                @if($totalPaid > 0 && $fee->status !== 'paid')
                                    <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                        Dibayar: Rp {{ number_format($totalPaid, 0, ',', '.') }}
                                        · <span class="font-bold text-[#E09F17]">Sisa: Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                                    </p>
                                @endif
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-ui font-bold {{ $feeCfg['bg'] }} {{ $feeCfg['text'] }}">
                                {{ $feeCfg['label'] }}
                            </span>
                        </div>

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
                                    <div class="mb-3 p-3 bg-[#FEF2F2] rounded-lg">
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
                                               placeholder="Contoh: 3000000"
                                               class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                                      font-sans text-sm focus:outline-none focus:border-ich-teal">
                                    </div>
                                    <div>
                                        <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Nama Bank</label>
                                        <input type="text" name="nama_bank"
                                               value="{{ old('nama_bank') }}"
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

                {{-- ═══ SPP BULANAN ═══ --}}
                @if($invoices->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                        <div class="px-5 py-4 border-b border-ich-line">
                            <p class="font-ui font-bold text-sm text-ich-ink-900">SPP Bulanan</p>
                            <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                {{ $invoices->count() }} tagihan belum lunas
                            </p>
                        </div>

                        <div class="divide-y divide-ich-line">
                        @foreach($invoices as $invoice)
                            @php
                                $invCfg = match($invoice->status) {
                                    'pending' => ['label'=>'Menunggu','bg'=>'bg-[#EDE9FE]','text'=>'text-[#8B5CF6]'],
                                    'overdue' => ['label'=>'Terlambat','bg'=>'bg-[#FEE2E2]','text'=>'text-ich-error'],
                                    default   => ['label'=>'Belum Bayar','bg'=>'bg-[#FEF5DC]','text'=>'text-[#E09F17]'],
                                };
                            @endphp
                            <div x-data="{ open: false }" class="px-5 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-ui font-bold text-sm text-ich-ink-900">
                                            {{ \Carbon\Carbon::parse($invoice->tanggal_tahun)->translatedFormat('F Y') }}
                                        </p>
                                        <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                            Rp {{ number_format($invoice->jumlah, 0, ',', '.') }}
                                            · Jatuh tempo {{ \Carbon\Carbon::parse($invoice->jatuh_tempo)->translatedFormat('d M Y') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-ui font-bold {{ $invCfg['bg'] }} {{ $invCfg['text'] }}">
                                            {{ $invCfg['label'] }}
                                        </span>
                                        @if($invoice->status !== 'pending')
                                            <button @click="open = !open"
                                                    class="p-1 text-ich-ink-400 hover:text-ich-ink-900 transition-colors">
                                                <x-ich-icon name="chevron_down" :size="16" color="currentColor"/>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                @if($invoice->status === 'pending')
                                    <p class="mt-2 font-sans text-xs text-[#8B5CF6] flex items-center gap-1">
                                        <x-ich-icon name="clock" :size="13" color="#8B5CF6"/>
                                        Bukti dikirim, menunggu konfirmasi admin
                                    </p>
                                @else
                                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-3">
                                        <form method="POST"
                                              action="{{ route('pembayaran.spp', $invoice->invoice_id) }}"
                                              enctype="multipart/form-data"
                                              class="space-y-3">
                                            @csrf
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Jumlah Bayar</label>
                                                    <input type="number" name="jumlah_bayar"
                                                           value="{{ old('jumlah_bayar', $invoice->jumlah) }}"
                                                           class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                                                  font-sans text-sm focus:outline-none focus:border-ich-teal">
                                                </div>
                                                <div>
                                                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1">Nama Bank</label>
                                                    <input type="text" name="nama_bank"
                                                           value="{{ old('nama_bank') }}"
                                                           placeholder="BCA, BRI, dsb"
                                                           class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                                                  font-sans text-sm focus:outline-none focus:border-ich-teal">
                                                </div>
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
                                                    class="w-full h-10 bg-ich-teal text-white font-ui font-bold text-sm
                                                           rounded-ich-lg shadow-ich-btn hover:opacity-90 transition-opacity">
                                                Kirim Bukti SPP
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        </div>
                    </div>
                @elseif($fee)
                    <div class="bg-white rounded-xl shadow-ich-card px-5 py-4">
                        <p class="font-sans text-sm text-ich-ink-400 text-center">
                            Belum ada tagihan SPP aktif
                        </p>
                    </div>
                @endif
            </div>

        @endforeach
        </div>
    @endif

</x-mobile-layout>
