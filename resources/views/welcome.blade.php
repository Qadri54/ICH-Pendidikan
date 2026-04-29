<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TK ICH - IQRA' CREATIVE HOUSE</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-800">

{{-- NAVBAR --}}
<nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
        <a href="/" class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="ICH Logo" class="w-10 h-10 object-contain">
            <span class="font-bold text-green-700 text-lg leading-tight">IQRA'<br><span class="text-xs font-medium text-gray-600">CREATIVE HOUSE</span></span>
        </a>

        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-700">
            <a href="#tentang" class="hover:text-green-700 transition">Tentang Kami</a>
            <a href="#program" class="hover:text-green-700 transition">Program</a>
            <a href="#kegiatan" class="hover:text-green-700 transition">Kegiatan</a>
            <a href="#kontak" class="hover:text-green-700 transition">Kontak</a>
        </div>

        <div class="hidden md:flex items-center gap-3">
            <a href="{{ route('login') }}"
               class="px-5 py-2 text-sm font-semibold text-green-700 border-2 border-green-700 rounded-lg hover:bg-green-50 transition">
                Masuk
            </a>
            <a href="{{ route('register') }}"
               class="px-5 py-2 text-sm font-semibold text-white bg-green-700 rounded-lg hover:bg-green-800 transition">
                Daftar
            </a>
        </div>

        {{-- Mobile burger --}}
        <button @click="open = !open" class="md:hidden text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition class="md:hidden px-6 pb-4 bg-white border-t">
        <a href="#tentang" class="block py-2 text-gray-700 hover:text-green-700">Tentang Kami</a>
        <a href="#program" class="block py-2 text-gray-700 hover:text-green-700">Program</a>
        <a href="#kegiatan" class="block py-2 text-gray-700 hover:text-green-700">Kegiatan</a>
        <a href="#kontak" class="block py-2 text-gray-700 hover:text-green-700">Kontak</a>
        <div class="flex gap-3 mt-3">
            <a href="{{ route('login') }}" class="flex-1 text-center py-2 text-sm font-semibold text-green-700 border-2 border-green-700 rounded-lg">Masuk</a>
            <a href="{{ route('register') }}" class="flex-1 text-center py-2 text-sm font-semibold text-white bg-green-700 rounded-lg">Daftar</a>
        </div>
    </div>
</nav>

{{-- HERO --}}
<section class="relative min-h-screen flex items-center pt-16">
    <img src="{{ asset('images/hero-bg.jpg') }}" alt="Hero" class="absolute inset-0 w-full h-full object-cover">
    <div class="absolute inset-0 bg-green-900/60"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-6 text-white">
        <div class="flex items-center gap-3 mb-4">
            <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">PG - TK</span>
        </div>
        <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-4">
            Membangun Generasi<br>
            <span class="text-yellow-400">Qurani &amp; Kreatif</span>
        </h1>
        <div class="flex flex-wrap gap-4 mb-8 text-sm">
            <span class="flex items-center gap-2 bg-white/20 px-4 py-2 rounded-full">
                ✅ Belajar Al-Quran
            </span>
            <span class="flex items-center gap-2 bg-white/20 px-4 py-2 rounded-full">
                ✅ Bimbingan Akhlak
            </span>
            <span class="flex items-center gap-2 bg-white/20 px-4 py-2 rounded-full">
                ✅ Program Terstruktur
            </span>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('register') }}"
               class="px-8 py-3 bg-yellow-400 hover:bg-yellow-500 text-white font-bold rounded-xl transition text-lg">
                Daftar Sekarang
            </a>
            <a href="#tentang"
               class="px-8 py-3 border-2 border-white text-white font-bold rounded-xl hover:bg-white/10 transition text-lg">
                Pelajari Lebih
            </a>
        </div>
    </div>
</section>

{{-- TENTANG KAMI --}}
<section id="tentang" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Tentang Kami</h2>
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <img src="{{ asset('images/about.jpg') }}" alt="Tentang Kami"
                     class="rounded-2xl w-full h-72 object-cover shadow-lg">
            </div>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-green-700 mb-2">Visi</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Menjadi lembaga belajar Islami yang unggul, profesional, dan terpercaya dalam membina generasi yang cinta Al-Qur'an, berakhlak mulia, berjiwa seni, berakal sehat, dan mengembangkan potensi peserta didik secara islami dan kreatif.
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-green-700 mb-2">Misi</h3>
                    <ul class="text-gray-600 space-y-2 leading-relaxed list-disc list-inside">
                        <li>Menyelenggarakan pembelajaran Al-Qur'an yang berkualitas dengan metode yang menyenangkan</li>
                        <li>Membangun karakter dan akhlak Islami pada setiap peserta didik</li>
                        <li>Menyediakan lingkungan belajar yang islami, kreatif, dan kondusif</li>
                        <li>Meningkatkan kualitas kelulusan yang islami dan kreatif</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- PROGRAM UNGGULAN --}}
