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
        .kpi-row { margin-bottom: 12px; }
        .kpi-box { display: inline-block; width: 32%; vertical-align: top; padding: 8px 10px; border: 1px solid #e5e7eb; border-radius: 6px; text-align: center; }
        .kpi-box .kpi-value { font-size: 18px; font-weight: bold; color: #3DA746; }
        .kpi-box .kpi-label { font-size: 9px; color: #666; margin-top: 2px; }
        .kpi-box.danger .kpi-value { color: #e53e3e; }
        .kpi-box.warning .kpi-value { color: #f59e0b; }
        .aging-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .aging-table th, .aging-table td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        .aging-table th { background: #f0f0f0; text-align: left; }
        .aging-bar { display: inline-block; height: 10px; background: #3DA746; min-width: 1px; vertical-align: middle; }
        .aging-bar.warning { background: #f59e0b; }
        .aging-bar.danger { background: #e53e3e; }
        .insight-box { margin-top: 16px; padding: 10px 12px; background: #fffbeb; border-left: 3px solid #f59e0b; font-size: 10px; color: #92400e; }
        .insight-box strong { color: #78350f; }
    </style>
</head>
<body>
    <h3>Ringkasan SPP — {{ $periodLabel }}</h3>

    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-value">{{ $collectionRate }}%</div>
            <div class="kpi-label">Collection Rate</div>
        </div>
        <div class="kpi-box danger">
            <div class="kpi-value">{{ $delinquencyRate }}%</div>
            <div class="kpi-label">Delinquency Rate ({{ $siswaMenunggak }} siswa)</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">Rp {{ number_format($totalCollected / 1000000, 1) }}jt</div>
            <div class="kpi-label">Terkumpul</div>
        </div>
    </div>

    <table class="summary">
        <tr>
            <td class="label">Total Tagihan Semester Ini</td>
            <td class="value">Rp {{ number_format($totalInvoiced, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Terbayar</td>
            <td class="value-highlight">Rp {{ number_format($totalCollected, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Tunggakan</td>
            <td class="value-danger">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h3>Aging Analysis Tunggakan</h3>
    @php $maxAging = max(array_column($aging, 'amount') ?: [1]); @endphp
    <table class="aging-table">
        <thead>
            <tr>
                <th style="width: 90px;">Kategori</th>
                <th class="text-center" style="width: 50px;">Tagihan</th>
                <th class="text-right" style="width: 110px;">Jumlah</th>
                <th>Proporsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aging as $idx => $a)
                <tr>
                    <td>{{ $a['label'] }}</td>
                    <td class="text-center">{{ $a['count'] }}</td>
                    <td class="text-right">Rp {{ number_format($a['amount'], 0, ',', '.') }}</td>
                    <td>
                        <span class="aging-bar {{ $idx >= 2 ? ($idx >= 3 ? 'danger' : 'warning') : '' }}" style="width: {{ $maxAging > 0 ? max(round($a['amount'] / $maxAging * 150), 1) : 1 }}px;"></span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Pembayaran SPP per Bulan</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">Tagihan</th>
                <th class="text-right">Terbayar</th>
                <th class="text-center">Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyBreakdown as $mb)
                <tr>
                    <td>{{ $mb['label'] }}</td>
                    <td class="text-right">Rp {{ number_format($mb['invoiced'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($mb['value'], 0, ',', '.') }}</td>
                    <td class="text-center" style="color: {{ $mb['rate'] >= 80 ? '#3DA746' : ($mb['rate'] >= 50 ? '#f59e0b' : '#e53e3e') }}; font-weight: bold;">{{ $mb['rate'] }}%</td>
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
            <div class="class-header">{{ $kelas }} ({{ $invoices->count() }} tagihan — Rp {{ number_format($invoices->sum('jumlah'), 0, ',', '.') }})</div>
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

    @if($collectionRate < 80)
        <div class="insight-box">
            <strong>Perhatian:</strong> Collection rate {{ $collectionRate }}% masih di bawah target 80%.
            {{ $siswaMenunggak }} siswa memiliki tunggakan aktif. Disarankan untuk mengirim pengingat pembayaran secara berkala.
        </div>
    @endif

    @include('exports.partials.pdf-validation')
</body>
</html>
