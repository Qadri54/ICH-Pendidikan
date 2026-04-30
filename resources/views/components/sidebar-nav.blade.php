@php
    $adminMenu = [
        ['label' => 'Dashboard',    'route' => 'dashboard',          'icon' => 'grid'],
        ['label' => 'Siswa',        'route' => 'admin.siswa.index',  'icon' => 'user'],
        ['label' => 'Guru',         'route' => 'admin.guru.index',   'icon' => 'users'],
        ['label' => 'Orang Tua',    'route' => 'admin.user.index',   'icon' => 'user-circle'],
        ['label' => 'Kelas',        'route' => 'admin.kelas.index',  'icon' => 'school'],
        ['label' => 'Keuangan',     'route' => 'admin.keuangan.index','icon' => 'banknote'],
        ['label' => 'Tabungan',     'route' => 'admin.tabungan.index','icon' => 'piggy'],
        ['label' => 'Laporan',      'route' => 'admin.laporan.index','icon' => 'document'],
        ['label' => 'Pendaftaran',  'route' => 'admin.pendaftaran.index','icon' => 'clipboard'],
        ['label' => 'Pengaturan',   'route' => 'admin.pengaturan.index','icon' => 'cog'],
    ];

    $guruMenu = [
        ['label' => 'Dashboard',    'route' => 'dashboard',         'icon' => 'grid'],
        ['label' => 'Pengelolaan',  'route' => 'pengelolaan.index', 'icon' => 'user'],
        ['label' => 'Profil Anak',  'route' => 'profil-anak.index', 'icon' => 'user-group'],
        ['label' => 'Akademik',     'route' => 'akademik.index',    'icon' => 'academic-cap'],
        ['label' => 'Kehadiran',    'route' => 'kehadiran.index',   'icon' => 'check-circle'],
        ['label' => 'Tabungan',     'route' => 'tabungan.index',    'icon' => 'savings'],
        ['label' => 'Pengaturan',   'route' => 'pengaturan.index',  'icon' => 'cog'],
    ];

    $orangTuaMenu = [
        ['label' => 'Dashboard',    'route' => 'dashboard',         'icon' => 'grid'],
        ['label' => 'Pendaftaran',  'route' => 'pendaftaran.index', 'icon' => 'clipboard'],
        ['label' => 'Profil Anak',  'route' => 'profil-anak.index', 'icon' => 'user-group'],
        ['label' => 'Akademik',     'route' => 'akademik.index',    'icon' => 'academic-cap'],
        ['label' => 'Keuangan',     'route' => 'keuangan.index',    'icon' => 'cash'],
        ['label' => 'Tabungan',     'route' => 'tabungan.index',    'icon' => 'savings'],
        ['label' => 'Kelulusan',    'route' => 'kelulusan.index',   'icon' => 'badge-check'],
        ['label' => 'Pengaturan',   'route' => 'pengaturan.index',  'icon' => 'cog'],
    ];

    $menus = match($role) {
        'Admin', 'Kepala Sekolah', 'Kepala Yayasan' => $adminMenu,
        'Guru', 'Guru Ngaji'                         => $guruMenu,
        'Orang Tua'                                  => $orangTuaMenu,
        default                                      => $adminMenu,
    };
@endphp

@foreach ($menus as $item)
    @php
        $base     = str_replace('.index', '', $item['route']);
        $isActive = request()->routeIs($base) || request()->routeIs($base . '.*');
    @endphp
    <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
       class="flex flex-col items-center gap-1 w-full px-2 py-2 rounded-lg text-center transition
              {{ $isActive ? 'bg-white/20 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
        <x-nav-icon :name="$item['icon']" class="w-5 h-5" />
        <span class="text-xs leading-tight">{{ $item['label'] }}</span>
    </a>
@endforeach
