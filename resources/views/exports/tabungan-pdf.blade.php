<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tabungan</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Laporan Tabungan Siswa'])
    <style>
        .summary { margin-bottom: 20px; }
        .summary td { padding: 4px 12px 4px 0; }
        .summary .label { color: #666; font-size: 11px; }
        .summary .value { font-weight: bold; font-size: 13px; }
        .summary .value-highlight { font-weight: bold; font-size: 15px; color: #3DA746; }
        h3 { font-size: 13px; margin: 20px 0 6px 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th { background: #f0f0f0; text-align: left; padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data tr:nth-child(even) { background: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bar-container { margin: 10px 0; }
        .bar-row { margin-bottom: 3px; }
        .bar-label { display: inline-block; width: 60px; font-size: 9px; text-align: right; padding-right: 6px; vertical-align: middle; }
        .bar-track { display: inline-block; width: 300px; height: 14px; background: #f0f0f0; vertical-align: middle; }
        .bar-fill { display: inline-block; height: 14px; background: #3DA746; min-width: 1px; }
        .bar-value { display: inline-block; font-size: 8px; padding-left: 6px; vertical-align: middle; }
        .kpi-row { margin-bottom: 16px; }
        .kpi-box { display: inline-block; width: 24%; vertical-align: top; padding: 8px 6px; border: 1px solid #e5e7eb; border-radius: 6px; text-align: center; }
        .kpi-box .kpi-value { font-size: 16px; font-weight: bold; color: #3DA746; }
        .kpi-box .kpi-label { font-size: 8px; color: #666; margin-top: 2px; }
    </style>
</head>
<body>
    <h3>Ringkasan Tabungan</h3>

    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-value">Rp {{ number_format($totalSavings / 1000, 0) }}rb</div>
            <div class="kpi-label">Total Tabungan</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $participationRate }}%</div>
            <div class="kpi-label">Partisipasi ({{ $totalPassbooks }}/{{ $totalSiswaAktif }})</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">Rp {{ number_format($avgBalance, 0, ',', '.') }}</div>
            <div class="kpi-label">Rata-rata Saldo</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $savingsPerClass->count() }}</div>
            <div class="kpi-label">Kelas Aktif</div>
        </div>
    </div>

    <h3>Total Tabungan per Kelas</h3>
    @php $maxClass = max($savingsPerClass->values()->toArray() ?: [1]); @endphp
    <div class="bar-container">
        @foreach($savingsPerClass as $kelas => $total)
            <div class="bar-row">
                <span class="bar-label">{{ $kelas }}</span>
                <span class="bar-track">
                    <span class="bar-fill" style="width: {{ $maxClass > 0 ? round($total / $maxClass * 100) : 0 }}%;"></span>
                </span>
                <span class="bar-value">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    <h3>Top 10 Siswa dengan Tabungan Terbanyak</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th class="text-right">Saldo Tabungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topStudents as $i => $passbook)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $passbook->student?->nama_siswa ?? '-' }}</td>
                    <td>{{ $passbook->student?->classRoom?->nama_kelas ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($passbook->current_balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @include('exports.partials.pdf-validation')
</body>
</html>
