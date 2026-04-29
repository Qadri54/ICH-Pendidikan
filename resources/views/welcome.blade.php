<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TK ICH - IQRA' CREATIVE HOUSE</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="font-sans antialiased text-gray-800" x-data>

{{-- ═══════════════════════════════════════
     NAVBAR
═══════════════════════════════════════ --}}
<nav class="fixed top-0 left-0 right-0 z-50 bg-[#1a4a1a]" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">

        {{-- Logo --}}
        <a href="/" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="ICH"
                 class="w-10 h-10 object-contain"
                 onerror="this.style.display='none'">
            <span class="text-yellow-400 font-bold text-sm leading-tight">ICH<br>
                <span class="text-white text-xs font-normal">Iqra Creative House</span>
            </span>
        </a>

        {{-- Desktop nav --}}
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-white/80">
            <a href="#tentang" class="hover:text-white transition">Tentang Kami</a>
            <a href="#program" class="hover:text-white transition">Program</a>
            <a href="#kegiatan" class="hover:text-white transition">Kegiatan</a>
            <a href="#kontak" class="hover:text-white transition">Kontak</a>
        </div>

        {{-- Buttons --}}
        <div class="hidden md:flex items-center gap-3">
            <a href="{{ route('login') }}"
               class="px-5 py-2 text-sm font-semibold text-white border border-white rounded-lg hover:bg-white/10 transition">
                Masuk
            </a>
            <a href="{{ route('register') }}"
               class="px-5 py-2 text-sm font-semibold text-[#1a4a1a] bg-white rounded-lg hover:bg-gray-100 transition">
                Daftar
            </a>
        </div>

        {{-- Mobile burger --}}
        <button @click="open = !open" class="md:hidden text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition class="md:hidden bg-[#1a4a1a] border-t border-white/10 px-6 pb-4">
        <a href="#tentang" class="block py-2 text-white/80 hover:text-white text-sm">Tentang Kami</a>
        <a href="#program"  class="block py-2 text-white/80 hover:text-white text-sm">Program</a>
        <a href="#kegiatan" class="block py-2 text-white/80 hover:text-white text-sm">Kegiatan</a>
        <a href="#kontak"   class="block py-2 text-white/80 hover:text-white text-sm">Kontak</a>
        <div class="flex gap-3 mt-3">
            <a href="{{ route('login') }}"
               class="flex-1 text-center py-2 text-sm font-semibold text-white border border-white rounded-lg">Masuk</a>
            <a href="{{ route('register') }}"
               class="flex-1 text-center py-2 text-sm font-semibold text-[#1a4a1a] bg-white rounded-lg">Daftar</a>
        </div>
    </div>
</nav>

{{-- ═══════════════════════════════════════
     HERO
═══════════════════════════════════════ --}}
<section class="relative min-h-screen flex flex-col items-center justify-center pt-16">
    {{-- Background --}}
    <img src="{{ asset('images/hero-bg.jpg') }}" alt=""
         class="absolute inset-0 w-full h-full object-cover"
         onerror="this.style.background='#1a4a1a';this.style.display='block'">
    <div class="absolute inset-0 bg-[#0d3b0d]/65"></div>

    {{-- Content --}}
    <div class="relative z-10 text-center text-white px-6 max-w-4xl mx-auto">

        {{-- Banner tag --}}
        <div class="inline-block bg-green-700/80 border border-green-500 rounded-xl px-6 py-3 mb-6">
            <p class="font-bold text-base md:text-lg tracking-wide">PESERTA DIDIK BARU</p>
            <p class="font-bold text-base md:text-lg">TAHUN AJARAN 2023 - 2024</p>
            <p class="font-bold text-xl md:text-2xl tracking-widest mt-1">PG - TK</p>
        </div>

        <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4">
            Membangun Generasi<br>
            <span class="italic">Qurani &amp; Kreatif</span>
        </h1>

        {{-- Badges --}}
        <div class="flex flex-wrap justify-center gap-4 mt-8">
            @foreach (['Belajar Al-Quran', 'Bimbingan Akhlak', 'Program Terstruktur'] as $badge)
            <span class="flex items-center gap-2 bg-white/20 backdrop-blur border border-white/30
                         px-5 py-2 rounded-full text-sm font-medium">
                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9
                             12.586 7.707 11.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"/>
                </svg>
                {{ $badge }}
            </span>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════
     TENTANG KAMI
═══════════════════════════════════════ --}}
<section id="tentang" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Tentang Kami</h2>

        {{-- Baris 1: Foto kiri, Visi kanan --}}
        <div class="grid md:grid-cols-2 gap-10 items-center mb-12">
            <div>
                <img src="{{ asset('images/about-1.jpg') }}" alt="Tentang Kami"
                     class="w-full h-64 object-cover rounded-2xl shadow"
                     onerror="this.src='https://placehold.co/600x400/16a34a/white?text=ICH+Pendidikan'">
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Visi</h3>
                <p class="text-gray-600 leading-relaxed">
                    Menjadi lembaga belajar Islami yang unggul, profesional, dan terpercaya dalam membina generasi yang cinta
                    Al-Qur'an, berakhlak mulia, berjiwa seni, berakal sehat, dan mengembangkan potensi peserta didik secara
                    islami dan kreatif.
                </p>
            </div>
        </div>

        {{-- Baris 2: Misi kiri, Foto kanan --}}
        <div class="grid md:grid-cols-2 gap-10 items-center">
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Misi</h3>
                <ul class="text-gray-600 space-y-2 leading-relaxed">
                    @foreach ([
                        'Menyelenggarakan pembelajaran Al-Qur\'an dengan metode yang menyenangkan dan efektif',
                        'Membangun karakter dan akhlak Islami pada setiap peserta didik',
                        'Menyediakan lingkungan belajar yang islami, kreatif, dan kondusif',
                        'Meningkatkan kualitas kelulusan yang islami dan kreatif',
                    ] as $item)
                    <li class="flex items-start gap-2">
                        <span class="text-green-600 mt-1 shrink-0">•</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <img src="{{ asset('images/about-2.jpg') }}" alt="Misi ICH"
                     class="w-full h-64 object-cover rounded-2xl shadow"
                     onerror="this.src='https://placehold.co/600x400/16a34a/white?text=Misi+ICH'">
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════
     STRUKTUR YAYASAN
