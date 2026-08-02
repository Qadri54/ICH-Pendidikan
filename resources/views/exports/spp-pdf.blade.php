<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan SPP</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Laporan SPP — ' . $periodLabel])
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
        .bar-container { margin: 10px 0; }
        .bar-row { margin-bottom: 3px; }
        .bar-label { display: inline-block; width: 80px; font-size: 8px; text-align: right; padding-right: 6px; vertical-align: middle; }
        .bar-track { display: inline-block; width: 280px; height: 14px; background: #f0f0f0; vertical-align: middle; }
        .bar-fill { display: inline-block; height: 14px; background: #3DA746; min-width: 1px; }
        .bar-value { display: inline-block; font-size: 8px; padding-left: 6px; vertical-align: middle; }
        .class-header { background: #e8f5e9; padding: 6px 8px; font-weight: bold; font-size: 11px; margin-top: 12px; border-left: 3px solid #3DA746; }
    </style>
</head>
<body>
    <h3>Ringkasan SPP — {{ $periodLabel }}</h3>
    <table class="summary">
        <tr>
            <td class="label">Total SPP Terbayar Semester Ini</td>
            <td class="value-highlight">Rp {{ number_format($totalCollected, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Tunggakan SPP</td>
            <td class="value-danger">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h3>Pembayaran SPP per Bulan</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">Jumlah Terbayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyBreakdown as $mb)
                <tr>
                    <td>{{ $mb['label'] }}</td>
                    <td class="text-right">Rp {{ number_format($mb['value'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Grafik SPP per Bulan</h3>
    <div class="bar-container">
        @foreach($monthlyBreakdown as $bar)
            <div class="bar-row">
                <span class="bar-label">{{ $bar['label'] }}</span>
                <span class="bar-track">
                    <span class="bar-fill" style="width: {{ $maxMonthly > 0 ? round($bar['value'] / $maxMonthly * 100) : 0 }}%;"></span>
                </span>
                <span class="bar-value">Rp {{ number_format($bar['value'], 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    @if($unpaidByClass->count() > 0)
        <h3>Daftar Tunggakan SPP per Kelas</h3>
        @foreach($unpaidByClass as $kelas => $invoices)
            <div class="class-header">{{ $kelas }} ({{ $invoices->count() }} tagihan)</div>
            <table class="data">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Nama Siswa</th>
                        <th>Orang Tua</th>
                        <th>Periode</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $i => $inv)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $inv->student?->nama_siswa ?? '-' }}</td>
                            <td>{{ $inv->student?->user?->name ?? '-' }}</td>
                            <td>{{ $inv->tanggal_tahun?->translatedFormat('F Y') ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($inv->jumlah, 0, ',', '.') }}</td>
                            <td class="text-center">{{ ucfirst($inv->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif

    @include('exports.partials.pdf-validation')
</body>
</html>
