<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Laporan Keuangan'])
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
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-unpaid { background: #fef3c7; color: #92400e; }
        .badge-overdue { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <h3>Ringkasan Keuangan</h3>
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
            <td class="label">Total Tabungan</td>
            <td class="value">Rp {{ number_format($totalTabungan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label" style="padding-top: 8px; border-top: 1px solid #ddd;">Total Pendapatan</td>
            <td class="value-highlight" style="padding-top: 8px; border-top: 1px solid #ddd;">Rp {{ number_format($totalSpp + $totalPendaftaran, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if(isset($monthlySummary) && count($monthlySummary) > 0)
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

    <h3>Detail Tagihan SPP ({{ $invoices->count() }} data)</h3>
    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Periode</th>
                <th class="text-right">Jumlah</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $i => $inv)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $inv->student?->nama_siswa ?? '-' }}</td>
                    <td>{{ $inv->student?->classRoom?->nama_kelas ?? '-' }}</td>
                    <td>{{ $inv->tanggal_tahun?->translatedFormat('F Y') ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($inv->jumlah, 0, ',', '.') }}</td>
                    <td>{{ $inv->jatuh_tempo?->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $inv->status }}">
                            {{ ucfirst($inv->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