═══════════════════════════════════════ --}}
<section class="py-16 bg-green-700">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-white mb-10">Struktur Yayasan</h2>
        <div class="rounded-2xl overflow-hidden shadow-2xl">
            <img src="{{ asset('images/struktur-yayasan.jpg') }}" alt="Struktur Yayasan"
                 class="w-full object-cover"
                 onerror="this.src='https://placehold.co/1200x600/155215/white?text=Struktur+Yayasan+ICH'">
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════
     PROGRAM UNGGULAN
═══════════════════════════════════════ --}}
<section id="program" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-3xl font-bold text-gray-900">Program Unggulan</h2>
            <a href="#" class="text-green-700 text-sm font-semibold hover:underline">Lihat Semua →</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach ([
                ['img' => 'program-1.jpg', 'title' => 'Mengaji & Tahsin'],
                ['img' => 'program-2.jpg', 'title' => 'Tahfidz Al-Qur\'an'],
                ['img' => 'program-3.jpg', 'title' => 'Pendidikan Akhlak'],
                ['img' => 'program-4.jpg', 'title' => 'Kajian Islam'],
            ] as $program)
            <div class="rounded-2xl overflow-hidden shadow group cursor-pointer">
                <div class="relative h-48">
                    <img src="{{ asset('images/' . $program['img']) }}"
                         alt="{{ $program['title'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                         onerror="this.src='https://placehold.co/400x300/16a34a/white?text={{ urlencode($program['title']) }}'">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition"></div>
                </div>
                <div class="bg-white px-4 py-3">
                    <p class="font-semibold text-gray-800 text-sm">{{ $program['title'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════
     KAMI MEMBIMBING
═══════════════════════════════════════ --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">

            {{-- Teks kiri --}}
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight mb-4">
                    Kami Membimbing<br>
                    dengan
                    <span class="text-green-700 italic">Ilmu &amp; Akhlak</span>
                </h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    IQRA' Creative House berfokus pada pembelajaran Al-Qur'an, pembentukan akhlak, dan pengembangan
                    potensi peserta didik dengan pendekatan islami dan kreatif.
                </p>
                <a href="{{ route('register') }}"
                   class="inline-block px-8 py-3 bg-green-700 hover:bg-green-800 text-white font-bold rounded-xl transition">
                    Daftar Sekarang
                </a>
            </div>

            {{-- Foto guru + badge kanan --}}
            <div class="relative flex justify-center">
                <div class="relative">
                    <div class="w-72 h-72 rounded-full overflow-hidden border-4 border-green-700 shadow-xl">
                        <img src="{{ asset('images/guru-cta.jpg') }}" alt="Guru"
                             class="w-full h-full object-cover"
                             onerror="this.src='https://placehold.co/300x300/16a34a/white?text=Guru+ICH'">
                    </div>
                    {{-- Badge --}}
                    <div class="absolute -bottom-4 -right-4 bg-yellow-400 text-white font-bold text-xs
                                rounded-full w-24 h-24 flex items-center justify-center text-center shadow-lg leading-tight p-2">
                        DAFTAR<br>SEKARANG<br>BISA
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════
     KEGIATAN SISWA
═══════════════════════════════════════ --}}
<section id="kegiatan" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Kegiatan Siswa</h2>

        <div class="grid grid-cols-3 gap-4">
            @foreach ([
                ['img' => 'activity-1.jpg', 'label' => 'Belajar Mengaji Dan Doa'],
                ['img' => 'activity-2.jpg', 'label' => 'Tahsin & Tajwid Intensif'],
                ['img' => 'activity-3.jpg', 'label' => 'Tahfidz Iqra'],
                ['img' => 'activity-4.jpg', 'label' => 'Adab & Akhlak Islami'],
                ['img' => 'activity-5.jpg', 'label' => 'Kajian Islam Anak'],
                ['img' => 'activity-6.jpg', 'label' => 'Menulis & Dakwah Kreatif'],
            ] as $item)
            <div class="group">
                <div class="relative rounded-xl overflow-hidden h-44">
                    <img src="{{ asset('images/' . $item['img']) }}"
                         alt="{{ $item['label'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                         onerror="this.src='https://placehold.co/400x300/16a34a/white?text={{ urlencode($item['label']) }}'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                </div>
                <p class="mt-2 text-sm font-medium text-gray-700 text-center">{{ $item['label'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════
     MEMBINA PERKEMBANGAN ANAK
═══════════════════════════════════════ --}}
<section class="py-20 bg-gradient-to-br from-yellow-100 to-yellow-200">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">

            <div>
                <h2 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                    <span class="text-gray-900">Membina</span><br>
                    <span class="text-green-700">Perkembangan</span><br>
                    <span class="text-gray-900">Anak</span>
                </h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Setiap anak mendapatkan edukasi berkualitas untuk mengembangkan potensi, pembentukan akhlak, dan
                    pemahaman perkembangan berjalan optimal.
                </p>
                <a href="{{ route('register') }}"
                   class="inline-block px-8 py-3 bg-green-700 hover:bg-green-800 text-white font-bold rounded-xl transition">
                    Daftar Sekarang
                </a>
            </div>

            <div class="flex justify-center">
                <div class="relative w-80 h-80">
                    <img src="{{ asset('images/guru-cta-2.jpg') }}" alt="Guru"
                         class="w-full h-full object-cover rounded-2xl shadow-xl"
                         onerror="this.src='https://placehold.co/320x320/16a34a/white?text=ICH+Pendidikan'">
                    <div class="absolute -top-4 -right-4 bg-green-700 text-white font-bold text-xs
                                rounded-full w-20 h-20 flex items-center justify-center text-center shadow-lg p-2 leading-tight">
                        PENDAFTARAN<br>DIBUKA
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════
     ULASAN
═══════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
            Apa Kata Siswa<br>&amp; Orang Tua
        </h2>

        <div class="grid md:grid-cols-2 gap-6">
            @foreach ([
                [
                    'name'   => 'Ibu Siti',
                    'avatar' => 'IS',
                    'text'   => 'Alhamdulillah, anak saya sangat senang belajar di sini. Guru-gurunya sangat sabar dan metode pengajarannya sangat baik untuk anak usia dini.',
                ],
                [
                    'name'   => 'Aisyah',
                    'avatar' => 'A',
                    'text'   => 'Aku suka belajar mengaji di sini karena bu guru sangat baik dan teman-temannya menyenangkan. Sudah hafal banyak surat!',
                ],
            ] as $ulasan)
            <div class="bg-gray-50 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center
                                text-white font-bold text-sm shrink-0">
                        {{ $ulasan['avatar'] }}
                    </div>
                    <span class="font-semibold text-gray-800">{{ $ulasan['name'] }}</span>
                </div>
                <p class="text-gray-600 leading-relaxed text-sm italic">
                    "{{ $ulasan['text'] }}"
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════
     HUBUNGI KAMI
═══════════════════════════════════════ --}}
<section id="kontak" class="py-20 bg-green-800">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12 items-start">

            {{-- Informasi Kontak --}}
            <div class="text-white">
                <h2 class="text-3xl font-bold mb-8">Informasi Kontak</h2>
                <div class="space-y-5">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-green-300 text-xs mb-0.5">Email</p>
                            <p class="text-sm">info@ich.sch.id</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-green-300 text-xs mb-0.5">Telepon</p>
                            <p class="text-sm">+62 xxx-xxxx-xxxx</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-green-300 text-xs mb-0.5">Alamat</p>
                            <p class="text-sm leading-relaxed">
                                Jl. Datuk Kabu Gang Ridho No. 11,<br>
                                Tembung, Percut Sei Tuan, Deli Serdang
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form class="space-y-4">
                <div>
                    <input type="text" placeholder="Nama Anda"
                           class="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white
                                  placeholder-white/50 focus:outline-none focus:border-white text-sm">
                </div>
                <div>
                    <input type="email" placeholder="Email Anda"
                           class="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white
                                  placeholder-white/50 focus:outline-none focus:border-white text-sm">
                </div>
                <div>
                    <textarea rows="4" placeholder="Pesan Anda..."
                              class="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white
                                     placeholder-white/50 focus:outline-none focus:border-white text-sm resize-none"></textarea>
                </div>
                <button type="submit"
                        class="w-full py-3 bg-yellow-400 hover:bg-yellow-500 text-white font-bold rounded-lg transition">
                    Kirim Pesan
                </button>
            </form>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════
     FOOTER
