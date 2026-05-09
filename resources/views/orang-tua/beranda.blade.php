<x-mobile-layout title="Beranda" page-title="Beranda">

    <div class="p-4 space-y-4">

        {{-- Greeting --}}
        <div class="bg-ich-green rounded-xl p-5 text-white">
            <p class="font-sans text-sm opacity-80">Selamat datang,</p>
            <p class="font-display font-bold text-xl mt-0.5">{{ auth()->user()->name }}</p>
            <p class="font-sans text-xs opacity-70 mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>

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
                            <div class="text-center p-2 bg-[#EDE9FE] rounded-lg">
                                <p class="font-display font-bold text-lg text-[#8B5CF6]">{{ $item['izin'] }}</p>
                                <p class="font-ui font-bold text-xs text-[#8B5CF6]">Izin</p>
                            </div>
                            <div class="text-center p-2 bg-[#FEE2E2] rounded-lg">
                                <p class="font-display font-bold text-lg text-ich-error">{{ $item['sakit'] }}</p>
                                <p class="font-ui font-bold text-xs text-ich-error">Sakit</p>
                            </div>
                            <div class="text-center p-2 bg-[#FEF5DC] rounded-lg">
                                <p class="font-display font-bold text-lg text-[#E09F17]">{{ $item['alfa'] }}</p>
                                <p class="font-ui font-bold text-xs text-[#E09F17]">Alfa</p>
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
                    ['label' => 'Kehadiran',  'icon' => 'calendar',    'route' => 'kehadiran',  'color' => 'bg-[#E8F5EA] text-ich-green'],
                    ['label' => 'Akademik',   'icon' => 'book',        'route' => 'akademik',   'color' => 'bg-[#F4F7FC] text-ich-teal'],
                    ['label' => 'Keuangan',   'icon' => 'card',        'route' => 'pembayaran', 'color' => 'bg-[#FEF5DC] text-[#E09F17]'],
                    ['label' => 'Tabungan',   'icon' => 'piggy',       'route' => 'tabungan',   'color' => 'bg-[#EDE9FE] text-[#8B5CF6]'],
                    ['label' => 'Profil Anak','icon' => 'user_circle', 'route' => 'profil-anak','color' => 'bg-[#F4F7FC] text-ich-teal'],
                    ['label' => 'Pengaturan', 'icon' => 'settings',    'route' => 'pengaturan', 'color' => 'bg-[#F3F4F6] text-ich-ink-500'],
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
