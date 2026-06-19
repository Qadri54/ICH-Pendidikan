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
                    <p class="font-sans text-sm opacity-80">Selamat datang kembali,</p>
                    <p class="font-display font-bold text-xl">{{ $user->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-3">
                <span class="px-3 py-1 bg-white/15 rounded-full text-xs font-ui font-bold">{{ $role }}</span>
                <span class="text-xs opacity-70">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-ich-card p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-ich-green-surface flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-ich-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <div class="text-ich-ink-400 text-xs font-sans mb-0.5">Total Siswa</div>
                <div class="text-2xl font-display font-bold text-ich-ink-900">{{ $stats['total_siswa'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-ich-blue-soft flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-ich-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <div class="text-ich-ink-400 text-xs font-sans mb-0.5">Total Guru</div>
                <div class="text-2xl font-display font-bold text-ich-ink-900">{{ $stats['total_guru'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-ich-error-soft flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-ich-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-ich-ink-400 text-xs font-sans mb-0.5">Tagihan Berjalan</div>
                <div class="text-2xl font-display font-bold text-ich-error">{{ $stats['tagihan_berjalan'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-ich-success-soft flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-ich-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-ich-ink-400 text-xs font-sans mb-0.5">Tagihan Lunas</div>
                <div class="text-2xl font-display font-bold text-ich-success">{{ $stats['tagihan_lunas'] }}</div>
            </div>
        </div>
    </div>

    {{-- Revenue & Alerts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        {{-- Revenue highlight --}}
        <div class="lg:col-span-2 bg-gradient-to-br from-ich-green to-ich-gradient-end rounded-xl shadow-ich-card p-6 relative overflow-hidden">
            <div class="absolute -bottom-4 -right-4 w-28 h-28 bg-white/5 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-white/70 text-xs font-sans">Total Pendapatan (SPP + Pendaftaran)</span>
                </div>
                <div class="text-3xl font-display font-bold text-white mb-4">
                    Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 rounded-lg p-3">
                        <div class="text-white/60 text-xs font-sans mb-1">Total Tabungan</div>
                        <div class="text-lg font-display font-bold text-white">
                            Rp {{ number_format($stats['total_tabungan'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="bg-white/10 rounded-lg p-3">
                        <div class="text-white/60 text-xs font-sans mb-1">Tagihan Aktif</div>
                        <div class="text-lg font-display font-bold text-white">
                            {{ $stats['tagihan_berjalan'] }} tagihan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts / Pending --}}
        <div class="space-y-4">
            @if($stats['pending_daftar'] > 0)
                <a href="{{ route('admin.pendaftaran.index') }}" class="block bg-white rounded-xl shadow-ich-card p-5 hover:shadow-md transition-shadow no-underline group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-ich-warning-soft flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-ich-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div>
                            <p class="font-ui font-bold text-sm text-ich-ink-900">Pendaftaran Pending</p>
                            <p class="text-xs text-ich-ink-400">{{ $stats['pending_daftar'] }} menunggu approval</p>
                        </div>
                        <svg class="w-4 h-4 text-ich-ink-300 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            @endif
            @if($stats['pending_raport'] > 0)
                <a href="{{ route('admin.raport.index') }}" class="block bg-white rounded-xl shadow-ich-card p-5 hover:shadow-md transition-shadow no-underline group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-ich-purple-soft flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-ich-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="font-ui font-bold text-sm text-ich-ink-900">Raport Menunggu</p>
                            <p class="text-xs text-ich-ink-400">{{ $stats['pending_raport'] }} menunggu approval</p>
                        </div>
                        <svg class="w-4 h-4 text-ich-ink-300 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            @endif
            @if($stats['pending_daftar'] == 0 && $stats['pending_raport'] == 0)
                <div class="bg-white rounded-xl shadow-ich-card p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-ich-success-soft flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-ich-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <p class="font-ui font-bold text-sm text-ich-ink-900">Semua Beres!</p>
                            <p class="text-xs text-ich-ink-400">Tidak ada yang perlu ditindaklanjuti</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mb-6">
        <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide mb-3">Menu Cepat</p>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @php
            $menus = [
                ['label' => 'Keuangan',       'desc' => 'SPP & tagihan',           'icon' => 'banknote',  'route' => 'admin.keuangan.index',    'color' => 'bg-ich-green-surface text-ich-green'],
                ['label' => 'Siswa',          'desc' => 'Data siswa',              'icon' => 'user',      'route' => 'admin.siswa.index',        'color' => 'bg-ich-blue-soft text-ich-teal'],
                ['label' => 'Raport',         'desc' => 'Penilaian siswa',         'icon' => 'book',      'route' => 'admin.raport.index',       'color' => 'bg-ich-purple-soft text-ich-purple'],
                ['label' => 'Absensi Siswa',  'desc' => 'Kehadiran siswa',         'icon' => 'calendar',  'route' => 'admin.absensi.index',      'color' => 'bg-ich-warning-soft text-ich-warning'],
                ['label' => 'Absensi Guru',   'desc' => 'Kehadiran guru',          'icon' => 'user_check','route' => 'admin.absensi-guru.index', 'color' => 'bg-ich-pink-soft text-pink-500'],
                ['label' => 'Pendaftaran',    'desc' => 'PPDB online',             'icon' => 'clipboard', 'route' => 'admin.pendaftaran.index',  'color' => 'bg-ich-blue-soft text-blue-500'],
                ['label' => 'Laporan',        'desc' => 'Ringkasan & export',      'icon' => 'document',  'route' => 'admin.laporan.index',      'color' => 'bg-orange-50 text-orange-500'],
                ['label' => 'Pengaturan',     'desc' => 'Konfigurasi sistem',      'icon' => 'settings',  'route' => 'admin.pengaturan.index',   'color' => 'bg-gray-100 text-ich-ink-500'],
            ];
            @endphp

            @foreach($menus as $menu)
                <a href="{{ route($menu['route']) }}"
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
    </div>

    {{-- Recent SPP Payments --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="px-6 py-4 border-b border-ich-line flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-ich-green-surface flex items-center justify-center">
                    <svg class="w-4 h-4 text-ich-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h2 class="font-ui font-bold text-ich-ink-900">Pembayaran SPP Terbaru</h2>
                    <p class="text-xs text-ich-ink-400 mt-0.5">5 transaksi lunas terakhir</p>
                </div>
            </div>
            <a href="{{ route('admin.keuangan.index') }}?status=paid"
               class="text-xs font-ui font-bold text-ich-teal hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-ich-surface">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Periode</th>
                        <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($recentPayments as $inv)
                        <tr class="hover:bg-ich-surface transition-colors">
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                {{ $inv->student?->nama_siswa ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-ich-green-surface text-ich-green font-ui font-bold text-xs rounded-full">
                                    {{ $inv->student?->classRoom?->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-ich-ink-500">
                                {{ $inv->tanggal_tahun?->translatedFormat('F Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-ui font-semibold text-ich-green">
                                Rp {{ number_format($inv->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                Belum ada pembayaran SPP yang lunas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-main-layout>