═══════════════════════════════════════ --}}
<footer class="bg-[#0d1f0d] text-white py-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-3 gap-10 mb-10">

            {{-- Logo & desc --}}
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="ICH"
                         class="w-10 h-10 object-contain bg-white rounded-lg p-1"
                         onerror="this.style.display='none'">
                    <span class="font-bold text-base">IQRA' CREATIVE HOUSE</span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Lembaga pendidikan anak usia dini berbasis islami di Deli Serdang, Sumatera Utara.
                </p>
                <div class="flex gap-3 mt-5">
                    @foreach (['facebook', 'instagram', 'youtube'] as $sosmed)
                    <a href="#" class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-full
                                      flex items-center justify-center transition text-xs font-bold">
                        {{ strtoupper(substr($sosmed, 0, 1)) }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Nav links --}}
            <div>
                <h3 class="font-semibold mb-4 text-sm uppercase tracking-wider text-gray-300">Navigasi</h3>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="#" class="hover:text-white transition">Beranda</a></li>
                    <li><a href="#tentang" class="hover:text-white transition">Tentang Kami</a></li>
                    <li><a href="#program" class="hover:text-white transition">Program</a></li>
                    <li><a href="#kegiatan" class="hover:text-white transition">Kegiatan</a></li>
                    <li><a href="#kontak" class="hover:text-white transition">Kontak</a></li>
                </ul>
            </div>

            {{-- Email subscription --}}
            <div>
                <h3 class="font-semibold mb-4 text-sm uppercase tracking-wider text-gray-300">Newsletter</h3>
                <p class="text-gray-400 text-sm mb-4">
                    Berikan email anda untuk info tanjutan
                </p>
                <div class="flex gap-2">
                    <input type="email" placeholder="Email anda..."
                           class="flex-1 px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white
                                  placeholder-white/40 focus:outline-none focus:border-white text-sm">
                    <button class="px-4 py-2 bg-green-700 hover:bg-green-600 rounded-lg text-sm font-semibold transition">
                        Kirim
                    </button>
                </div>
            </div>
        </div>

        {{-- Divider + copyright --}}
        <div class="border-t border-white/10 pt-6 text-center text-gray-500 text-xs">
            Copyright &copy; {{ date('Y') }} IQRA' CREATIVE GROUP. All Rights Reserved.
        </div>
    </div>
</footer>

</body>
</html>
