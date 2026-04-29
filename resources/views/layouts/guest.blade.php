<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ICH Pendidikan') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

<div class="min-h-screen flex">

    {{-- KIRI: foto sekolah --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        <img src="{{ asset('images/auth-bg.jpg') }}"
             alt="ICH Pendidikan"
             class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-green-900/50"></div>

        <div class="relative z-10 flex flex-col items-center justify-center w-full px-12 text-center">
            <div class="bg-white rounded-2xl p-4 mb-6 shadow-xl">
                <img src="{{ asset('images/logo.png') }}" alt="ICH Logo" class="w-28 h-28 object-contain">
            </div>
            <h1 class="text-white text-3xl font-bold drop-shadow">Selamat Datang!</h1>
            <p class="text-white text-xl font-semibold mt-1 drop-shadow">IQRA' CREATIVE HOUSE</p>
        </div>

        <p class="absolute bottom-4 left-0 right-0 text-center text-white/60 text-xs">
            Copyright &copy; {{ date('Y') }} IQRA' CREATIVE GROUP. All Rights Reserved.
        </p>
    </div>

    {{-- KANAN: form --}}
    <div class="w-full lg:w-1/2 flex flex-col justify-between bg-gradient-to-b from-white to-green-100">

        <div class="flex-1 flex items-center justify-center px-8 py-12">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>

        <p class="lg:hidden text-center text-gray-400 text-xs pb-4">
            Copyright &copy; {{ date('Y') }} IQRA' CREATIVE GROUP. All Rights Reserved.
        </p>
    </div>

</div>
</body>
</html>
