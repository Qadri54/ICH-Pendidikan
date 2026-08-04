<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendaftaran</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Laporan Pendaftaran — ' . $periodLabel])
    <style>
        .summary { margin-bottom: 20px; }
        .summary td { padding: 4px 12px 4px 0; }
        .summary .label { color: #666; font-size: 11px; }
        .summary .value { font-weight: bold; font-size: 13px; }
        .summary .value-highlight { font-weight: bold; font-size: 15px; color: #3DA746; }
        .summary .value-danger { font-weight: bold; font-size: 13px; color: #e53e3e; }
        h3 { font-size: 13px; margin: 20px 0 6px 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th { background: #f0f0f0; text-align: left; padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data tr:nth-child(even) { background: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .kpi-row { margin-bottom: 16px; }
        .kpi-box { display: inline-block; width: 32%; vertical-align: top; padding: 8px 10px; border: 1px solid #e5e7eb; border-radius: 6px; text-align: center; }
        .kpi-box .kpi-value { font-size: 18px; font-weight: bold; color: #3DA746; }
        .kpi-box .kpi-label { font-size: 9px; color: #666; margin-top: 2px; }
        .kpi-box.info .kpi-value { color: #3b82f6; }
        .funnel { margin: 12px 0; }
        .funnel-row { margin-bottom: 4px; }
        .funnel-label { display: inline-block; width: 80px; font-size: 9px; text-align: right; padding-right: 8px; vertical-align: middle; }
        .funnel-track { display: inline-block; height: 18px; vertical-align: middle; }
        .funnel-value { display: inline-block; font-size: 9px; padding-left: 6px; vertical-align: middle; font-weight: bold; }
        .bg-accepted { background: #3DA746; }
        .bg-pending { background: #f59e0b; }
        .bg-rejected { background: #e53e3e; }
    </style>
</head>
<body>
    <h3>Ringkasan Pendaftaran — {{ $periodLabel }}</h3>

    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-value">{{ $totalRegistrations }}</div>
            <div class="kpi-label">Total Pendaftar</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $conversionRate }}%</div>
            <div class="kpi-label">Tingkat Penerimaan</div>
            <div style="font-size: 7px; color: #999; margin-top: 1px;">% pendaftar yang diterima</div>
        </div>
        <div class="kpi-box info">
            <div class="kpi-value">Rp {{ number_format($totalPendapatan / 1000000, 1) }}jt</div>
            <div class="kpi-label">Pendapatan Pendaftaran</div>
        </div>
    </div>

    @if($totalRegistrations > 0)
    <h3>Alur Seleksi Pendaftaran</h3>
    @php
        $maxFunnel = max($totalRegistrations, 1);
        $widthAccepted = round($totalAccepted / $maxFunnel * 350);
        $widthPending = round($totalPending / $maxFunnel * 350);
        $widthRejected = round($totalRejected / $maxFunnel * 350);
    @endphp
    <div class="funnel">
        <div class="funnel-row">
            <span class="funnel-label">Total</span>
            <span class="funnel-track" style="width: 350px; background: #e5e7eb;"></span>
            <span class="funnel-value">{{ $totalRegistrations }}</span>
        </div>
        <div class="funnel-row">
            <span class="funnel-label">Diterima</span>
            <span class="funnel-track bg-accepted" style="width: {{ max($widthAccepted, 2) }}px;"></span>
            <span class="funnel-value" style="color: #3DA746;">{{ $totalAccepted }} ({{ $totalRegistrations > 0 ? round($totalAccepted / $totalRegistrations * 100) : 0 }}%)</span>
        </div>
        <div class="funnel-row">
            <span class="funnel-label">Pending</span>
            <span class="funnel-track bg-pending" style="width: {{ max($widthPending, 2) }}px;"></span>
            <span class="funnel-value" style="color: #f59e0b;">{{ $totalPending }} ({{ $totalRegistrations > 0 ? round($totalPending / $totalRegistrations * 100) : 0 }}%)</span>
        </div>
        <div class="funnel-row">
            <span class="funnel-label">Ditolak</span>
            <span class="funnel-track bg-rejected" style="width: {{ max($widthRejected, 2) }}px;"></span>
            <span class="funnel-value" style="color: #e53e3e;">{{ $totalRejected }} ({{ $totalRegistrations > 0 ? round($totalRejected / $totalRegistrations * 100) : 0 }}%)</span>
        </div>
    </div>
    @endif

    <table class="summary">
        <tr>
            <td class="label">Pendaftaran via Aplikasi</td>
            <td class="value">{{ $totalViaApp }}</td>
        </tr>
        <tr>
            <td class="label">Pendaftaran via Admin (Langsung)</td>
            <td class="value">{{ $totalViaAdmin }}</td>
        </tr>
        <tr>
            <td class="label" style="padding-top: 8px; border-top: 1px solid #ddd;">Total Pendapatan Pendaftaran</td>
            <td class="value-highlight" style="padding-top: 8px; border-top: 1px solid #ddd;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Belum Membayar</td>
            <td class="value-danger">{{ $totalUnpaid }}</td>
        </tr>
        <tr>
            <td class="label">Total Pelunasan Langsung</td>
            <td class="value">{{ $totalLunas }}</td>
        </tr>
        <tr>
            <td class="label">Total Cicilan</td>
            <td class="value">{{ $totalCicilan }}</td>
        </tr>
    </table>

    @if($unpaidFees->count() > 0)
        <h3>Daftar Siswa yang Belum Melunasi Biaya Pendaftaran</h3>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th class="text-right">Total Biaya</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unpaidFees as $i => $fee)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $fee->student?->nama_siswa ?? '-' }}</td>
                        <td>{{ $fee->student?->classRoom?->nama_kelas ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($fee->total_jumlah, 0, ',', '.') }}</td>
                        <td class="text-center">{{ ucfirst($fee->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @include('exports.partials.pdf-validation')
</body>
</html>
