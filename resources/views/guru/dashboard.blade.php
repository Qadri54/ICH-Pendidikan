<x-main-layout title="Dashboard">

    {{-- Greeting --}}
    <div class="bg-gradient-to-br from-ich-green to-ich-gradient-end rounded-2xl p-6 text-white mb-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-1/2 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
        <div class="relative">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-full bg-white/15 flex items-center justify-center text-lg font-display font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-sans text-sm opacity-80">Selamat datang,</p>
                    <p class="font-display font-bold text-xl">{{ $user->name }}</p>
                </div>
            </div>
            <p class="text-xs opacity-70 mt-2">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide mb-3">Menu Cepat</p>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @php
        $menus = [
            [
                'label'  => 'Absensi Saya',
                'desc'   => 'Absensi kehadiran',
                'icon'   => 'user_check',
                'route'  => 'guru.absensi-guru.index',
                'color'  => 'bg-ich-green-surface text-ich-green',
            ],
            [
                'label'  => 'Absensi Siswa',
                'desc'   => 'Input kehadiran kelas',
                'icon'   => 'calendar',
                'route'  => 'guru.absensi.index',
                'color'  => 'bg-ich-blue-soft text-ich-teal',
            ],
            [
                'label'  => 'Raport',
                'desc'   => 'Penilaian perkembangan',
                'icon'   => 'book',
                'route'  => 'guru.raport.index',
                'color'  => 'bg-ich-purple-soft text-ich-purple',
            ],
            [
                'label'  => 'Tabungan',
                'desc'   => 'Kelola buku tabungan',
                'icon'   => 'piggy',
                'route'  => 'guru.tabungan.index',
                'color'  => 'bg-ich-warning-soft text-ich-warning',
            ],
            [
                'label'  => 'Profil',
                'desc'   => 'Pengaturan akun',
                'icon'   => 'settings',
                'route'  => 'profile.edit',
                'color'  => 'bg-gray-100 text-ich-ink-500',
            ],
        ];
        @endphp

        @foreach($menus as $menu)
            <a href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
               class="bg-white rounded-xl shadow-ich-card p-5 flex flex-col gap-3
                      hover:shadow-md transition-all no-underline group hover:-translate-y-0.5">
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

    {{-- Rekap Absensi Saya --}}
    @if(isset($records))
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-ich-line">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-ich-pink-soft flex items-center justify-center">
                        <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-ui font-bold text-ich-ink-900">Rekap Absensi Saya</h2>
                        <p class="text-xs text-ich-ink-400 mt-0.5">Riwayat kehadiran per bulan</p>
                    </div>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <input type="month" name="bulan" value="{{ $bulan }}"
                           onchange="this.form.submit()"
                           class="border border-ich-line rounded-lg px-3 py-1.5 text-sm font-sans text-ich-ink-900 focus:outline-none focus:ring-2 focus:ring-ich-green/30">
                </form>
            </div>
        </div>

        {{-- Summary pills --}}
        <div class="px-6 py-3 bg-ich-surface flex flex-wrap gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-success-soft text-ich-success text-xs font-ui font-bold">
                Hadir {{ $recapSummary['hadir'] }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-info-soft text-ich-info text-xs font-ui font-bold">
                Sakit {{ $recapSummary['sakit'] }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-purple-soft text-ich-purple text-xs font-ui font-bold">
                Izin {{ $recapSummary['izin'] }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-error-soft text-ich-error text-xs font-ui font-bold">
                Tanpa Keterangan {{ $recapSummary['tanpa_keterangan'] }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-ich-ink-600 text-xs font-ui font-bold">
                Total {{ $recapSummary['total'] }} hari
            </span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-ich-surface">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">No</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Tanggal</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Jam Masuk</th>
                        <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($records as $i => $rec)
                        <tr class="hover:bg-ich-surface transition-colors">
                            <td class="px-4 py-3 text-ich-ink-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                {{ $rec->check_in_time?->translatedFormat('l, d M Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-ich-ink-600">
                                {{ $rec->check_in_time?->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @switch($rec->attendance_status)
                                    @case('Hadir')
                                        <span class="px-2.5 py-1 bg-ich-success-soft text-ich-success font-ui font-bold text-xs rounded-full">Hadir</span>
                                        @break
                                    @case('Sakit')
                                        <span class="px-2.5 py-1 bg-ich-info-soft text-ich-info font-ui font-bold text-xs rounded-full">Sakit</span>
                                        @break
                                    @case('Izin')
                                        <span class="px-2.5 py-1 bg-ich-purple-soft text-ich-purple font-ui font-bold text-xs rounded-full">Izin</span>
                                        @break
                                    @case('Tanpa Keterangan')
                                        <span class="px-2.5 py-1 bg-ich-error-soft text-ich-error font-ui font-bold text-xs rounded-full">Tanpa Ket.</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                Belum ada data absensi pada bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Daftar Hadir Siswa --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-ich-line">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-ich-warning-soft flex items-center justify-center">
                        <svg class="w-4 h-4 text-ich-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-ui font-bold text-ich-ink-900">Daftar Hadir Siswa</h2>
                        <p class="text-xs text-ich-ink-400 mt-0.5">Kehadiran siswa per tanggal</p>
                    </div>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <input type="date" name="tanggal" value="{{ $tanggal }}"
                           onchange="this.form.submit()"
                           class="border border-ich-line rounded-lg px-3 py-1.5 text-sm font-sans text-ich-ink-900 focus:outline-none focus:ring-2 focus:ring-ich-green/30">
                    <select name="kelas" onchange="this.form.submit()"
                            class="border border-ich-line rounded-lg px-3 py-1.5 text-sm font-sans text-ich-ink-900 focus:outline-none focus:ring-2 focus:ring-ich-green/30">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $kelas)
                            <option value="{{ $kelas->class_id }}" {{ $filterKelas == $kelas->class_id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        {{-- Summary pills --}}
        <div class="px-6 py-3 bg-ich-surface flex flex-wrap gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-success-soft text-ich-success text-xs font-ui font-bold">
                Hadir {{ $attendanceSummary['hadir'] }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-info-soft text-ich-info text-xs font-ui font-bold">
                Sakit {{ $attendanceSummary['sakit'] }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-purple-soft text-ich-purple text-xs font-ui font-bold">
                Izin {{ $attendanceSummary['izin'] }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-error-soft text-ich-error text-xs font-ui font-bold">
                Alpha {{ $attendanceSummary['alpha'] }}
            </span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-ich-surface">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">No</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                        <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($attendances as $i => $att)
                        <tr class="hover:bg-ich-surface transition-colors">
                            <td class="px-4 py-3 text-ich-ink-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                {{ $att->student?->nama_siswa ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-ich-green-surface text-ich-green font-ui font-bold text-xs rounded-full">
                                    {{ $att->student?->classRoom?->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @switch($att->status)
                                    @case('Hadir')
                                        <span class="px-2.5 py-1 bg-ich-success-soft text-ich-success font-ui font-bold text-xs rounded-full">Hadir</span>
                                        @break
                                    @case('Sakit')
                                        <span class="px-2.5 py-1 bg-ich-info-soft text-ich-info font-ui font-bold text-xs rounded-full">Sakit</span>
                                        @break
                                    @case('Izin')
                                        <span class="px-2.5 py-1 bg-ich-purple-soft text-ich-purple font-ui font-bold text-xs rounded-full">Izin</span>
                                        @break
                                    @case('Alpha')
                                        <span class="px-2.5 py-1 bg-ich-error-soft text-ich-error font-ui font-bold text-xs rounded-full">Alpha</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                Belum ada data kehadiran pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-main-layout>
