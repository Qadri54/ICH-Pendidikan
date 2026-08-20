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

    {{-- Ringkasan Eksekutif --}}
    @if($executive)
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-ich-purple to-purple-600 flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h2 class="font-ui font-bold text-sm text-ich-ink-900">Ringkasan Eksekutif</h2>
                    <p class="text-xs text-ich-ink-400">{{ $executive['period_label'] }}</p>
                </div>
            </div>
            <a href="{{ route('admin.laporan.export.ringkasan-eksekutif-pdf') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-ich-purple-soft text-ich-purple text-xs font-ui font-bold rounded-lg hover:bg-purple-100 transition-colors no-loading">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export PDF
            </a>
        </div>

        {{-- Top metric cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
            {{-- Collection Rate --}}
            @php
                $crColor = $executive['collection_rate'] >= 80 ? 'green' : ($executive['collection_rate'] >= 50 ? 'yellow' : 'red');
                $crBorder = ['green' => 'border-l-ich-success', 'yellow' => 'border-l-ich-warning', 'red' => 'border-l-ich-error'][$crColor];
                $crBg = ['green' => 'bg-gradient-to-r from-green-50 to-white', 'yellow' => 'bg-gradient-to-r from-yellow-50 to-white', 'red' => 'bg-gradient-to-r from-red-50 to-white'][$crColor];
                $crText = ['green' => 'text-ich-success', 'yellow' => 'text-ich-warning', 'red' => 'text-ich-error'][$crColor];
                $crBar = ['green' => 'bg-ich-success', 'yellow' => 'bg-ich-warning', 'red' => 'bg-ich-error'][$crColor];
            @endphp
            <div class="rounded-xl border border-ich-line border-l-4 {{ $crBorder }} {{ $crBg }} p-4 shadow-ich-card hover:-translate-y-0.5 transition-all">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-ui font-bold text-ich-ink-400 uppercase tracking-wider">Tingkat Pembayaran SPP</span>
                    <span class="text-[9px] text-ich-ink-300 font-sans normal-case tracking-normal">% tagihan yang terbayar</span>
                </div>
                <div class="text-3xl font-display font-bold {{ $crText }} leading-tight">{{ $executive['collection_rate'] }}%</div>
                <div class="w-full bg-white/80 rounded-full h-2 mt-2.5 shadow-inner">
                    <div class="h-2 rounded-full {{ $crBar }} transition-all" style="width: {{ min($executive['collection_rate'], 100) }}%"></div>
                </div>
                <p class="text-[11px] text-ich-ink-500 mt-2">Rp {{ number_format($executive['revenue'], 0, ',', '.') }} terkumpul</p>
            </div>

            {{-- Revenue vs Previous --}}
            <div class="rounded-xl border border-ich-line border-l-4 border-l-ich-green bg-gradient-to-r from-green-50 to-white p-4 shadow-ich-card hover:-translate-y-0.5 transition-all">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-ui font-bold text-ich-ink-400 uppercase tracking-wider">Pendapatan Semester</span>
                    @if($executive['revenue_delta'] !== null)
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-ui font-bold
                            {{ $executive['revenue_delta'] >= 0 ? 'bg-green-100 text-ich-success' : 'bg-red-100 text-ich-error' }}">
                            @if($executive['revenue_delta'] >= 0)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            @else
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            @endif
                            {{ abs($executive['revenue_delta']) }}%
                        </span>
                    @endif
                </div>
                <div class="text-xl sm:text-2xl lg:text-3xl font-display font-bold text-ich-ink-900 leading-tight truncate" title="Rp {{ number_format($executive['revenue'], 0, ',', '.') }}">
                    Rp {{ number_format($executive['revenue'], 0, ',', '.') }}
                </div>
                @if($executive['prev_revenue'] > 0)
                    <p class="text-[11px] text-ich-ink-500 mt-2">Smt lalu: Rp {{ number_format($executive['prev_revenue'], 0, ',', '.') }}</p>
                @else
                    <p class="text-[11px] text-ich-ink-300 mt-2">Belum ada data pembanding</p>
                @endif
            </div>

            {{-- Revenue per Siswa --}}
            <div class="rounded-xl border border-ich-line border-l-4 border-l-ich-teal bg-gradient-to-r from-teal-50 to-white p-4 shadow-ich-card hover:-translate-y-0.5 transition-all">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-ui font-bold text-ich-ink-400 uppercase tracking-wider" title="Rata-rata pendapatan yang dihasilkan per siswa">Pendapatan / Siswa</span>
                    <div class="w-7 h-7 rounded-lg bg-white shadow-sm flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-ich-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>
                <div class="text-xl sm:text-2xl lg:text-3xl font-display font-bold text-ich-ink-900 leading-tight truncate" title="Rp {{ number_format($executive['siswa_aktif'] > 0 ? round($executive['revenue'] / $executive['siswa_aktif']) : 0, 0, ',', '.') }}">
                    Rp {{ number_format($executive['siswa_aktif'] > 0 ? round($executive['revenue'] / $executive['siswa_aktif']) : 0, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-ich-ink-500 mt-2">{{ $executive['siswa_aktif'] }} siswa aktif</p>
            </div>
        </div>

        {{-- Bottom metric row --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            {{-- Churn Rate --}}
            @php
                $chColor = $executive['churn_rate'] > 5 ? 'text-ich-error' : ($executive['churn_rate'] > 0 ? 'text-ich-warning' : 'text-ich-success');
                $chBg = $executive['churn_rate'] > 5 ? 'bg-ich-error-soft' : ($executive['churn_rate'] > 0 ? 'bg-ich-warning-soft' : 'bg-ich-success-soft');
            @endphp
            <div class="bg-white rounded-xl shadow-ich-card p-4 flex items-center gap-3 hover:-translate-y-0.5 transition-all">
                <div class="w-11 h-11 rounded-xl {{ $chBg }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $chColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>
                </div>
                <div>
                    <div class="text-[10px] font-ui font-bold text-ich-ink-400 uppercase tracking-wider" title="Persentase siswa yang keluar dari sekolah">Tingkat Kehilangan Siswa</div>
                    <div class="text-xl font-display font-bold {{ $chColor }}">{{ $executive['churn_rate'] }}%</div>
                    <div class="text-[10px] text-ich-ink-400">{{ $executive['siswa_keluar'] }} siswa keluar</div>
                </div>
            </div>

            {{-- Konversi PPDB --}}
            @php
                $cvColor = $executive['conversion_rate'] >= 70 ? 'text-ich-success' : ($executive['conversion_rate'] >= 40 ? 'text-ich-warning' : 'text-ich-error');
                $cvBg = $executive['conversion_rate'] >= 70 ? 'bg-ich-success-soft' : ($executive['conversion_rate'] >= 40 ? 'bg-ich-warning-soft' : 'bg-ich-error-soft');
            @endphp
            <div class="bg-white rounded-xl shadow-ich-card p-4 flex items-center gap-3 hover:-translate-y-0.5 transition-all">
                <div class="w-11 h-11 rounded-xl {{ $cvBg }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $cvColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div>
                    <div class="text-[10px] font-ui font-bold text-ich-ink-400 uppercase tracking-wider">Konversi PPDB</div>
                    <div class="text-xl font-display font-bold {{ $cvColor }}">{{ $executive['conversion_rate'] }}%</div>
                    <div class="text-[10px] text-ich-ink-400">{{ $executive['total_accepted'] }}/{{ $executive['total_registrations'] }} diterima</div>
                </div>
            </div>

            {{-- Rasio Guru:Siswa --}}
            @php
                $rsColor = $executive['rasio'] > 15 ? 'text-ich-warning' : 'text-ich-success';
                $rsBg = $executive['rasio'] > 15 ? 'bg-ich-warning-soft' : 'bg-ich-blue-soft';
            @endphp
            <div class="bg-white rounded-xl shadow-ich-card p-4 flex items-center gap-3 hover:-translate-y-0.5 transition-all">
                <div class="w-11 h-11 rounded-xl {{ $rsBg }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $executive['rasio'] > 15 ? 'text-ich-warning' : 'text-ich-teal' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <div class="text-[10px] font-ui font-bold text-ich-ink-400 uppercase tracking-wider">Rasio Guru:Siswa</div>
                    <div class="text-xl font-display font-bold {{ $rsColor }}">1:{{ $executive['rasio'] }}</div>
                    <div class="text-[10px] text-ich-ink-400">{{ $executive['guru_aktif'] }} guru, {{ $executive['siswa_aktif'] }} siswa</div>
                </div>
            </div>

            {{-- Total Tunggakan --}}
            <div class="bg-white rounded-xl shadow-ich-card p-4 flex items-center gap-3 hover:-translate-y-0.5 transition-all">
                <div class="w-11 h-11 rounded-xl {{ $executive['total_outstanding'] > 0 ? 'bg-ich-error-soft' : 'bg-ich-success-soft' }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $executive['total_outstanding'] > 0 ? 'text-ich-error' : 'text-ich-success' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-[10px] font-ui font-bold text-ich-ink-400 uppercase tracking-wider">Total Tunggakan</div>
                    <div class="text-lg sm:text-xl font-display font-bold truncate {{ $executive['total_outstanding'] > 0 ? 'text-ich-error' : 'text-ich-success' }}" title="Rp {{ number_format($executive['total_outstanding'], 0, ',', '.') }}">
                        Rp {{ number_format($executive['total_outstanding'], 0, ',', '.') }}
                    </div>
                    <div class="text-[10px] text-ich-ink-400">SPP belum terbayar</div>
                </div>
            </div>
        </div>

        {{-- Aging Analysis --}}
        @if($executive['total_outstanding'] > 0)
            <div class="bg-white rounded-xl shadow-ich-card p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-ich-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-ui font-bold text-ich-ink-600">Umur Tunggakan SPP</span>
                    </div>
                    <span class="px-2.5 py-1 bg-ich-error-soft text-ich-error text-xs font-ui font-bold rounded-full">
                        Rp {{ number_format($executive['total_outstanding'], 0, ',', '.') }}
                    </span>
                </div>
                <div class="grid grid-cols-4 gap-2">
                    @php $agingColors = ['bg-ich-success-soft border-ich-success', 'bg-ich-warning-soft border-ich-warning', 'bg-orange-50 border-orange-400', 'bg-ich-error-soft border-ich-error']; @endphp
                    @php $agingTextColors = ['text-ich-success', 'text-ich-warning', 'text-orange-500', 'text-ich-error']; @endphp
                    @foreach($executive['aging'] as $i => $a)
                        <div class="rounded-lg {{ $agingColors[$i] }} border p-3 text-center">
                            <div class="text-xl font-display font-bold {{ $agingTextColors[$i] }}">{{ $a['count'] }}</div>
                            <div class="text-[10px] font-ui font-bold text-ich-ink-500 mt-0.5">{{ $a['label'] }}</div>
                            <div class="text-[10px] font-ui font-semibold {{ $agingTextColors[$i] }} mt-1">
                                Rp {{ number_format($a['amount'], 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- Stacked bar --}}
                <div class="flex rounded-full h-3 mt-4 overflow-hidden shadow-inner bg-gray-100">
                    @php $barColors = ['bg-ich-success', 'bg-ich-warning', 'bg-orange-400', 'bg-ich-error']; @endphp
                    @foreach($executive['aging'] as $i => $a)
                        @php $pct = $executive['total_outstanding'] > 0 ? round($a['amount'] / $executive['total_outstanding'] * 100) : 0; @endphp
                        @if($pct > 0)
                            <div class="{{ $barColors[$i] }}" style="width: {{ $pct }}%"></div>
                        @endif
                    @endforeach
                </div>
                <div class="flex justify-between mt-1.5 px-1">
                    <span class="text-[10px] text-ich-ink-300 font-ui">Tunggakan baru</span>
                    <span class="text-[10px] text-ich-ink-300 font-ui">Tunggakan lama</span>
                </div>
            </div>
        @endif
    </div>
    @endif

    {{-- Export Laporan --}}
    @php
        $periodsData = $periods->map(function($p) {
            $months = [];
            $d = $p->tanggal_mulai->copy()->startOfMonth();
            $end = $p->tanggal_selesai->copy()->startOfMonth();
            while ($d->lte($end)) {
                $months[] = ['m' => $d->month, 'y' => $d->year, 'label' => $d->translatedFormat('F')];
                $d->addMonth();
            }
            return ['id' => $p->period_id, 'label' => $p->tahun_ajaran . ' - Semester ' . $p->semester, 'year' => $p->tanggal_mulai->year, 'months' => $months];
        });
        $activePeriodId = $periods->firstWhere('is_active', true)?->period_id ?? $periods->first()?->period_id;
    @endphp
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden mb-6 no-loading"
         x-data="exportLaporan()">
        <div class="px-6 py-4 border-b border-ich-line flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h2 class="font-ui font-bold text-ich-ink-900">Export Laporan</h2>
                <p class="text-xs text-ich-ink-400 mt-0.5">Download laporan dalam format PDF atau Excel</p>
            </div>
        </div>

        <div class="p-6">
            {{-- Periode selector --}}
            <div class="mb-5 pb-5 border-b border-ich-line">
                <label class="block text-xs font-ui font-bold text-ich-ink-500 mb-1.5">Periode / Semester</label>
                <select x-model="periodId"
                        class="h-10 px-3 bg-[#F9FAFB] border-2 border-ich-line rounded-lg font-sans text-sm focus:outline-none focus:border-ich-teal transition-colors w-full max-w-xs">
                    <template x-for="p in periods" :key="p.id">
                        <option :value="p.id" x-text="p.label" :selected="p.id == periodId"></option>
                    </template>
                </select>
                <p class="text-xs text-ich-ink-400 mt-1.5" x-show="period">
                    Periode: <span class="font-semibold" x-text="period ? (period.months[0]?.label + ' ' + period.months[0]?.y + ' — ' + period.months[period.months.length - 1]?.label + ' ' + period.months[period.months.length - 1]?.y) : ''"></span>
                </p>
            </div>

            {{-- Tab buttons --}}
            <div class="flex flex-wrap gap-2 mb-5">
                <template x-for="t in tabs" :key="t.key">
                    <button @click="tab = t.key" :class="tab === t.key ? 'bg-ich-green text-white shadow-sm' : 'bg-ich-surface text-ich-ink-500 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg text-xs font-ui font-bold transition-all" x-text="t.label"></button>
                </template>
            </div>

            {{-- Ringkasan Eksekutif --}}
            <div x-show="tab === 'eksekutif'" x-cloak>
                <p class="text-sm text-ich-ink-400 font-sans mb-2">Laporan evaluasi bisnis: metrik kinerja, tingkat pembayaran, umur tunggakan, analisis PPDB, dan perbandingan antar semester.</p>
                <p class="text-xs text-ich-ink-300 font-sans mb-4">Cocok untuk rapat yayasan dan evaluasi semester.</p>
                <div class="flex gap-3 no-loading">
                    <a :href="'{{ route('admin.laporan.export.ringkasan-eksekutif-pdf') }}?period_id=' + periodId"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>

            {{-- Keuangan --}}
            <div x-show="tab === 'keuangan'" x-cloak>
                <p class="text-sm text-ich-ink-400 font-sans mb-4">Download laporan keuangan SPP lengkap dengan ringkasan bulanan dan grafik.</p>
                <div class="flex gap-3 no-loading">
                    <a :href="'{{ route('admin.laporan.export.keuangan-pdf') }}?year=' + (period ? period.year : {{ now()->year }})"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                    <a :href="'{{ route('admin.laporan.export.keuangan-excel') }}?year=' + (period ? period.year : {{ now()->year }})"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Excel
                    </a>
                </div>
            </div>

            {{-- Absensi Siswa --}}
            <div x-show="tab === 'absensi-siswa'" x-cloak>
                <form id="formAbsensiSiswa" class="flex flex-wrap items-end gap-3 mb-4">
                    <div>
                        <label class="block text-xs font-ui font-bold text-ich-ink-500 mb-1">Kelas</label>
                        <select name="class_id" required
                                class="h-10 px-3 bg-[#F9FAFB] border-2 border-ich-line rounded-lg font-sans text-sm focus:outline-none focus:border-ich-teal transition-colors">
                            <option value="">Pilih Kelas</option>
                            @foreach($classes as $kelas)
                                <option value="{{ $kelas->class_id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-ui font-bold text-ich-ink-500 mb-1">Bulan</label>
                        <select name="month" x-model="selectedMonth" required
                                class="h-10 px-3 bg-[#F9FAFB] border-2 border-ich-line rounded-lg font-sans text-sm focus:outline-none focus:border-ich-teal transition-colors">
                            <template x-for="mo in months" :key="mo.m">
                                <option :value="mo.m" x-text="mo.label + ' ' + mo.y"></option>
                            </template>
                        </select>
                    </div>
                    <input type="hidden" name="year" :value="monthData.y">
                </form>
                <div class="flex gap-3">
                    <button onclick="exportAbsensiSiswa('pdf')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </button>
                    <button onclick="exportAbsensiSiswa('excel')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Excel
                    </button>
                </div>
            </div>

            {{-- Absensi Guru --}}
            <div x-show="tab === 'absensi-guru'" x-cloak>
                <form id="formAbsensiGuru" class="flex flex-wrap items-end gap-3 mb-4">
                    <div>
                        <label class="block text-xs font-ui font-bold text-ich-ink-500 mb-1">Bulan</label>
                        <select name="month" x-model="selectedMonth" required
                                class="h-10 px-3 bg-[#F9FAFB] border-2 border-ich-line rounded-lg font-sans text-sm focus:outline-none focus:border-ich-teal transition-colors">
                            <template x-for="mo in months" :key="mo.m">
                                <option :value="mo.m" x-text="mo.label + ' ' + mo.y"></option>
                            </template>
                        </select>
                    </div>
                    <input type="hidden" name="year" :value="monthData.y">
                </form>
                <div class="flex gap-3">
                    <button onclick="exportAbsensiGuru('pdf')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </button>
                    <button onclick="exportAbsensiGuru('excel')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Excel
                    </button>
                </div>
            </div>

            {{-- Data Siswa --}}
            <div x-show="tab === 'data-siswa'" x-cloak>
                <p class="text-sm text-ich-ink-400 font-sans mb-4">Laporan data siswa aktif, alumni, dan keluar beserta grafik pertumbuhan.</p>
                <div class="flex gap-3 no-loading">
                    <a href="{{ route('admin.laporan.export.data-siswa-pdf') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>

            {{-- Data Guru --}}
            <div x-show="tab === 'data-guru'" x-cloak>
                <p class="text-sm text-ich-ink-400 font-sans mb-4">Laporan data guru aktif dan nonaktif.</p>
                <div class="flex gap-3 no-loading">
                    <a href="{{ route('admin.laporan.export.data-guru-pdf') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>

            {{-- Data Orang Tua --}}
            <div x-show="tab === 'data-ortu'" x-cloak>
                <p class="text-sm text-ich-ink-400 font-sans mb-4">Laporan data orang tua aktif dan status pembayaran SPP.</p>
                <div class="flex gap-3 no-loading">
                    <a href="{{ route('admin.laporan.export.data-orang-tua-pdf') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>

            {{-- SPP --}}
            <div x-show="tab === 'spp'" x-cloak>
                <p class="text-sm text-ich-ink-400 font-sans mb-4">Laporan SPP semester: total terbayar, tunggakan, dan daftar belum bayar per kelas.</p>
                <div class="flex gap-3 no-loading">
                    <a :href="'{{ route('admin.laporan.export.spp-pdf') }}?period_id=' + periodId" class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>

            {{-- Pendaftaran --}}
            <div x-show="tab === 'pendaftaran'" x-cloak>
                <p class="text-sm text-ich-ink-400 font-sans mb-4">Laporan pendaftaran semester: total daftar, via aplikasi vs admin, pendapatan, dan cicilan.</p>
                <div class="flex gap-3 no-loading">
                    <a :href="'{{ route('admin.laporan.export.pendaftaran-pdf') }}?period_id=' + periodId" class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>

            {{-- Kelas --}}
            <div x-show="tab === 'kelas'" x-cloak>
                <p class="text-sm text-ich-ink-400 font-sans mb-4">Laporan data kelas: jumlah siswa aktif dan wali kelas.</p>
                <div class="flex gap-3 no-loading">
                    <a href="{{ route('admin.laporan.export.kelas-pdf') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>

            {{-- Tabungan --}}
            <div x-show="tab === 'tabungan'" x-cloak>
                <p class="text-sm text-ich-ink-400 font-sans mb-4">Laporan tabungan: total per kelas dan top 10 siswa.</p>
                <div class="flex gap-3 no-loading">
                    <a href="{{ route('admin.laporan.export.tabungan-pdf') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>

            {{-- Raport --}}
            <div x-show="tab === 'raport'" x-cloak>
                <p class="text-sm text-ich-ink-400 font-sans mb-2">Rekap status raport per periode: draft, menunggu persetujuan, disetujui, dan daftar siswa yang belum dibuatkan raport.</p>
                <p class="text-xs text-ich-ink-300 font-sans mb-4">Menggunakan periode yang dipilih di atas.</p>
                <div class="flex gap-3 no-loading">
                    <a :href="'{{ route('admin.laporan.export.raport-pdf') }}?period_id=' + periodId"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
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
                ['label' => 'Pmbyr Daftar',   'desc' => 'Biaya pendaftaran',       'icon' => 'card',      'route' => 'admin.pembayaran-pendaftaran.index', 'color' => 'bg-orange-50 text-orange-500'],
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

    {{-- Pendapatan Bulanan --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-ich-line flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-ich-green-surface flex items-center justify-center">
                    <svg class="w-4 h-4 text-ich-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h2 class="font-ui font-bold text-ich-ink-900">Pendapatan Bulanan {{ $currentYear }}</h2>
                    <p class="text-xs text-ich-ink-400 mt-0.5">SPP & Pendaftaran per bulan</p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-xs font-ui">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-ich-green inline-block"></span>SPP</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-ich-teal inline-block"></span>Pendaftaran</span>
            </div>
        </div>
        <div class="p-6">
            <div class="relative h-56">
                <canvas id="dashboardRevenueChart"></canvas>
            </div>
        </div>
        <div class="overflow-x-auto border-t border-ich-line">
            <table class="w-full text-xs">
                <thead class="bg-ich-surface">
                    <tr>
                        <th class="px-4 py-3.5 text-left font-ui font-bold text-ich-ink-600">Bulan</th>
                        <th class="px-4 py-3.5 text-right font-ui font-bold text-ich-green">SPP</th>
                        <th class="px-4 py-3.5 text-right font-ui font-bold text-ich-teal">Pendaftaran</th>
                        <th class="px-4 py-3.5 text-right font-ui font-bold text-ich-ink-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @php $grandSpp = 0; $grandReg = 0; @endphp
                    @foreach($monthlyIncome as $row)
                        @php $grandSpp += $row['spp']; $grandReg += $row['pendaftaran']; @endphp
                        <tr class="hover:bg-ich-surface transition-colors">
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-700">{{ $row['label'] }}</td>
                            <td class="px-4 py-3 text-right text-ich-green">Rp {{ number_format($row['spp'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-ich-teal">Rp {{ number_format($row['pendaftaran'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-ui font-bold text-ich-ink-900">Rp {{ number_format($row['spp'] + $row['pendaftaran'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-ich-surface font-ui font-bold text-xs">
                        <td class="px-4 py-3 text-ich-ink-700">Total</td>
                        <td class="px-4 py-3 text-right text-ich-green">Rp {{ number_format($grandSpp, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-ich-teal">Rp {{ number_format($grandReg, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-ich-ink-900">Rp {{ number_format($grandSpp + $grandReg, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
    function exportLaporan() {
        return {
            tab: 'eksekutif',
            periodId: @json($activePeriodId),
            periods: @json($periodsData),
            tabs: [
                { key: 'eksekutif', label: 'Ringkasan Eksekutif' },
                { key: 'keuangan', label: 'Keuangan' },
                { key: 'absensi-siswa', label: 'Absensi Siswa' },
                { key: 'absensi-guru', label: 'Absensi Guru' },
                { key: 'data-siswa', label: 'Data Siswa' },
                { key: 'data-guru', label: 'Data Guru' },
                { key: 'data-ortu', label: 'Data Ortu' },
                { key: 'spp', label: 'SPP' },
                { key: 'pendaftaran', label: 'Pendaftaran' },
                { key: 'kelas', label: 'Kelas' },
                { key: 'tabungan', label: 'Tabungan' },
                { key: 'raport', label: 'Raport' },
            ],
            selectedMonth: null,
            get period() { return this.periods.find(p => p.id == this.periodId) },
            get months() { return this.period ? this.period.months : [] },
            get monthData() {
                return this.months.find(m => m.m == this.selectedMonth) || this.months[0] || { m: 1, y: new Date().getFullYear() };
            },
            init() {
                this.resetMonth();
                this.$watch('periodId', () => this.resetMonth());
            },
            resetMonth() {
                const cm = new Date().getMonth() + 1;
                this.selectedMonth = this.months.find(m => m.m == cm) ? cm : (this.months[0]?.m ?? 1);
            }
        }
    }

    function exportAbsensiSiswa(format) {
        const form = document.getElementById('formAbsensiSiswa');
        const classId = form.querySelector('[name=class_id]').value;
        const month = form.querySelector('[name=month]').value;
        const year = form.querySelector('[name=year]').value;
        if (!classId) { alert('Pilih kelas terlebih dahulu'); return; }
        const url = format === 'pdf'
            ? '{{ route("admin.laporan.export.absensi-siswa-pdf") }}'
            : '{{ route("admin.laporan.export.absensi-siswa-excel") }}';
        window.location.href = url + '?class_id=' + classId + '&year=' + year + '&month=' + month;
    }

    function exportAbsensiGuru(format) {
        const form = document.getElementById('formAbsensiGuru');
        const month = form.querySelector('[name=month]').value;
        const year = form.querySelector('[name=year]').value;
        const url = format === 'pdf'
            ? '{{ route("admin.laporan.export.absensi-guru-pdf") }}'
            : '{{ route("admin.laporan.export.absensi-guru-excel") }}';
        window.location.href = url + '?year=' + year + '&month=' + month;
    }

    (function () {
        const labels = @json($monthlyIncome->pluck('label'));
        const sppData = @json($monthlyIncome->pluck('spp'));
        const regData = @json($monthlyIncome->pluck('pendaftaran'));

        new Chart(document.getElementById('dashboardRevenueChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'SPP',
                        data: sppData,
                        backgroundColor: '#3DA746',
                        borderRadius: 4,
                    },
                    {
                        label: 'Pendaftaran',
                        data: regData,
                        backgroundColor: '#2A8A94',
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' },
                        ticks: {
                            font: { size: 11 },
                            callback: v => 'Rp ' + (v >= 1000000
                                ? (v / 1000000).toFixed(1) + 'jt'
                                : (v / 1000).toFixed(0) + 'rb'),
                        },
                    },
                },
            },
        });
    })();
    </script>
    @endpush

</x-main-layout>
