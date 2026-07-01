<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'ICH Pendidikan') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100 h-full overflow-hidden"
      x-data="{
          pageLoading: false,
          cm: { show: false, title: '', message: '', action: '', method: 'POST', fields: {}, btnText: 'Konfirmasi', danger: false }
      }"
      @page-loading.window="pageLoading = true"
      @open-confirm.window="cm = { show: true, method: 'POST', fields: {}, btnText: 'Konfirmasi', danger: false, ...$event.detail }">

{{-- Global Loading Overlay --}}
<div x-show="pageLoading" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20 backdrop-blur-[2px]" x-cloak>
    <div class="bg-white rounded-2xl shadow-xl px-8 py-6 flex flex-col items-center gap-3">
        <svg class="animate-spin h-8 w-8 text-ich-green" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm font-ui font-semibold text-ich-ink-600">Memproses...</span>
    </div>
</div>

{{-- Global Confirm Modal --}}
<div x-show="cm.show" x-cloak
     class="fixed inset-0 z-[9998] flex items-center justify-center px-4"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="fixed inset-0 bg-black/50" @click="cm.show = false"></div>
    <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-xl z-10"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @keydown.escape.window="cm.show && (cm.show = false)">
        <div class="px-6 py-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                     :class="cm.danger ? 'bg-ich-error-soft' : 'bg-ich-green-surface'">
                    <svg x-show="!cm.danger" class="w-5 h-5 text-ich-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg x-show="cm.danger" x-cloak class="w-5 h-5 text-ich-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-display font-bold text-ich-ink-900" x-text="cm.title"></h2>
            </div>
            <p class="text-sm font-sans text-ich-ink-600 mb-5 leading-relaxed" x-text="cm.message"></p>
            <form :action="cm.action" method="POST">
                @csrf
                <template x-if="cm.method !== 'POST'">
                    <input type="hidden" name="_method" :value="cm.method">
                </template>
                <template x-for="[key, val] in Object.entries(cm.fields || {})" :key="key">
                    <input type="hidden" :name="key" :value="val">
                </template>
                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 py-2.5 font-ui font-bold text-sm text-white rounded-ich-lg transition-colors"
                            :class="cm.danger ? 'bg-ich-error hover:opacity-90' : 'bg-ich-green hover:bg-ich-green-dark shadow-ich-btn'"
                            x-text="cm.btnText">
                    </button>
                    <button type="button" @click="cm.show = false"
                            class="flex-1 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">

    {{-- SIDEBAR --}}
    <aside class="bg-ich-sidebar flex flex-col items-center flex-shrink-0 transition-all duration-200"
           :class="sidebarOpen ? 'w-32 py-4 gap-2 overflow-y-auto' : 'w-0 overflow-hidden'">

        {{-- Nav Items --}}
        @php
            $role      = auth()->user()?->role?->role_name;
            $isAdmin   = in_array($role, ['Admin', 'Kepala Sekolah', 'Kepala Yayasan']);
            $logoRoute = route('dashboard');
        @endphp

        {{-- Logo --}}
        <a href="{{ $logoRoute }}" class="mb-2">
            <img src="{{ asset('images/Logo.png') }}" alt="ICH Logo" class="w-14 h-14 object-contain">
        </a>

        <x-sidebar-nav :role="$role" />
    </aside>

    {{-- MAIN AREA --}}
    <div class="flex flex-col flex-1 overflow-hidden">

        {{-- TOPBAR --}}
        <header class="bg-ich-green h-14 flex items-center px-6 gap-4 flex-shrink-0">
            {{-- Hamburger --}}
            <button @click="sidebarOpen = !sidebarOpen" class="text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="flex-1"></div>

            {{-- Notification --}}
            @php
                $notifCount   = auth()->user()->notifications()->count();
                $recentNotifs = auth()->user()->notifications()->latest()->take(5)->get();
            @endphp
            <div class="relative" x-data="{ notifOpen: false }">
                <button @click="notifOpen = !notifOpen" class="relative text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($notifCount > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                            {{ $notifCount > 9 ? '9+' : $notifCount }}
                        </span>
                    @endif
                </button>

                {{-- Dropdown --}}
                <div x-show="notifOpen" @click.outside="notifOpen = false" x-transition
                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800 text-sm">Notifikasi</h3>
                        @if($notifCount > 0)
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                <button type="submit" class="text-xs text-red-500 hover:underline">
                                    Hapus semua
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- List --}}
                    <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                        @forelse($recentNotifs as $notif)
                            <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start gap-3">
                                        {{-- Icon --}}
                                        <div class="w-8 h-8 rounded-full bg-ich-green/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-ich-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                            </svg>
                                        </div>
                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-800 font-medium leading-snug">
                                                {{ $notif->data['message'] }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $notif->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </button>
                            </form>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-400 text-sm">
                                Tidak ada notifikasi
                            </div>
                        @endforelse
                    </div>

                    {{-- Footer --}}
                    <div class="border-t border-gray-100 px-4 py-2.5">
                        <a href="{{ route('notifications.index') }}"
                           class="block text-center text-xs font-ui font-semibold text-ich-green hover:underline">
                            Lihat semua notifikasi
                        </a>
                    </div>
                </div>
            </div>

            {{-- User dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 text-white">
                    <div class="w-9 h-9 rounded-full bg-white/20 border-2 border-white
                                flex items-center justify-center font-display font-bold text-sm text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="text-left hidden sm:block">
                        <div class="text-sm font-semibold leading-tight">{{ auth()->user()->name }}</div>
                        <div class="text-xs opacity-80">{{ auth()->user()->role?->role_name ?? 'User' }}</div>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50 py-1">
                    <a href="{{ route('profile.edit') }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-y-auto p-6">

            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>

    </div>
</div>

@livewireScripts
<script>
document.addEventListener('click', function(e) {
    const link = e.target.closest('a[href]');
    if (!link) return;
    const href = link.getAttribute('href');
    if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
    if (link.hasAttribute('download') || link.target === '_blank') return;
    if (e.ctrlKey || e.metaKey || e.shiftKey) return;
    if (link.hasAttribute('@click.prevent') || link.hasAttribute('x-on:click.prevent')) return;
    if (link.closest('.no-loading') || link.classList.contains('no-loading')) return;
    window.dispatchEvent(new CustomEvent('page-loading'));
});
document.addEventListener('submit', function(e) {
    if (e.target.closest('.no-loading')) return;
    window.dispatchEvent(new CustomEvent('page-loading'));
});
window.addEventListener('pageshow', function(e) {
    if (e.persisted) {
        var el = document.querySelector('[x-show="pageLoading"]');
        if (el) el.style.display = 'none';
        Alpine.evaluate(document.body, 'pageLoading = false');
    }
});
</script>
@stack('scripts')
</body>
</html>
