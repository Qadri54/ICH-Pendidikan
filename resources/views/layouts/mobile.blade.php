<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'ICH Pendidikan') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-[#101828] flex justify-center">

{{-- Mobile app frame (max 430px, centered) --}}
<div class="ich-app flex flex-col" x-data="{ drawerOpen: false }">

    {{-- Topbar: uses drawerOpen from x-data above --}}
    <x-mobile.topbar
        :title="$pageTitle ?? 'ICH Pendidikan'"
        :user="auth()->user()?->name"
        :notif-count="0"/>

    {{-- Scrollable page content --}}
    <main class="flex-1 overflow-y-auto bg-[#F5F6FA]
                 pb-[calc(64px+env(safe-area-inset-bottom,0px))]">
        {{ $slot }}
    </main>

    {{-- Bottom tab bar --}}
    <x-mobile.tab-bar/>

    {{-- Slide-in drawer (overlays everything) --}}
    <x-mobile.drawer/>

</div>

@livewireScripts
</body>
</html>
