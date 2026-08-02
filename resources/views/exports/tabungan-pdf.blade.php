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
    </style>
</head>
<body>
    <h3>Ringkasan Tabungan</h3>
    <table class="summary">
        <tr>
            <td class="label">Total Tabungan Semua Kelas</td>
            <td class="value-highlight">Rp {{ number_format($totalSavings, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h3>Total Tabungan per Kelas</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Kelas</th>
                <th class="text-right">Total Tabungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($savingsPerClass as $kelas => $total)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $kelas }}</td>
                    <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

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
