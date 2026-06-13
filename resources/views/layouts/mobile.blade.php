<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name', 'ICH Pendidikan') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            [x-cloak] {
                display: none !important
            }
        </style>
    </head>

    <body class="bg-[#101828] lg:bg-ich-surface flex justify-center lg:block">

        {{-- Mobile app frame (max 430px, centered) --}}
        <div class="ich-app flex flex-col">

            {{-- Topbar: uses drawerOpen from x-data above --}}
            <x-mobile.topbar :title="$pageTitle ?? 'ICH Pendidikan'" :user="auth()->user()?->name"
                :notif-count="auth()->user()->notifications()->count()" :notif-url="route('notifications.index')" />

            {{-- Scrollable page content --}}
            <main class="flex-1 overflow-y-auto bg-ich-surface
                 px-6 pt-4 pb-[calc(64px+env(safe-area-inset-bottom,0px))]">
                {{ $slot }}
            </main>

            {{-- Bottom tab bar --}}
            <x-mobile.tab-bar />

        </div>

        {{-- Page loading overlay --}}
        <div id="page-loader" style="display:none"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-white/70 backdrop-blur-sm">
            <div class="flex flex-col items-center gap-3">
                <svg class="animate-spin h-8 w-8 text-ich-green" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="font-ui font-semibold text-sm text-ich-ink-600">Memuat...</span>
            </div>
        </div>

        <script>
            (function () {
                const loader = document.getElementById('page-loader');

                document.addEventListener('click', function (e) {
                    const link = e.target.closest('a[href]');
                    if (!link) return;
                    const href = link.getAttribute('href');
                    if (!href || href.startsWith('#') || href.startsWith('javascript:')
                        || link.hasAttribute('download') || link.target === '_blank'
                        || href.match(/\/(download|export|cetak)/)) return;
                    loader.style.display = 'flex';
                });

                document.querySelectorAll('form').forEach(function (form) {
                    form.addEventListener('submit', function () {
                        loader.style.display = 'flex';
                    });
                });

                window.addEventListener('pageshow', function (e) {
                    if (e.persisted) loader.style.display = 'none';
                });
            })();
        </script>

        @livewireScripts
    </body>

</html>