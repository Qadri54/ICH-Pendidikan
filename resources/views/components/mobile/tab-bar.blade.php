@php
$tabs = [
    ['label' => 'Beranda',   'icon' => 'dashboard',   'route' => 'beranda'],
    ['label' => 'Bayar',     'icon' => 'card',         'route' => 'pembayaran'],
    ['label' => 'Kehadiran', 'icon' => 'calendar',     'route' => 'kehadiran'],
    ['label' => 'Nilai',     'icon' => 'book',         'route' => 'akademik'],
    ['label' => 'Profil',    'icon' => 'user_circle',  'route' => 'profil-anak'],
];
@endphp

<div class="sticky bottom-0 z-10 bg-white border-t border-ich-line flex justify-around
            px-1.5 pt-2 pb-[calc(8px+env(safe-area-inset-bottom,0px))]">
    @foreach($tabs as $tab)
        @php
            $isActive = Route::has($tab['route']) && request()->routeIs($tab['route'] . '*');
            $color = $isActive ? '#3DA746' : '#99A1AF';
        @endphp
        <a href="{{ Route::has($tab['route']) ? route($tab['route']) : '#' }}"
           class="flex-1 flex flex-col items-center gap-[3px] py-1 px-0.5 no-underline cursor-pointer">
            <x-ich-icon :name="$tab['icon']" :size="20" :color="$color"/>
            <span class="font-ui text-[10px] leading-tight {{ $isActive ? 'font-bold text-ich-green' : 'font-semibold text-ich-ink-300' }}">
                {{ $tab['label'] }}
            </span>
        </a>
    @endforeach
</div>
