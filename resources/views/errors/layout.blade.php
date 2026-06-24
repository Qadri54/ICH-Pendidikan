<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ $title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-ich-surface">

    <div class="min-h-screen flex flex-col items-center justify-center px-6 py-12">

        {{-- Logo --}}
        <div class="w-[72px] h-[72px] rounded-[18px] bg-white flex items-center justify-center shadow-ich-card mb-8 overflow-hidden">
            <img src="{{ asset('images/Logo.png') }}" alt="ICH Logo" class="w-14 h-14 object-contain">
        </div>

        {{-- Icon --}}
        <div class="w-20 h-20 rounded-2xl {{ $iconBg }} flex items-center justify-center mb-6">
            {!! $icon !!}
        </div>

        {{-- Code --}}
        <h1 class="font-display font-extrabold text-[64px] leading-none text-ich-ink-900 mb-2">{{ $code }}</h1>

        {{-- Title --}}
        <h2 class="font-display font-bold text-[20px] text-ich-ink-900 mb-2 text-center">{{ $title }}</h2>

        {{-- Message --}}
        <p class="font-sans text-sm text-ich-ink-400 text-center max-w-md mb-8">{{ $message }}</p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <a href="{{ url('/') }}"
               class="h-[46px] px-6 bg-ich-teal text-white font-sans font-bold text-[14px]
                      rounded-ich-lg flex items-center justify-center no-underline
                      shadow-ich-btn hover:bg-ich-teal-dark transition-colors">
                Kembali ke Beranda
            </a>
            <button onclick="history.back()"
                    class="h-[46px] px-6 bg-white text-ich-ink-600 font-sans font-bold text-[14px]
                           rounded-ich-lg flex items-center justify-center border-2 border-ich-line
                           shadow-ich-card hover:bg-ich-surface transition-colors cursor-pointer">
                Halaman Sebelumnya
            </button>
        </div>

        {{-- Footer --}}
        <p class="font-sans text-[10px] text-ich-ink-300 mt-12">
            Copyright &copy; {{ date('Y') }} IQRA' CREATIVE GROUP. All Rights Reserved.
        </p>

    </div>

</body>
</html>
