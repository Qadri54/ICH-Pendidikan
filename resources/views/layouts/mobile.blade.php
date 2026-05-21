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
<body class="bg-[#101828] lg:bg-[#F5F6FA] flex justify-center lg:block">

{{-- Mobile app frame (max 430px, centered) --}}
<div class="ich-app flex flex-col">

    {{-- Topbar: uses drawerOpen from x-data above --}}
    <x-mobile.topbar
        :title="$pageTitle ?? 'ICH Pendidikan'"
        :user="auth()->user()?->name"
        :notif-count="auth()->user()->notifications()->count()"
        :notif-url="route('notifications.index')"/>

    {{-- Scrollable page content --}}
    <main class="flex-1 overflow-y-auto bg-[#F5F6FA]
                 px-4 pt-4 pb-[calc(64px+env(safe-area-inset-bottom,0px))]">
        {{ $slot }}
    </main>

    {{-- Bottom tab bar --}}
    <x-mobile.tab-bar/>


</div>

@livewireScripts
</body>
</html>
