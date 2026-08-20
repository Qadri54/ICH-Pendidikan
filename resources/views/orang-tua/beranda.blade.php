<x-mobile-layout title="Beranda" page-title="Beranda">

    <div class="p-4 space-y-4">

        {{-- Greeting --}}
        <div class="bg-ich-green rounded-xl p-5 text-white">
            <p class="font-sans text-sm opacity-80">Selamat datang,</p>
            <p class="font-display font-bold text-xl mt-0.5">{{ auth()->user()->name }}</p>
            <p class="font-sans text-xs opacity-70 mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>

        {{-- Notifikasi Raport --}}
        @foreach($latestRaports as $raport)
            <div class="bg-ich-teal-soft rounded-xl shadow-sm p-4 flex items-center justify-between gap-3 border border-ich-teal/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-ich-teal text-white flex items-center justify-center flex-shrink-0">
                        <x-ich-icon name="book" :size="20"/>
                    </div>
                    <div>
                        <p class="font-ui font-bold text-sm text-ich-ink-900 leading-tight">Raport Baru Tersedia!</p>
                        <p class="font-sans text-xs text-ich-ink-500 mt-0.5">Raport {{ $raport->student->nama_siswa }} telah selesai dinilai.</p>
                    </div>
                </div>
                <a href="{{ route('akademik.detail', $raport->student_id) }}" class="px-3 py-1.5 bg-ich-teal text-white font-ui font-bold text-xs rounded-lg whitespace-nowrap shadow-sm hover:bg-ich-teal-dark">
                    Lihat Raport
                </a>
            </div>
        @endforeach

        {{-- Notifikasi SPP Tertunggak --}}
        @foreach($unpaidSpp as $spp)
            <div class="bg-ich-error-soft rounded-xl shadow-sm p-4 flex items-center justify-between gap-3 border border-ich-error/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-ich-error text-white flex items-center justify-center flex-shrink-0">
                        <x-ich-icon name="alert" :size="20"/>
                    </div>
                    <div>
                        <p class="font-ui font-bold text-sm text-ich-ink-900 leading-tight">Tagihan SPP Belum Dibayar</p>
                        <p class="font-sans text-xs text-ich-ink-500 mt-0.5">{{ $spp->student->nama_siswa }} - Rp {{ number_format($spp->amount, 0, ',', '.') }}</p>
                    </div>
                </div>
                <a href="{{ route('pembayaran.spp.detail', $spp->student_id) }}" class="px-3 py-1.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg whitespace-nowrap shadow-sm hover:bg-ich-error-dark">
                    Bayar
                </a>
            </div>
        @endforeach

        {{-- Info Tabungan Singkat --}}
        @if($totalTabungan > 0)
            <div class="bg-ich-purple-soft rounded-xl shadow-sm p-4 flex items-center justify-between gap-3 border border-ich-purple/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-ich-purple text-white flex items-center justify-center flex-shrink-0">
                        <x-ich-icon name="piggy" :size="20"/>
                    </div>
                    <div>
                        <p class="font-ui font-bold text-sm text-ich-ink-900 leading-tight">Total Saldo Tabungan</p>
                        <p class="font-sans text-xs text-ich-ink-500 mt-0.5">Semua Anak</p>
                    </div>
                </div>
                <div class="font-display font-bold text-ich-purple">
                    Rp {{ number_format($totalTabungan, 0, ',', '.') }}
                </div>
            </div>
        @endif

        {{-- Ringkasan Absensi Bulan Ini --}}
        @if($absensiPerAnak->isNotEmpty())
            <div>
                <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide mb-2">
                    Absensi Bulan Ini
                </p>
                @foreach($absensiPerAnak as $item)
                    <div class="bg-white rounded-xl shadow-ich-card p-4 mb-3">
                        <p class="font-ui font-bold text-sm text-ich-ink-900 mb-3">
                            {{ $item['student']->nama_siswa }}
                        </p>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="text-center p-2 bg-ich-purple-soft rounded-lg">
                                <p class="font-display font-bold text-lg text-ich-purple">{{ $item['izin'] }}</p>
                                <p class="font-ui font-bold text-xs text-ich-purple">Izin</p>
                            </div>
                            <div class="text-center p-2 bg-ich-error-soft rounded-lg">
                                <p class="font-display font-bold text-lg text-ich-error">{{ $item['sakit'] }}</p>
                                <p class="font-ui font-bold text-xs text-ich-error">Sakit</p>
                            </div>
                            <div class="text-center p-2 bg-ich-warning-soft rounded-lg">
                                <p class="font-display font-bold text-lg text-ich-warning">{{ $item['alfa'] }}</p>
                                <p class="font-ui font-bold text-xs text-ich-warning">Alfa</p>
                            </div>
                        </div>
                        <a href="{{ route('kehadiran') }}"
                           class="block mt-3 text-center text-xs font-ui font-bold text-ich-teal hover:underline">
                            Lihat detail kehadiran →
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Menu Cepat --}}
        <div>
            <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide mb-2">Menu</p>
            <div class="grid grid-cols-2 gap-3">
                @php
                $menus = [
                    ['label' => 'Kehadiran',  'icon' => 'calendar',    'route' => 'kehadiran',  'color' => 'bg-ich-green-surface text-ich-green'],
                    ['label' => 'Akademik',   'icon' => 'book',        'route' => 'akademik',   'color' => 'bg-ich-info-soft text-ich-teal'],
                    ['label' => 'Keuangan',   'icon' => 'card',        'route' => 'pembayaran', 'color' => 'bg-ich-warning-soft text-ich-warning'],
                    ['label' => 'Tabungan',   'icon' => 'piggy',       'route' => 'tabungan',   'color' => 'bg-ich-purple-soft text-ich-purple'],
                    ['label' => 'Profil Anak','icon' => 'user_circle', 'route' => 'profil-anak','color' => 'bg-ich-info-soft text-ich-teal'],
                    ['label' => 'Pengaturan', 'icon' => 'settings',    'route' => 'pengaturan', 'color' => 'bg-gray-100 text-ich-ink-500'],
                ];
                @endphp

                @foreach($menus as $menu)
                    <a href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
                       class="bg-white rounded-xl shadow-ich-card p-4 flex flex-col items-center gap-2 no-underline">
                        <div class="w-11 h-11 rounded-xl {{ $menu['color'] }} flex items-center justify-center">
                            <x-ich-icon :name="$menu['icon']" :size="22" color="currentColor"/>
                        </div>
                        <span class="font-ui font-bold text-xs text-ich-ink-900 text-center">{{ $menu['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

</x-mobile-layout>
