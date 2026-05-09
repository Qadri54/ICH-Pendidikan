<x-main-layout title="Dashboard">

    {{-- Greeting --}}
    <div class="bg-ich-green rounded-xl p-6 text-white mb-6">
        <p class="font-sans text-sm opacity-80">Selamat datang,</p>
        <p class="font-display font-bold text-2xl mt-0.5">{{ $user->name }}</p>
        <p class="font-sans text-xs opacity-70 mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Quick Actions --}}
    <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide mb-3">Menu Cepat</p>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @php
        $menus = [
            [
                'label'  => 'Absensi Saya',
                'desc'   => 'Check-in & check-out',
                'icon'   => 'check_circle',
                'route'  => 'guru.absensi-guru.index',
                'color'  => 'bg-[#E8F5EA] text-ich-green',
            ],
            [
                'label'  => 'Absensi Siswa',
                'desc'   => 'Input kehadiran kelas',
                'icon'   => 'calendar',
                'route'  => 'guru.absensi.index',
                'color'  => 'bg-[#F0F4FF] text-ich-teal',
            ],
            [
                'label'  => 'Raport',
                'desc'   => 'Penilaian perkembangan',
                'icon'   => 'book',
                'route'  => 'guru.raport.index',
                'color'  => 'bg-[#EDE9FE] text-[#8B5CF6]',
            ],
            [
                'label'  => 'Tabungan',
                'desc'   => 'Kelola buku tabungan',
                'icon'   => 'piggy',
                'route'  => 'guru.tabungan.index',
                'color'  => 'bg-[#FEF5DC] text-[#E09F17]',
            ],
            [
                'label'  => 'Profil',
                'desc'   => 'Pengaturan akun',
                'icon'   => 'settings',
                'route'  => 'profile.edit',
                'color'  => 'bg-[#F3F4F6] text-ich-ink-500',
            ],
        ];
        @endphp

        @foreach($menus as $menu)
            <a href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
               class="bg-white rounded-xl shadow-ich-card p-5 flex flex-col gap-3
                      hover:shadow-md transition-shadow no-underline group">
                <div class="w-12 h-12 rounded-xl {{ $menu['color'] }} flex items-center justify-center
                            group-hover:scale-105 transition-transform">
                    <x-ich-icon :name="$menu['icon']" :size="24" color="currentColor"/>
                </div>
                <div>
                    <p class="font-ui font-bold text-sm text-ich-ink-900">{{ $menu['label'] }}</p>
                    <p class="font-sans text-xs text-ich-ink-400 mt-0.5">{{ $menu['desc'] }}</p>
                </div>
            </a>
        @endforeach
    </div>

</x-main-layout>
