@php
$items = [
    ['label' => 'Dashboard',   'icon' => 'dashboard',   'route' => 'beranda'],
    ['label' => 'Profil Anak', 'icon' => 'user_circle', 'route' => 'profil-anak'],
    ['label' => 'Akademik',    'icon' => 'book',         'route' => 'akademik'],
    ['label' => 'Kehadiran',   'icon' => 'calendar',     'route' => 'kehadiran'],
    ['label' => 'Keuangan',    'icon' => 'card',         'route' => 'pembayaran'],
    ['label' => 'Tabungan',    'icon' => 'piggy',        'route' => 'tabungan'],
    ['label' => 'Prestasi',    'icon' => 'trophy',       'route' => 'prestasi'],
    ['label' => 'Pengaturan',  'icon' => 'settings',     'route' => 'pengaturan'],
];
$user = auth()->user();
$initials = strtoupper(substr($user?->name ?? 'U', 0, 1));
@endphp

{{-- Drawer overlay --}}
<div class="fixed inset-0 z-50" x-show="drawerOpen" x-cloak style="pointer-events: none;">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-[#101828]/50 transition-opacity"
         :style="drawerOpen ? 'opacity:1;pointer-events:auto' : 'opacity:0'"
         @click="drawerOpen = false"></div>

    {{-- Panel --}}
    <div class="absolute top-0 left-0 bottom-0 w-[280px] bg-ich-sidebar text-white flex flex-col
                transition-transform duration-[220ms] ease-[cubic-bezier(0.2,0.8,0.2,1)]"
         :style="drawerOpen ? 'transform:translateX(0);pointer-events:auto' : 'transform:translateX(-100%)'">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-white/15">
            <div class="w-11 h-11 rounded-[12px] bg-white flex items-center justify-center shrink-0 overflow-hidden">
                <img src="{{ asset('images/Logo.png') }}" alt="ICH Logo" class="w-8 h-8 object-contain">
            </div>
            <div class="min-w-0">
                <div class="font-ui font-bold text-[14px] truncate">{{ $user?->name ?? 'Pengguna' }}</div>
                <div class="font-sans text-[11px] opacity-90 mt-0.5">Orang Tua</div>
            </div>
        </div>

        {{-- Nav items --}}
        <nav class="flex-1 overflow-y-auto p-2.5">
            @foreach($items as $item)
                <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                   @click="drawerOpen = false"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-[10px] font-ui font-semibold
                          text-[13px] text-white no-underline hover:bg-white/10 transition-colors">
                    <x-ich-icon :name="$item['icon']" :size="18" color="#fff"/>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="border-t border-white/15">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-3.5 py-3.5 font-ui font-semibold
                           text-[13px] text-white bg-transparent border-none cursor-pointer
                           hover:bg-white/10 transition-colors">
                <x-ich-icon name="logout" :size="18" color="#fff"/>
                Keluar
            </button>
        </form>

    </div>
</div>
