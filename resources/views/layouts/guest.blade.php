<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ICH Pendidikan') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-[#101828] min-h-screen flex justify-center">
    <div class="ich-app overflow-hidden">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
