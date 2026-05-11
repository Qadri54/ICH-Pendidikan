<x-mobile-layout title="SPP Bulanan" page-title="SPP Bulanan">

    <div class="mb-4">
        <a href="{{ route('pembayaran') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">
            {{ session('success') }}
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
                $student = $item['student'];
                $invoices = $item['invoices'];
            @endphp

            <div>
                <h2 class="font-display font-bold text-base text-ich-ink-900 mb-3">
                    {{ $student->nama_siswa }}
                </h2>

                @if($invoices->isEmpty())
                    <div class="bg-white rounded-xl shadow-ich-card px-5 py-4">
                        <p class="font-sans text-sm text-ich-ink-400 text-center">Tidak ada tagihan SPP aktif</p>
                    </div>
                @else
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
                                    'pending' => ['label' => 'Menunggu',    'bg' => 'bg-[#EDE9FE]', 'text' => 'text-[#8B5CF6]'],
                                    'overdue' => ['label' => 'Terlambat',   'bg' => 'bg-[#FEE2E2]', 'text' => 'text-ich-error'],
                                    default   => ['label' => 'Belum Bayar', 'bg' => 'bg-[#FEF5DC]', 'text' => 'text-[#E09F17]'],
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
                                                    <input type="text" name="nama_bank" value="{{ old('nama_bank') }}"
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
                @endif
            </div>
        @endforeach
        </div>
    @endif

</x-mobile-layout>
