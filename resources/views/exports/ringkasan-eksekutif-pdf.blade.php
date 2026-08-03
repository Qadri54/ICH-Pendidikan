<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ringkasan Eksekutif</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Ringkasan Eksekutif'])
    <style>
        h2 { font-size: 14px; color: #3DA746; margin: 0 0 4px 0; }
        h3 { font-size: 12px; margin: 18px 0 6px 0; color: #333; border-bottom: 2px solid #3DA746; padding-bottom: 4px; }
        .subtitle { font-size: 10px; color: #666; margin: 0 0 14px 0; }
        .metric-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .metric-table td { width: 25%; padding: 8px 10px; vertical-align: top; border: 1px solid #e5e7eb; }
        .metric-label { font-size: 8px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 2px 0; }
        .metric-value { font-size: 16px; font-weight: bold; color: #1a1a1a; margin: 0; }
        .metric-value-green { font-size: 16px; font-weight: bold; color: #3DA746; margin: 0; }
        .metric-note { font-size: 8px; color: #999; margin: 2px 0 0 0; }
        .delta-up { color: #16a34a; font-size: 9px; font-weight: bold; }
        .delta-down { color: #dc2626; font-size: 9px; font-weight: bold; }
        .delta-neutral { color: #888; font-size: 9px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th { background: #f3f4f6; text-align: left; padding: 5px 8px; border: 1px solid #ddd; font-size: 9px; font-weight: bold; }
        table.data td { padding: 4px 8px; border: 1px solid #ddd; font-size: 9px; }
        table.data tr:nth-child(even) { background: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bar-row { margin-bottom: 3px; }
        .bar-label { display: inline-block; width: 55px; font-size: 8px; text-align: right; padding-right: 6px; vertical-align: middle; }
        .bar-track { display: inline-block; width: 280px; height: 14px; background: #f0f0f0; vertical-align: middle; }
        .bar-fill-green { display: inline-block; height: 14px; background: #3DA746; min-width: 1px; }
        .bar-fill-blue { display: inline-block; height: 14px; background: #3b82f6; min-width: 0; }
        .bar-value { display: inline-block; font-size: 8px; padding-left: 4px; vertical-align: middle; color: #555; }
        .highlight-box { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 8px 12px; margin: 8px 0; }
        .warning-box { background: #fefce8; border: 1px solid #fef08a; padding: 8px 12px; margin: 8px 0; }
        .danger-box { background: #fef2f2; border: 1px solid #fecaca; padding: 8px 12px; margin: 8px 0; }
        .box-title { font-size: 10px; font-weight: bold; margin: 0 0 2px 0; }
        .box-text { font-size: 9px; color: #555; margin: 0; }
        .page-break { page-break-before: always; }
        .funnel-table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        .funnel-table td { padding: 6px 10px; font-size: 10px; }
        .funnel-bar { height: 20px; background: #3DA746; display: inline-block; vertical-align: middle; }
        .funnel-bar-pending { background: #f59e0b; }
        .funnel-bar-rejected { background: #ef4444; }
        .aging-critical { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <h2>RINGKASAN EKSEKUTIF</h2>
    <p class="subtitle">Periode: {{ $periodLabel }} &mdash; Dicetak: {{ now()->translatedFormat('d F Y') }}</p>

    {{-- ════════ METRIK UTAMA ════════ --}}
    <h3>Indikator Kinerja Utama</h3>
    <table class="metric-table">
        <tr>
            <td>
                <p class="metric-label">Siswa Aktif</p>
                <p class="metric-value-green">{{ $totalSiswaAktif }}</p>
                <p class="metric-note">Baru semester ini: {{ $siswaBaru }}
                    @if($prevPeriodLabel)
                        @if($siswaBaru > $prevSiswaBaru)
                            <span class="delta-up">+{{ $siswaBaru - $prevSiswaBaru }} vs smt lalu</span>
                        @elseif($siswaBaru < $prevSiswaBaru)
                            <span class="delta-down">{{ $siswaBaru - $prevSiswaBaru }} vs smt lalu</span>
                        @else
                            <span class="delta-neutral">= smt lalu</span>
                        @endif
                    @endif
                </p>
            </td>
            <td>
                <p class="metric-label">Guru Aktif</p>
                <p class="metric-value">{{ $totalGuruAktif }}</p>
                <p class="metric-note">Rasio guru:siswa = 1:{{ $rasioGuruSiswa }}</p>
            </td>
            <td>
                <p class="metric-label">Churn Rate</p>
                <p class="metric-value" style="color: {{ $churnRate > 5 ? '#dc2626' : ($churnRate > 0 ? '#f59e0b' : '#16a34a') }};">{{ $churnRate }}%</p>
                <p class="metric-note">{{ $siswaKeluar }} siswa keluar semester ini</p>
            </td>
            <td>
                <p class="metric-label">Konversi PPDB</p>
                <p class="metric-value" style="color: {{ $conversionRate >= 70 ? '#16a34a' : ($conversionRate >= 40 ? '#f59e0b' : '#dc2626') }};">{{ $conversionRate }}%</p>
                <p class="metric-note">{{ $totalAccepted }}/{{ $totalRegistrations }} diterima</p>
            </td>
        </tr>
        <tr>
            <td>
                <p class="metric-label">Total Pendapatan</p>
                <p class="metric-value-green">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <p class="metric-note">
                    @if($revenueDelta !== null)
                        @if($revenueDelta > 0)
                            <span class="delta-up">+{{ $revenueDelta }}% vs semester lalu</span>
                        @elseif($revenueDelta < 0)
                            <span class="delta-down">{{ $revenueDelta }}% vs semester lalu</span>
                        @else
                            <span class="delta-neutral">Sama dengan semester lalu</span>
                        @endif
                    @else
                        <span class="delta-neutral">Tidak ada data pembanding</span>
                    @endif
                </p>
            </td>
            <td>
                <p class="metric-label">Collection Rate SPP</p>
                <p class="metric-value" style="color: {{ $sppCollectionRate >= 80 ? '#16a34a' : ($sppCollectionRate >= 50 ? '#f59e0b' : '#dc2626') }};">{{ $sppCollectionRate }}%</p>
                <p class="metric-note">Rp {{ number_format($sppCollectedAmount, 0, ',', '.') }} / {{ number_format($sppInvoicedAmount, 0, ',', '.') }}</p>
            </td>
            <td>
                <p class="metric-label">Revenue per Siswa</p>
                <p class="metric-value">Rp {{ number_format($revenuePerSiswa, 0, ',', '.') }}</p>
                <p class="metric-note">Total pendapatan / siswa aktif</p>
            </td>
            <td>
                <p class="metric-label">Total Tunggakan</p>
                <p class="metric-value" style="color: {{ $totalOutstanding > 0 ? '#dc2626' : '#16a34a' }};">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</p>
                <p class="metric-note">SPP belum terbayar (semua periode)</p>
            </td>
        </tr>
    </table>

    {{-- ════════ INSIGHT BOX ════════ --}}
    @if($sppCollectionRate < 70 || $churnRate > 5 || $totalOutstanding > 0)
        @if($sppCollectionRate < 70)
            <div class="warning-box">
                <p class="box-title" style="color: #92400e;">Perhatian: Collection Rate SPP Rendah</p>
                <p class="box-text">Hanya {{ $sppCollectionRate }}% tagihan SPP semester ini yang terbayar. Disarankan untuk meninjau strategi penagihan dan berkomunikasi dengan orang tua yang menunggak.</p>
            </div>
        @endif
        @if($totalOutstanding > 5000000)
            <div class="danger-box">
                <p class="box-title" style="color: #991b1b;">Peringatan: Tunggakan Signifikan</p>
                <p class="box-text">Total tunggakan SPP mencapai Rp {{ number_format($totalOutstanding, 0, ',', '.') }}. Lihat aging analysis di halaman berikutnya untuk prioritas penagihan.</p>
            </div>
        @endif
    @else
        <div class="highlight-box">
            <p class="box-title" style="color: #166534;">Kinerja Baik</p>
            <p class="box-text">Collection rate SPP {{ $sppCollectionRate }}% dan tidak ada masalah churn signifikan. Pertahankan kinerja ini.</p>
        </div>
    @endif

    {{-- ════════ HALAMAN 2: ANALISIS KEUANGAN ════════ --}}
    <div class="page-break"></div>

    <h3>Pendapatan Bulanan — {{ $periodLabel }}</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">SPP (Rp)</th>
                <th class="text-right">Pendaftaran (Rp)</th>
                <th class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $sumSpp = 0; $sumReg = 0; @endphp
            @foreach($monthlyRevenue as $mr)
                @php $sumSpp += $mr['spp']; $sumReg += $mr['reg']; @endphp
                <tr>
                    <td>{{ $mr['label'] }}</td>
                    <td class="text-right">{{ number_format($mr['spp'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($mr['reg'], 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($mr['total'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #e8f5e9; font-weight: bold;">
                <td>TOTAL</td>
                <td class="text-right">{{ number_format($sumSpp, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($sumReg, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($sumSpp + $sumReg, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @if($prevPeriodLabel)
        <div style="margin: 10px 0; padding: 6px 10px; background: #f9fafb; border: 1px solid #e5e7eb;">
            <table style="width: 100%; font-size: 9px; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%;">
                        <span style="color: #888;">Semester ini:</span>
                        <strong style="color: #3DA746;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
                    </td>
                    <td style="width: 50%;">
                        <span style="color: #888;">Semester lalu ({{ $prevPeriodLabel }}):</span>
                        <strong>Rp {{ number_format($prevTotalRevenue, 0, ',', '.') }}</strong>
                        @if($revenueDelta !== null)
                            @if($revenueDelta > 0)
                                <span class="delta-up">(+{{ $revenueDelta }}%)</span>
                            @elseif($revenueDelta < 0)
                                <span class="delta-down">({{ $revenueDelta }}%)</span>
                            @endif
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <h3>Grafik Pendapatan Bulanan</h3>
    <div style="margin-bottom: 6px;">
        <span style="display: inline-block; width: 10px; height: 10px; background: #3DA746; vertical-align: middle;"></span>
        <span style="font-size: 8px; vertical-align: middle; color: #666;"> SPP</span>
        &nbsp;&nbsp;
        <span style="display: inline-block; width: 10px; height: 10px; background: #3b82f6; vertical-align: middle;"></span>
        <span style="font-size: 8px; vertical-align: middle; color: #666;"> Pendaftaran</span>
    </div>
    @foreach($monthlyRevenue as $mr)
        <div class="bar-row">
            <span class="bar-label">{{ $mr['label'] }}</span>
            <span class="bar-track">
                <span class="bar-fill-green" style="width: {{ $maxMonthlyRevenue > 0 ? round($mr['spp'] / $maxMonthlyRevenue * 100) : 0 }}%;"></span><span class="bar-fill-blue" style="width: {{ $maxMonthlyRevenue > 0 ? round($mr['reg'] / $maxMonthlyRevenue * 100) : 0 }}%;"></span>
            </span>
            <span class="bar-value">Rp {{ number_format($mr['total'], 0, ',', '.') }}</span>
        </div>
    @endforeach

    {{-- ════════ AGING ANALYSIS ════════ --}}
    <h3>Aging Analysis Tunggakan SPP</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Umur Tunggakan</th>
                <th class="text-center">Jumlah Tagihan</th>
                <th class="text-right">Nominal (Rp)</th>
                <th class="text-right">% dari Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aging as $a)
                <tr>
                    <td @if($loop->last && $a['count'] > 0) class="aging-critical" @endif>{{ $a['label'] }}</td>
                    <td class="text-center" @if($loop->last && $a['count'] > 0) class="text-center aging-critical" @endif>{{ $a['count'] }}</td>
                    <td class="text-right" @if($loop->last && $a['count'] > 0) style="color: #dc2626; font-weight: bold;" @endif>{{ number_format($a['amount'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ $totalOutstanding > 0 ? round($a['amount'] / $totalOutstanding * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #fef2f2; font-weight: bold;">
                <td>TOTAL TUNGGAKAN</td>
                <td class="text-center">{{ array_sum(array_column($aging, 'count')) }}</td>
                <td class="text-right" style="color: #dc2626;">{{ number_format($totalOutstanding, 0, ',', '.') }}</td>
                <td class="text-right">100%</td>
            </tr>
        </tfoot>
    </table>
    @if($aging[3]['count'] > 0)
        <div class="danger-box">
            <p class="box-title" style="color: #991b1b;">{{ $aging[3]['count'] }} tagihan sudah lebih dari 6 bulan</p>
            <p class="box-text">Tunggakan lama senilai Rp {{ number_format($aging[3]['amount'], 0, ',', '.') }} memerlukan tindakan penagihan segera atau kebijakan penghapusan piutang.</p>
        </div>
    @endif

    {{-- ════════ HALAMAN 3: ANALISIS PPDB & KELAS ════════ --}}
    <div class="page-break"></div>

    <h3>Analisis PPDB — {{ $periodLabel }}</h3>

    {{-- Funnel --}}
    <table class="data" style="margin-bottom: 14px;">
        <thead>
            <tr>
                <th>Tahap</th>
                <th class="text-center">Jumlah</th>
                <th class="text-right">Persentase</th>
                <th style="width: 40%;">Proporsi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold;">Total Mendaftar</td>
                <td class="text-center">{{ $totalRegistrations }}</td>
                <td class="text-right">100%</td>
                <td>
                    <span class="bar-track" style="width: 100%;">
                        <span class="bar-fill-green" style="width: 100%; background: #6b7280;"></span>
                    </span>
                </td>
            </tr>
            <tr>
                <td style="color: #16a34a; font-weight: bold;">Diterima</td>
                <td class="text-center">{{ $totalAccepted }}</td>
                <td class="text-right">{{ $totalRegistrations > 0 ? round($totalAccepted / $totalRegistrations * 100, 1) : 0 }}%</td>
                <td>
                    <span class="bar-track" style="width: 100%;">
                        <span class="bar-fill-green" style="width: {{ $totalRegistrations > 0 ? round($totalAccepted / $totalRegistrations * 100) : 0 }}%;"></span>
                    </span>
                </td>
            </tr>
            <tr>
                <td style="color: #f59e0b; font-weight: bold;">Menunggu</td>
                <td class="text-center">{{ $totalPending }}</td>
                <td class="text-right">{{ $totalRegistrations > 0 ? round($totalPending / $totalRegistrations * 100, 1) : 0 }}%</td>
                <td>
                    <span class="bar-track" style="width: 100%;">
                        <span class="bar-fill-green" style="width: {{ $totalRegistrations > 0 ? round($totalPending / $totalRegistrations * 100) : 0 }}%; background: #f59e0b;"></span>
                    </span>
                </td>
            </tr>
            <tr>
                <td style="color: #dc2626; font-weight: bold;">Ditolak</td>
                <td class="text-center">{{ $totalRejected }}</td>
                <td class="text-right">{{ $totalRegistrations > 0 ? round($totalRejected / $totalRegistrations * 100, 1) : 0 }}%</td>
                <td>
                    <span class="bar-track" style="width: 100%;">
                        <span class="bar-fill-green" style="width: {{ $totalRegistrations > 0 ? round($totalRejected / $totalRegistrations * 100) : 0 }}%; background: #ef4444;"></span>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Sumber Pendaftaran --}}
    <table class="data" style="width: 50%;">
        <thead>
            <tr>
                <th>Sumber Pendaftaran</th>
                <th class="text-center">Jumlah</th>
                <th class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Via Aplikasi (Orang Tua)</td>
                <td class="text-center">{{ $regViaApp }}</td>
                <td class="text-right">{{ $totalRegistrations > 0 ? round($regViaApp / $totalRegistrations * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Via Admin (Langsung)</td>
                <td class="text-center">{{ $regViaAdmin }}</td>
                <td class="text-right">{{ $totalRegistrations > 0 ? round($regViaAdmin / $totalRegistrations * 100, 1) : 0 }}%</td>
            </tr>
        </tbody>
    </table>

    {{-- Kelas Overview --}}
    <h3>Distribusi Siswa per Kelas</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Kelas</th>
                <th>Wali Kelas</th>
                <th class="text-center">Jumlah Siswa</th>
                <th style="width: 35%;">Distribusi</th>
            </tr>
        </thead>
        <tbody>
            @php $maxStudents = $classOverview->max('students_count') ?: 1; @endphp
            @foreach($classOverview as $i => $cls)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td style="font-weight: bold;">{{ $cls->nama_kelas }}</td>
                    <td>{{ $cls->homeroomTeacher?->user?->name ?? '-' }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $cls->students_count }}</td>
                    <td>
                        <span class="bar-track" style="width: 100%;">
                            <span class="bar-fill-green" style="width: {{ round($cls->students_count / $maxStudents * 100) }}%;"></span>
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #e8f5e9; font-weight: bold;">
                <td colspan="3">TOTAL</td>
                <td class="text-center">{{ $classOverview->sum('students_count') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @if($rasioGuruSiswa > 15)
        <div class="warning-box">
            <p class="box-title" style="color: #92400e;">Rasio Guru:Siswa Tinggi (1:{{ $rasioGuruSiswa }})</p>
            <p class="box-text">Standar ideal PAUD adalah 1:10 sampai 1:15. Pertimbangkan untuk menambah tenaga pengajar jika jumlah siswa terus bertambah.</p>
        </div>
    @endif

    @include('exports.partials.pdf-validation')
</body>
</html>
