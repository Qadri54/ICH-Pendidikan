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
    </style>
</head>
<body>
    <h3>Ringkasan Pendaftaran — {{ $periodLabel }}</h3>
    <table class="summary">
        <tr>
            <td class="label">Total Pendaftaran Semester Ini</td>
            <td class="value-highlight">{{ $totalRegistrations }}</td>
        </tr>
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
