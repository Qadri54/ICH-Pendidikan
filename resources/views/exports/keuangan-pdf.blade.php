<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .subtitle { color: #666; font-size: 12px; margin-bottom: 20px; }
        .summary { margin-bottom: 20px; }
        .summary td { padding: 4px 12px 4px 0; }
        .summary .label { color: #666; }
        .summary .value { font-weight: bold; font-size: 13px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
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
    <h1>TK IQRA' Creative House</h1>
    <div class="subtitle">Laporan Keuangan &mdash; Dicetak {{ now()->translatedFormat('d F Y') }}</div>

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
            <td class="label">Total Pendapatan</td>
            <td class="value">Rp {{ number_format($totalSpp + $totalPendaftaran, 0, ',', '.') }}</td>
        </tr>
    </table>

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
