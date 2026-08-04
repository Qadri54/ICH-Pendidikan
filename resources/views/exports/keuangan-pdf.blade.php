<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Laporan Keuangan — Tahun ' . $year])
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
        .kpi-box { display: inline-block; width: 24%; vertical-align: top; padding: 8px 6px; border: 1px solid #e5e7eb; border-radius: 6px; text-align: center; }
        .kpi-box .kpi-value { font-size: 16px; font-weight: bold; color: #3DA746; }
        .kpi-box .kpi-label { font-size: 8px; color: #666; margin-top: 2px; }
        .kpi-box.danger .kpi-value { color: #e53e3e; }
        .bar-container { margin: 10px 0; }
        .bar-row { margin-bottom: 3px; }
        .bar-label { display: inline-block; width: 50px; font-size: 8px; text-align: right; padding-right: 6px; vertical-align: middle; }
        .bar-track { display: inline-block; width: 350px; height: 14px; background: #f0f0f0; vertical-align: middle; }
        .bar-fill { display: inline-block; height: 14px; min-width: 1px; vertical-align: top; }
        .bar-fill-spp { background: #3DA746; }
        .bar-fill-reg { background: #3b82f6; }
        .bar-value { display: inline-block; font-size: 8px; padding-left: 6px; vertical-align: middle; }
        .delta-positive { color: #3DA746; font-weight: bold; }
        .delta-negative { color: #e53e3e; font-weight: bold; }
    </style>
</head>
<body>
    <h3>Ringkasan Keuangan — Tahun {{ $year }}</h3>

    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-value">Rp {{ number_format($totalPendapatan / 1000000, 1) }}jt</div>
            <div class="kpi-label">Total Pendapatan</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $collectionRate }}%</div>
            <div class="kpi-label">Tingkat Pembayaran SPP</div>
            <div style="font-size: 7px; color: #999; margin-top: 1px;">% tagihan terbayar</div>
        </div>
        <div class="kpi-box danger">
            <div class="kpi-value">Rp {{ number_format($totalOutstanding / 1000000, 1) }}jt</div>
            <div class="kpi-label">Tunggakan SPP</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">
                @if($yearDelta !== null)
                    <span class="{{ $yearDelta >= 0 ? 'delta-positive' : 'delta-negative' }}">{{ $yearDelta >= 0 ? '+' : '' }}{{ $yearDelta }}%</span>
                @else
                    —
                @endif
            </div>
            <div class="kpi-label">vs {{ $prevYear }}</div>
        </div>
    </div>

    <table class="summary">
        <tr>
            <td class="label">Total SPP Terkumpul</td>
            <td class="value">Rp {{ number_format($totalSpp, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Biaya Pendaftaran</td>
            <td class="value">Rp {{ number_format($totalPendaftaran, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Tabungan (Dana Kelolaan)</td>
            <td class="value">Rp {{ number_format($totalTabungan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label" style="padding-top: 8px; border-top: 1px solid #ddd;">Total Pendapatan</td>
            <td class="value-highlight" style="padding-top: 8px; border-top: 1px solid #ddd;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
        </tr>
        @if($yearDelta !== null)
        <tr>
            <td class="label">Pendapatan Tahun {{ $prevYear }}</td>
            <td class="value">Rp {{ number_format($prevTotal, 0, ',', '.') }}</td>
        </tr>
        @endif
    </table>

    <h3>Grafik Pendapatan per Bulan</h3>
    <div class="bar-container">
        @foreach($monthlySummary as $ms)
            @php $total = $ms['spp'] + $ms['pendaftaran']; @endphp
            <div class="bar-row">
                <span class="bar-label">{{ $ms['label'] }}</span>
                <span class="bar-track">
                    <span class="bar-fill bar-fill-spp" style="width: {{ $maxMonthly > 0 ? round($ms['spp'] / $maxMonthly * 100) : 0 }}%;"></span><span class="bar-fill bar-fill-reg" style="width: {{ $maxMonthly > 0 ? round($ms['pendaftaran'] / $maxMonthly * 100) : 0 }}%;"></span>
                </span>
                <span class="bar-value">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>
    <div style="font-size: 8px; color: #666; margin-top: 4px;">
        <span style="display: inline-block; width: 10px; height: 10px; background: #3DA746; vertical-align: middle;"></span> SPP &nbsp;
        <span style="display: inline-block; width: 10px; height: 10px; background: #3b82f6; vertical-align: middle;"></span> Pendaftaran
    </div>

    @if(count($monthlySummary) > 0)
    <h3>Ringkasan Pendapatan per Bulan</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">SPP</th>
                <th class="text-right">Pendaftaran</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlySummary as $ms)
                <tr>
                    <td>{{ $ms['label'] }}</td>
                    <td class="text-right">Rp {{ number_format($ms['spp'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($ms['pendaftaran'], 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($ms['spp'] + $ms['pendaftaran'], 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f0f0f0; border-top: 2px solid #333;">
                <td>Total Setahun</td>
                <td class="text-right">Rp {{ number_format(collect($monthlySummary)->sum('spp'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format(collect($monthlySummary)->sum('pendaftaran'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format(collect($monthlySummary)->sum('spp') + collect($monthlySummary)->sum('pendaftaran'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    @include('exports.partials.pdf-validation')
</body>
</html>
