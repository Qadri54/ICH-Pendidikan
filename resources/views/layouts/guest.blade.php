<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ICH Pendidikan') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-[#101828]">

<div class="min-h-screen lg:flex">

    {{-- ── Desktop left panel: hero + ICH branding (hidden on mobile) ── --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-[45%] flex-shrink-0 relative flex-col items-center justify-center overflow-hidden">
        {{-- Hero photo --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
             style="background-image: url('{{ asset('images/hero-students.jpg') }}')"></div>
        {{-- Green wash --}}
        <div class="absolute inset-0" style="background: var(--ich-green); opacity: 0.55"></div>

        {{-- Brand content --}}
        <div class="relative z-10 flex flex-col items-center text-center px-12 gap-5">
            <div class="w-20 h-20 rounded-2xl bg-white flex items-center justify-center overflow-hidden"
                 style="box-shadow:0 10px 24px rgba(0,0,0,0.2)">
                <img src="{{ asset('images/Logo.png') }}" alt="ICH Logo" class="w-16 h-16 object-contain">
            </div>
            <div>
                <h1 class="text-white font-display font-bold text-4xl leading-tight"
                    style="text-shadow:0 4px 8px rgba(0,0,0,0.3)">
                    Selamat Datang!
                </h1>
                <p class="text-white/85 font-ui font-semibold text-lg mt-1">IQRA' CREATIVE HOUSE</p>
            </div>
            <p class="text-white/70 font-sans text-sm max-w-xs">
                Platform Manajemen Pendidikan TK/PG Islam Terpadu
            </p>
        </div>

        <p class="absolute bottom-5 left-0 right-0 text-center text-white/50 font-sans text-xs">
            Copyright &copy; {{ date('Y') }} IQRA' CREATIVE GROUP. All Rights Reserved.
        </p>
    </div>

    {{-- ── Right panel: mobile full-screen / desktop form area ── --}}
    {{-- Mobile  : flex justify-center → centers the 430px slot container --}}
    {{-- Desktop : flex-1, white gradient, centers slot vertically --}}
    <div class="flex justify-center
                lg:flex-1 lg:bg-gradient-to-b lg:from-white lg:to-ich-green-surface
                lg:flex lg:items-center lg:justify-center lg:overflow-y-auto">

        {{-- Slot wrapper --}}
        {{-- Mobile  : max-w-[430px] min-h-screen → full-height mobile frame --}}
        {{-- Desktop : max-w-[400px], no min-height → natural form height --}}
        <div class="w-full max-w-[430px] min-h-screen relative
                    lg:max-w-[400px] lg:min-h-0 lg:py-10">
            {{ $slot }}
        </div>
    </div>

</div>

@livewireScripts
</body>
</html>
