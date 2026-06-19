<x-mobile-layout title="SPP {{ $student->nama_siswa }}" page-title="SPP Bulanan">

    <div class="mb-4">
        <a href="{{ route('pembayaran.spp.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">&larr; Kembali</a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header siswa --}}
    <div class="bg-white rounded-xl shadow-ich-card p-5 mb-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-ich-green-surface flex items-center justify-center shrink-0">
                <span class="font-display font-bold text-lg text-ich-green">
                    {{ strtoupper(substr($student->nama_siswa, 0, 1)) }}
                </span>
            </div>
            <div>
                <p class="font-display font-bold text-base text-ich-ink-900">{{ $student->nama_siswa }}</p>
                <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                    {{ $invoices->count() }} tagihan belum lunas
                </p>
            </div>
        </div>
    </div>

    @if($invoices->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card px-5 py-6 text-center">
            <x-ich-icon name="check_circle" :size="40" color="#009966" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-sm text-ich-ink-900">Tidak Ada Tagihan</p>
            <p class="font-sans text-xs text-ich-ink-400 mt-1">Semua tagihan SPP sudah lunas.</p>
        </div>
    @else
        <div class="space-y-3">
        @foreach($invoices as $invoice)
            @php
                $invCfg = match($invoice->status) {
                    'pending' => ['label' => 'Menunggu',    'bg' => 'bg-ich-purple-soft', 'text' => 'text-ich-purple'],
                    'overdue' => ['label' => 'Terlambat',   'bg' => 'bg-ich-error-soft',  'text' => 'text-ich-error'],
                    default   => ['label' => 'Belum Bayar', 'bg' => 'bg-ich-warning-soft', 'text' => 'text-ich-warning'],
                };
            @endphp

            <div x-data="{ open: false, submitting: false }" class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                {{-- Invoice header --}}
                <div class="px-5 py-4 flex items-center justify-between"
                     @if($invoice->status !== 'pending') @click="open = !open" role="button" @endif>
                    <div>
                        <p class="font-ui font-bold text-sm text-ich-ink-900">
                            {{ \Carbon\Carbon::parse($invoice->tanggal_tahun)->translatedFormat('F Y') }}
                        </p>
                        <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                            Rp {{ number_format($invoice->jumlah, 0, ',', '.') }}
                            &middot; Jatuh tempo {{ \Carbon\Carbon::parse($invoice->jatuh_tempo)->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full text-xs font-ui font-bold {{ $invCfg['bg'] }} {{ $invCfg['text'] }}">
                            {{ $invCfg['label'] }}
                        </span>
                        @if($invoice->status !== 'pending')
                            <x-ich-icon name="chevron_down" :size="16" color="#99A1AF"
                                        ::class="open ? 'rotate-180 transition-transform' : 'transition-transform'"/>
                        @endif
                    </div>
                </div>

                @if($invoice->status === 'pending')
                    <div class="px-5 pb-4 flex items-center gap-2 text-ich-purple">
                        <x-ich-icon name="clock" :size="13" color="#8B5CF6"/>
                        <span class="font-sans text-xs">Bukti dikirim, menunggu konfirmasi admin</span>
                    </div>
                @else
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="border-t border-ich-line">
                        <form method="POST"
                              action="{{ route('pembayaran.spp', $invoice->invoice_id) }}"
                              enctype="multipart/form-data"
                              @submit="submitting = true"
                              class="px-5 py-4 space-y-3">
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
                                           rounded-ich-lg shadow-ich-btn hover:opacity-90 transition-opacity
                                           disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!submitting">Kirim Bukti SPP</span>
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
    @endif

</x-mobile-layout>
