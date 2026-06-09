@php
    $keuanganBadge = auth()->user()->notifications()
        ->whereIn('type', [
            'App\Notifications\SppOverdueNotification',
            'App\Notifications\RegistrationFeeOverdueNotification',
        ])
        ->count();

    $tabs = [
        ['label' => 'Beranda',    'icon' => 'dashboard',   'route' => 'beranda'],
        ['label' => 'Kehadiran',  'icon' => 'calendar',    'route' => 'kehadiran'],
        ['label' => 'Akademik',   'icon' => 'book',        'route' => 'akademik'],
        ['label' => 'Keuangan',   'icon' => 'card',        'route' => 'pembayaran', 'badge' => $keuanganBadge],
        ['label' => 'Tabungan',   'icon' => 'piggy',       'route' => 'tabungan'],
        ['label' => 'Profil Anak','icon' => 'user_circle', 'route' => 'profil-anak'],
        ['label' => 'Daftar',     'icon' => 'clipboard',   'route' => 'pendaftaran'],
        ['label' => 'Pengaturan', 'icon' => 'settings',    'route' => 'pengaturan'],
    ];
@endphp

<div class="sticky bottom-0 z-10 bg-white border-t border-ich-line"
    style="padding-bottom: calc(8px + env(safe-area-inset-bottom, 0px))">
    <div class="flex overflow-x-auto pt-2 px-1" style="scrollbar-width: none; -ms-overflow-style: none;">
        @foreach($tabs as $tab)
            @php
                $isActive = Route::has($tab['route']) && request()->routeIs($tab['route'] . '*');
                $color = $isActive ? '#4A9E5C' : '#99A1AF';
            @endphp
            <a href="{{ Route::has($tab['route']) ? route($tab['route']) : '#' }}"
                class="flex flex-col items-center gap-[3px] py-1 px-3.5 shrink-0 no-underline">
                <div class="relative inline-flex">
                    <x-ich-icon :name="$tab['icon']" :size="20" :color="$color" />
                    @if(($tab['badge'] ?? 0) > 0)
                        <span class="absolute top-0 right-0 w-[5px] h-[5px] bg-red-500 rounded-full"></span>
                    @endif
                </div>
                <span class="font-ui text-[10px] leading-tight whitespace-nowrap
                                 {{ $isActive ? 'font-bold text-ich-green' : 'font-semibold text-ich-ink-300' }}">
                    {{ $tab['label'] }}
                </span>
            </a>
        @endforeach

        <form method="POST" action="{{ route('logout') }}" class="shrink-0">
            @csrf
            <button type="submit"
                class="flex flex-col items-center gap-[3px] py-1 px-3.5 bg-transparent border-none cursor-pointer">
                <x-ich-icon name="logout" :size="20" color="#99A1AF" />
                <span class="font-ui text-[10px] leading-tight font-semibold text-ich-ink-300 whitespace-nowrap">
                    Keluar
                </span>
            </button>
        </form>
    </div>
</div>