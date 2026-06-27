<x-main-layout title="Laporan">

    <div class="mb-6">
        <h1 class="text-2xl font-display font-bold text-ich-ink-900">Laporan</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">Ringkasan data & export laporan</p>
    </div>

    {{-- Revenue highlight --}}
    <div class="bg-gradient-to-br from-ich-green to-ich-gradient-end rounded-2xl shadow-ich-card p-6 mb-6 relative overflow-hidden">
        <div class="absolute -bottom-4 -right-4 w-28 h-28 bg-white/5 rounded-full"></div>
        <div class="absolute top-0 right-1/3 w-20 h-20 bg-white/5 rounded-full -translate-y-1/2"></div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-white/70 text-xs font-sans">Total Pendapatan (SPP + Pendaftaran)</span>
            </div>
            <div class="text-3xl font-display font-bold text-white mb-4">
                Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="bg-white/10 rounded-lg p-3">
                    <div class="text-white/60 text-xs font-sans mb-1">SPP Terkumpul</div>
                    <div class="text-base font-display font-bold text-white">
                        Rp {{ number_format($stats['total_spp_terkumpul'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="bg-white/10 rounded-lg p-3">
                    <div class="text-white/60 text-xs font-sans mb-1">Biaya Pendaftaran</div>
                    <div class="text-base font-display font-bold text-white">
                        Rp {{ number_format($stats['total_pendaftaran'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="bg-white/10 rounded-lg p-3">
                    <div class="text-white/60 text-xs font-sans mb-1">Total Tabungan</div>
                    <div class="text-base font-display font-bold text-white">
                        Rp {{ number_format($stats['total_tabungan'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
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
            <div class="w-11 h-11 rounded-xl bg-ich-purple-soft flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-ich-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-ich-ink-400 text-xs font-sans mb-0.5">Pendaftaran Lunas</div>
                <div class="text-2xl font-display font-bold text-ich-purple">{{ $lunasPendaftaran->count() }}</div>
            </div>
        </div>
    </div>

    {{-- Chart: Pendapatan Bulanan --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-ich-line flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-ich-green-surface flex items-center justify-center">
                    <svg class="w-4 h-4 text-ich-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h2 class="font-ui font-bold text-sm text-ich-ink-900">Grafik Pendapatan Bulanan</h2>
                    <p class="text-xs text-ich-ink-400">Tahun {{ $year }}</p>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex items-center gap-2">
                <select name="year" onchange="this.form.submit()"
                        class="h-9 px-3 bg-[#F9FAFB] border-2 border-ich-line rounded-lg font-sans text-xs focus:outline-none focus:border-ich-teal transition-colors">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
        <div class="p-6">
            <div style="height: 320px; position: relative;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        {{-- Monthly summary table --}}
        <div class="px-6 pb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-ich-surface">
                        <tr>
                            <th class="px-3 py-2 text-left font-ui font-bold text-ich-ink-600 text-xs">Bulan</th>
                            <th class="px-3 py-2 text-right font-ui font-bold text-ich-ink-600 text-xs">SPP</th>
                            <th class="px-3 py-2 text-right font-ui font-bold text-ich-ink-600 text-xs">Pendaftaran</th>
                            <th class="px-3 py-2 text-right font-ui font-bold text-ich-ink-600 text-xs">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ich-line">
                        @php $totalSppChart = 0; $totalRegChart = 0; @endphp
                        @foreach($monthlySummary as $ms)
                            @php $totalSppChart += $ms['spp']; $totalRegChart += $ms['pendaftaran']; @endphp
                            <tr class="hover:bg-ich-surface transition-colors">
                                <td class="px-3 py-2 font-ui text-ich-ink-900 text-xs">{{ $ms['label'] }}</td>
                                <td class="px-3 py-2 text-right font-ui text-ich-ink-600 text-xs">Rp {{ number_format($ms['spp'], 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right font-ui text-ich-ink-600 text-xs">Rp {{ number_format($ms['pendaftaran'], 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right font-ui font-semibold text-ich-ink-900 text-xs">Rp {{ number_format($ms['spp'] + $ms['pendaftaran'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-ich-green-surface">
                            <td class="px-3 py-2 font-ui font-bold text-ich-ink-900 text-xs">Total Setahun</td>
                            <td class="px-3 py-2 text-right font-ui font-bold text-ich-green text-xs">Rp {{ number_format($totalSppChart, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right font-ui font-bold text-ich-green text-xs">Rp {{ number_format($totalRegChart, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right font-ui font-bold text-ich-green text-xs">Rp {{ number_format($totalSppChart + $totalRegChart, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Data Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Pelunasan Pendaftaran --}}
        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="px-6 py-4 border-b border-ich-line flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-ich-purple-soft flex items-center justify-center">
                        <svg class="w-4 h-4 text-ich-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <h2 class="font-ui font-bold text-sm text-ich-ink-900">Pelunasan Pendaftaran</h2>
                        <p class="text-xs text-ich-ink-400">{{ $lunasPendaftaran->count() }} siswa lunas</p>
                    </div>
                </div>
                <a href="{{ route('admin.pembayaran-pendaftaran.index') }}?status=approved"
                   class="text-xs font-ui font-bold text-ich-teal hover:underline">
                    Semua &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-ich-surface">
                        <tr>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                            <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ich-line">
                        @forelse($lunasPendaftaran->take(5) as $fee)
                            <tr class="hover:bg-ich-surface transition-colors">
                                <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                    {{ $fee->student?->nama_siswa ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-ich-purple-soft text-ich-purple font-ui font-bold text-xs rounded-full">
                                        {{ $fee->student?->classRoom?->nama_kelas ?? 'Belum' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-ui font-semibold text-ich-success">
                                    Rp {{ number_format($fee->total_jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-ich-ink-300 font-sans text-xs">
                                    Belum ada data pelunasan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pembayaran SPP --}}
        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="px-6 py-4 border-b border-ich-line flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-ich-green-surface flex items-center justify-center">
                        <svg class="w-4 h-4 text-ich-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-ui font-bold text-sm text-ich-ink-900">Pembayaran SPP</h2>
                        <p class="text-xs text-ich-ink-400">{{ $pembayaranSpp->count() }} transaksi lunas</p>
                    </div>
                </div>
                <a href="{{ route('admin.keuangan.index') }}?status=paid"
                   class="text-xs font-ui font-bold text-ich-teal hover:underline">
                    Semua &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-ich-surface">
                        <tr>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Periode</th>
                            <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ich-line">
                        @forelse($pembayaranSpp->take(5) as $inv)
                            <tr class="hover:bg-ich-surface transition-colors">
                                <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                    {{ $inv->student?->nama_siswa ?? '-' }}
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
                                <td colspan="3" class="px-4 py-8 text-center text-ich-ink-300 font-sans text-xs">
                                    Belum ada pembayaran SPP lunas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden no-loading"
         x-data="exportLaporan()">
        <div class="px-6 py-4 border-b border-ich-line flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-ich-blue-soft flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h2 class="font-ui font-bold text-ich-ink-900">Export Laporan</h2>
                <p class="text-xs text-ich-ink-400">Download laporan dalam format PDF atau Excel</p>
            </div>
        </div>

        <div class="p-6">
            {{-- Periode / Semester selector --}}
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
            <div class="flex gap-2 mb-5">
                <button @click="tab = 'keuangan'" :class="tab === 'keuangan' ? 'bg-ich-green text-white shadow-sm' : 'bg-ich-surface text-ich-ink-500 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-xs font-ui font-bold transition-all">Keuangan</button>
                <button @click="tab = 'absensi-siswa'" :class="tab === 'absensi-siswa' ? 'bg-ich-green text-white shadow-sm' : 'bg-ich-surface text-ich-ink-500 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-xs font-ui font-bold transition-all">Absensi Siswa</button>
                <button @click="tab = 'absensi-guru'" :class="tab === 'absensi-guru' ? 'bg-ich-green text-white shadow-sm' : 'bg-ich-surface text-ich-ink-500 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-xs font-ui font-bold transition-all">Absensi Guru</button>
            </div>

            {{-- Keuangan export --}}
            <div x-show="tab === 'keuangan'" x-cloak>
                <p class="text-sm text-ich-ink-400 font-sans mb-4">Download laporan keuangan SPP lengkap dengan ringkasan bulanan dan grafik.</p>
                <div class="flex gap-3 no-loading">
                    <a :href="'{{ route('admin.laporan.export.keuangan-pdf') }}?year=' + (period ? period.year : {{ $year }})"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                    <a :href="'{{ route('admin.laporan.export.keuangan-excel') }}?year=' + (period ? period.year : {{ $year }})"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Excel
                    </a>
                </div>
            </div>

            {{-- Absensi Siswa export --}}
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

            {{-- Absensi Guru export --}}
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
        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        function exportLaporan() {
            return {
                tab: 'keuangan',
                periodId: @json($activePeriodId),
                periods: @json($periodsData),
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

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const monthlyData = @json($monthlySummary);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthlyData.map(d => d.label),
                    datasets: [
                        {
                            label: 'SPP',
                            data: monthlyData.map(d => d.spp),
                            backgroundColor: 'rgba(61, 167, 70, 0.8)',
                            borderColor: 'rgba(61, 167, 70, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: 'Pendaftaran',
                            data: monthlyData.map(d => d.pendaftaran),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'rectRounded',
                                padding: 16,
                                font: { size: 11, family: "'Inter', sans-serif" }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                    return 'Rp ' + value;
                                },
                                font: { size: 10 }
                            },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            ticks: { font: { size: 10 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        });

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
    </script>

</x-main-layout>
