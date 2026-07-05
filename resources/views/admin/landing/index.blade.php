<x-main-layout title="Kelola Landing Page">

    <div class="mb-6 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-ich-blue-soft flex items-center justify-center">
            <svg class="w-5 h-5 text-ich-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12L12 4l9 8M5 10v10h4v-6h6v6h4V10"/></svg>
        </div>
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Kelola Landing Page</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Edit konten, gambar, dan struktur organisasi pada halaman utama</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-w-5xl">
        @foreach($sections as $section)
            @php
                $icons = [
                    'hero' => ['bg' => 'bg-ich-green-surface', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/>'],
                    'tentang' => ['bg' => 'bg-ich-blue-soft', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>'],
                    'struktur' => ['bg' => 'bg-ich-purple-soft', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                    'program' => ['bg' => 'bg-ich-green-surface', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
                    'aktivitas' => ['bg' => 'bg-ich-warning-soft', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
                    'testimoni' => ['bg' => 'bg-ich-pink-soft', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>'],
                    'cta' => ['bg' => 'bg-ich-green-surface', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>'],
                    'footer' => ['bg' => 'bg-gray-100', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>'],
                ];
                $style = $icons[$section->key] ?? ['bg' => 'bg-gray-100', 'icon' => ''];
            @endphp
            <div class="bg-white rounded-xl shadow-ich-card p-5 flex flex-col gap-4 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $style['bg'] }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $style['icon'] !!}</svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-display font-bold text-ich-ink-900 text-sm">{{ $section->title }}</h3>
                        <p class="text-xs text-ich-ink-400">{{ $section->key }}</p>
                    </div>
                    <form action="{{ route('admin.landing.toggle', $section->key) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs px-2 py-1 rounded-full font-semibold {{ $section->is_active ? 'bg-ich-success-soft text-ich-success' : 'bg-gray-100 text-ich-ink-400' }}">
                            {{ $section->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </form>
                </div>
                <a href="{{ route('admin.landing.edit', $section->key) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-ich-green text-white rounded-lg text-sm font-ui font-bold hover:bg-ich-green/90 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Konten
                </a>
            </div>
        @endforeach
    </div>

</x-main-layout>
