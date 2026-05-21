<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'ICH Pendidikan') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100 h-full overflow-hidden">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">

    {{-- SIDEBAR --}}
    <aside class="bg-ich-sidebar flex flex-col items-center flex-shrink-0 transition-all duration-200"
           :class="sidebarOpen ? 'w-32 py-4 gap-2 overflow-y-auto' : 'w-0 overflow-hidden'">

        {{-- Nav Items --}}
        @php
            $role      = auth()->user()?->role?->role_name;
            $isAdmin   = in_array($role, ['Admin', 'Kepala Sekolah', 'Kepala Yayasan']);
            $logoRoute = $isAdmin ? route('admin.laporan.index') : route('dashboard');
        @endphp

        {{-- Logo --}}
        <a href="{{ $logoRoute }}" class="mb-2">
            <img src="{{ asset('images/logo.svg') }}" alt="ICH Logo" class="w-14 h-14 object-contain">
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

            {{-- Search --}}
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" placeholder="Search"
                           class="w-full pl-10 pr-4 py-2 rounded-full text-sm bg-white focus:outline-none focus:ring-2 focus:ring-white/50">
                </div>
            </div>

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
                <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-[#FEE2E2] text-ich-error rounded-lg text-sm font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>

    </div>
</div>

@livewireScripts
</body>
</html>