<section id="program" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-4">Program Unggulan</h2>
        <p class="text-center text-gray-500 mb-12">Program terbaik untuk perkembangan anak</p>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ([
                ['icon' => '📖', 'title' => 'Mengaji & Tahsin', 'desc' => 'Pembelajaran Al-Qur\'an dengan metode yang menyenangkan dan efektif'],
                ['icon' => '🎓', 'title' => 'Tahfidz Al-Qur\'an', 'desc' => 'Program hafalan Al-Qur\'an terstruktur sesuai usia anak'],
                ['icon' => '🌟', 'title' => 'Pendidikan Akhlak', 'desc' => 'Pembentukan karakter islami dan akhlak mulia sejak dini'],
                ['icon' => '🕌', 'title' => 'Kajian Islam', 'desc' => 'Pengenalan dasar-dasar ajaran Islam yang menyenangkan'],
            ] as $program)
            <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition text-center">
                <div class="text-4xl mb-4">{{ $program['icon'] }}</div>
                <h3 class="font-bold text-gray-900 mb-2">{{ $program['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $program['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- MEMBINA PERKEMBANGAN ANAK --}}
<section class="py-20 bg-green-700">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="text-white">
                <h2 class="text-3xl font-bold mb-4">
                    Kami Membimbing dengan
                    <span class="text-yellow-400">Ilmu &amp; Akhlak</span>
                </h2>
                <p class="text-green-100 leading-relaxed mb-6">
                    IQRA' Creative House berfokus pada pembelajaran Al-Qur'an, pembentukan akhlak, dan pengembangan potensi peserta didik dengan pendekatan islami dan kreatif.
                </p>
                <a href="{{ route('register') }}"
                   class="inline-block px-8 py-3 bg-yellow-400 hover:bg-yellow-500 text-white font-bold rounded-xl transition">
                    Daftar Sekarang
                </a>
            </div>
            <div>
                <img src="{{ asset('images/cta.jpg') }}" alt="Bimbingan"
                     class="rounded-2xl w-full h-72 object-cover shadow-xl">
            </div>
        </div>
    </div>
</section>

{{-- KEGIATAN SISWA --}}
<section id="kegiatan" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-4">Kegiatan Siswa</h2>
        <p class="text-center text-gray-500 mb-12">Berbagai kegiatan yang mengembangkan potensi anak</p>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ([
                'Belajar Mengaji dan Doa',
                'Tahsin & Tajwid Intensif',
                'Tahfidz Iqra',
                'Adab & Akhlak Islami',
                'Kajian Islam Anak',
                'Menulis & Dakwah Kreatif',
            ] as $kegiatan)
            <div class="relative rounded-2xl overflow-hidden bg-gray-200 h-48 group">
                <img src="{{ asset('images/activity-' . ($loop->index + 1) . '.jpg') }}"
                     alt="{{ $kegiatan }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                     onerror="this.src='https://placehold.co/400x300/16a34a/white?text={{ urlencode($kegiatan) }}'">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <p class="absolute bottom-3 left-3 right-3 text-white text-sm font-semibold">{{ $kegiatan }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ULASAN --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-4">Apa Kata Siswa &amp; Orang Tua</h2>
        <p class="text-center text-gray-500 mb-12">Pengalaman mereka bersama kami</p>

        <div class="grid md:grid-cols-2 gap-6">
            @foreach ([
                ['name' => 'Ibu Siti', 'text' => 'Alhamdulillah, anak saya sangat senang belajar di sini. Guru-gurunya sabar dan metode pengajarannya sangat baik untuk anak usia dini.'],
                ['name' => 'Aisyah', 'text' => 'Saya suka belajar mengaji di sini karena bu guru sangat baik dan teman-temannya menyenangkan. Sudah hafal banyak surat!'],
            ] as $ulasan)
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr($ulasan['name'], 0, 1) }}
                    </div>
                    <span class="font-semibold text-gray-800">{{ $ulasan['name'] }}</span>
                </div>
                <p class="text-gray-600 leading-relaxed italic">"{{ $ulasan['text'] }}"</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- HUBUNGI KAMI --}}
<section id="kontak" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Hubungi Kami</h2>
                <div class="space-y-4 text-gray-600">
                    <div class="flex items-start gap-3">
                        <span class="text-green-700 mt-1">📍</span>
                        <p>Jl. Datuk Kabu Gang Ridho No. 11, Tembung, Percut Sei Tuan, Deli Serdang</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-green-700">📞</span>
                        <p>+62 xxx-xxxx-xxxx</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-green-700">✉️</span>
                        <p>info@ich.sch.id</p>
                    </div>
                </div>
            </div>

            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" placeholder="Nama Anda"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" placeholder="Email Anda"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                    <textarea rows="4" placeholder="Pesan Anda..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600 resize-none"></textarea>
                </div>
                <button type="submit"
                        class="w-full py-3 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-lg transition">
                    Kirim Pesan
                </button>
            </form>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer class="bg-green-900 text-white py-10">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-3 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="ICH" class="w-10 h-10 object-contain bg-white rounded-lg p-1">
                    <span class="font-bold text-lg">IQRA' CREATIVE HOUSE</span>
                </div>
                <p class="text-green-200 text-sm leading-relaxed">
                    Lembaga pendidikan anak usia dini berbasis islami di Deli Serdang, Sumatera Utara.
                </p>
            </div>
            <div>
                <h3 class="font-semibold mb-4">Navigasi</h3>
                <ul class="space-y-2 text-green-200 text-sm">
                    <li><a href="#tentang" class="hover:text-white transition">Tentang Kami</a></li>
                    <li><a href="#program" class="hover:text-white transition">Program</a></li>
                    <li><a href="#kegiatan" class="hover:text-white transition">Kegiatan</a></li>
                    <li><a href="#kontak" class="hover:text-white transition">Kontak</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold mb-4">Akses Sistem</h3>
                <ul class="space-y-2 text-green-200 text-sm">
                    <li><a href="{{ route('login') }}" class="hover:text-white transition">Login</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar Akun</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-green-800 pt-6 text-center text-green-300 text-sm">
            Copyright &copy; {{ date('Y') }} IQRA' CREATIVE GROUP. All Rights Reserved.
        </div>
    </div>
</footer>

</body>
</html>
