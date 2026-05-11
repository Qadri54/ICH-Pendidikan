<x-mobile-layout title="Keuangan" page-title="Keuangan">

    @if($summary->isEmpty())
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
        <div class="space-y-3">

            {{-- Kartu Biaya Pendaftaran --}}
            <a href="{{ route('pembayaran.pendaftaran.index') }}"
               class="block bg-white rounded-xl shadow-ich-card p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <x-ich-icon name="document" :size="24" color="#8B5CF6"/>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-ui font-bold text-sm text-ich-ink-900">Biaya Pendaftaran</p>
                        <p class="font-sans text-xs text-ich-ink-400 mt-0.5">Tagihan dan pembayaran biaya pendaftaran</p>
                        @php
                            $unpaidFees = $summary->filter(fn($d) => $d['fee'] && $d['fee']->status !== 'paid')->count();
                        @endphp
                        @if($unpaidFees > 0)
                            <span class="inline-block mt-1.5 px-2 py-0.5 bg-[#FEF5DC] text-[#E09F17] text-xs font-ui font-bold rounded-full">
                                {{ $unpaidFees }} belum lunas
                            </span>
                        @else
                            <span class="inline-block mt-1.5 px-2 py-0.5 bg-[#D1FAE5] text-[#009966] text-xs font-ui font-bold rounded-full">
                                Semua lunas
                            </span>
                        @endif
                    </div>
                    <x-ich-icon name="chevron_right" :size="20" color="#99A1AF"/>
                </div>
            </a>

            {{-- Kartu SPP --}}
            <a href="{{ route('pembayaran.spp.index') }}"
               class="block bg-white rounded-xl shadow-ich-card p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#D1FAE5] flex items-center justify-center shrink-0">
                        <x-ich-icon name="calendar" :size="24" color="#009966"/>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-ui font-bold text-sm text-ich-ink-900">SPP Bulanan</p>
                        <p class="font-sans text-xs text-ich-ink-400 mt-0.5">Tagihan dan pembayaran SPP setiap bulan</p>
                        @php
                            $totalSppPending = $summary->sum('sppCount');
                        @endphp
                        @if($totalSppPending > 0)
                            <span class="inline-block mt-1.5 px-2 py-0.5 bg-[#FEF5DC] text-[#E09F17] text-xs font-ui font-bold rounded-full">
                                {{ $totalSppPending }} tagihan belum lunas
                            </span>
                        @else
                            <span class="inline-block mt-1.5 px-2 py-0.5 bg-[#D1FAE5] text-[#009966] text-xs font-ui font-bold rounded-full">
                                Tidak ada tagihan
                            </span>
                        @endif
                    </div>
                    <x-ich-icon name="chevron_right" :size="20" color="#99A1AF"/>
                </div>
            </a>

        </div>
    @endif

</x-mobile-layout>
