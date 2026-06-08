<x-main-layout title="Dashboard">

    {{-- Greeting --}}
    <div class="bg-ich-green rounded-xl p-6 text-white mb-6">
        <p class="font-sans text-sm opacity-80">Selamat datang,</p>
        <p class="font-display font-bold text-2xl mt-0.5">{{ $user->name }}</p>
        <p class="font-sans text-xs opacity-70 mt-1">{{ $role }} &middot; {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Total Siswa</div>
            <div class="text-3xl font-display font-bold text-ich-green">{{ $stats['total_siswa'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Total Guru</div>
            <div class="text-3xl font-display font-bold text-ich-teal">{{ $stats['total_guru'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Tagihan Berjalan</div>
            <div class="text-3xl font-display font-bold text-ich-error">{{ $stats['tagihan_berjalan'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Tagihan Lunas</div>
            <div class="text-3xl font-display font-bold text-[#009966]">{{ $stats['tagihan_lunas'] }}</div>
        </div>
    </div>

    {{-- Revenue highlight --}}
    <div class="bg-ich-green rounded-xl shadow-ich-card p-5 mb-6">
        <div class="text-white/70 text-xs font-sans mb-1">Total Pendapatan (SPP + Pendaftaran)</div>
        <div class="text-3xl font-display font-bold text-white">
            Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}
        </div>
        <div class="flex gap-6 mt-3">
            <div>
                <div class="text-white/60 text-xs font-sans">Total Tabungan</div>
                <div class="text-base font-display font-bold text-white">
                    Rp {{ number_format($stats['total_tabungan'], 0, ',', '.') }}
                </div>
            </div>
            @if($stats['pending_daftar'] > 0)
                <div class="w-px bg-white/20"></div>
                <div>
                    <div class="text-white/60 text-xs font-sans">Pendaftaran Pending</div>
                    <div class="text-base font-display font-bold text-white">{{ $stats['pending_daftar'] }}</div>
                </div>
            @endif
            @if($stats['pending_raport'] > 0)
                <div class="w-px bg-white/20"></div>
                <div>
                    <div class="text-white/60 text-xs font-sans">Raport Menunggu Approval</div>
                    <div class="text-base font-display font-bold text-white">{{ $stats['pending_raport'] }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide mb-3">Menu Cepat</p>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        @php
        $menus = [
            ['label' => 'Keuangan',       'desc' => 'SPP & tagihan',           'icon' => 'banknote',  'route' => 'admin.keuangan.index',    'color' => 'bg-[#E8F5EA] text-ich-green'],
            ['label' => 'Siswa',          'desc' => 'Data siswa',              'icon' => 'user',      'route' => 'admin.siswa.index',        'color' => 'bg-[#F0F4FF] text-ich-teal'],
            ['label' => 'Raport',         'desc' => 'Penilaian siswa',         'icon' => 'book',      'route' => 'admin.raport.index',       'color' => 'bg-[#EDE9FE] text-[#8B5CF6]'],
            ['label' => 'Absensi Siswa',  'desc' => 'Kehadiran siswa',         'icon' => 'calendar',  'route' => 'admin.absensi.index',      'color' => 'bg-[#FEF5DC] text-[#E09F17]'],
            ['label' => 'Absensi Guru',   'desc' => 'Kehadiran guru',          'icon' => 'user_check','route' => 'admin.absensi-guru.index', 'color' => 'bg-[#FCE7F3] text-[#EC4899]'],
            ['label' => 'Pendaftaran',    'desc' => 'PPDB online',             'icon' => 'clipboard', 'route' => 'admin.pendaftaran.index',  'color' => 'bg-[#F3F4F6] text-ich-ink-500'],
            ['label' => 'Laporan',        'desc' => 'Ringkasan & export',      'icon' => 'document',  'route' => 'admin.laporan.index',      'color' => 'bg-[#DBEAFE] text-[#3B82F6]'],
            ['label' => 'Pengaturan',     'desc' => 'Konfigurasi sistem',      'icon' => 'settings',  'route' => 'admin.pengaturan.index',   'color' => 'bg-[#F3F4F6] text-ich-ink-500'],
        ];
        @endphp

        @foreach($menus as $menu)
            <a href="{{ route($menu['route']) }}"
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

    {{-- Recent SPP Payments --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="px-6 py-4 border-b border-ich-line flex items-center justify-between">
            <div>
                <h2 class="font-ui font-bold text-ich-ink-900">Pembayaran SPP Terbaru</h2>
                <p class="text-xs text-ich-ink-400 mt-0.5">5 transaksi lunas terakhir</p>
            </div>
            <a href="{{ route('admin.keuangan.index') }}?status=paid"
               class="text-xs font-ui font-bold text-ich-teal hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F6FA]">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Periode</th>
                        <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($recentPayments as $inv)
                        <tr class="hover:bg-[#F5F6FA]">
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                {{ $inv->student?->nama_siswa ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-[#E8F5EA] text-ich-green font-ui font-bold text-xs rounded-full">
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
