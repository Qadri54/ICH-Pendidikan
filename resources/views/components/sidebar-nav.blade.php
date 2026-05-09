@php
    // Semua icon name mengacu ke x-ich-icon (ich-icon.blade.php)
    $adminMenu = [
        ['label' => 'Laporan',        'route' => 'admin.laporan.index',               'icon' => 'document'],
        ['label' => 'Siswa',          'route' => 'admin.siswa.index',                 'icon' => 'user'],
        ['label' => 'Guru',           'route' => 'admin.guru.index',                  'icon' => 'users'],
        ['label' => 'User',           'route' => 'admin.user.index',                  'icon' => 'user_circle'],
        ['label' => 'Kelas',          'route' => 'admin.kelas.index',                 'icon' => 'school'],
        ['label' => 'Absensi Siswa',  'route' => 'admin.absensi.index',               'icon' => 'calendar'],
        ['label' => 'Absensi Guru',   'route' => 'admin.absensi-guru.index',          'icon' => 'user_check'],
        ['label' => 'Raport',         'route' => 'admin.raport.index',                'icon' => 'book'],
        ['label' => 'Keuangan',       'route' => 'admin.keuangan.index',              'icon' => 'banknote'],
        ['label' => 'Pmbyr Daftar',   'route' => 'admin.pembayaran-pendaftaran.index','icon' => 'card'],
        ['label' => 'Tabungan',       'route' => 'admin.tabungan.index',              'icon' => 'piggy'],
        ['label' => 'Pendaftaran',    'route' => 'admin.pendaftaran.index',           'icon' => 'clipboard'],
        ['label' => 'Pengaturan',     'route' => 'admin.pengaturan.index',            'icon' => 'settings'],
    ];

    $guruMenu = [
        ['label' => 'Dashboard',   'route' => 'dashboard',              'icon' => 'grid'],
        ['label' => 'Absensi Saya','route' => 'guru.absensi-guru.index','icon' => 'user_check'],
        ['label' => 'Absensi Siswa','route' => 'guru.absensi.index',   'icon' => 'calendar'],
        ['label' => 'Raport',      'route' => 'guru.raport.index',      'icon' => 'book'],
        ['label' => 'Tabungan',    'route' => 'guru.tabungan.index',    'icon' => 'piggy'],
        ['label' => 'Profil',      'route' => 'profile.edit',           'icon' => 'settings'],
    ];

    $orangTuaMenu = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'grid'],
        ['label' => 'Pendaftaran', 'route' => 'pendaftaran', 'icon' => 'clipboard'],
        ['label' => 'Profil Anak', 'route' => 'profil-anak', 'icon' => 'user_circle'],
        ['label' => 'Akademik', 'route' => 'akademik', 'icon' => 'book'],
        ['label' => 'Keuangan', 'route' => 'keuangan', 'icon' => 'banknote'],
        ['label' => 'Tabungan', 'route' => 'tabungan', 'icon' => 'piggy'],
        ['label' => 'Pengaturan', 'route' => 'pengaturan', 'icon' => 'settings'],
    ];

    $menus = match ($role) {
        'Admin', 'Kepala Sekolah', 'Kepala Yayasan' => $adminMenu,
        'Guru', 'Guru Ngaji' => $guruMenu,
        'Orang Tua' => $orangTuaMenu,
        default => $adminMenu,
    };
@endphp

@foreach ($menus as $item)
    @php
        $base = str_replace('.index', '', $item['route']);
        $isActive = request()->routeIs($base) || request()->routeIs($base . '.*');
    @endphp
    <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}" class="flex flex-col items-center gap-1 w-full px-2 py-2.5 rounded-lg text-center transition-colors
                  {{ $isActive
            ? 'bg-white/25 text-white'
            : 'text-white/75 hover:bg-white/15 hover:text-white' }}">
        <x-ich-icon :name="$item['icon']" :size="22" color="currentColor" />
        <span class="text-[10px] font-ui font-semibold leading-tight mt-0.5">{{ $item['label'] }}</span>
    </a>
@endforeach
