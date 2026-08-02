<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Siswa</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Laporan Data Siswa'])
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
        .bar-label { display: inline-block; width: 70px; font-size: 8px; text-align: right; padding-right: 6px; vertical-align: middle; }
        .bar-track { display: inline-block; width: 300px; height: 14px; background: #f0f0f0; vertical-align: middle; }
        .bar-fill { display: inline-block; height: 14px; background: #3DA746; min-width: 1px; }
        .bar-value { display: inline-block; font-size: 8px; padding-left: 6px; vertical-align: middle; }
    </style>
</head>
<body>
    <h3>Ringkasan Data Siswa</h3>
    <table class="summary">
        <tr>
            <td class="label">Total Siswa Aktif</td>
            <td class="value-highlight">{{ $totalAktif }}</td>
        </tr>
        <tr>
            <td class="label">Total Alumni</td>
            <td class="value">{{ $totalAlumni }}</td>
        </tr>
        <tr>
            <td class="label">Total Keluar</td>
            <td class="value">{{ $totalKeluar }}</td>
        </tr>
        <tr>
            <td class="label">Siswa Tanpa Akun Orang Tua</td>
            <td class="value">{{ $tanpaAkun }}</td>
        </tr>
    </table>

    <h3>Grafik Pertumbuhan Jumlah Siswa (12 Bulan Terakhir)</h3>
    <div class="bar-container">
        @foreach($growthData as $bar)
            <div class="bar-row">
                <span class="bar-label">{{ $bar['label'] }}</span>
                <span class="bar-track">
                    <span class="bar-fill" style="width: {{ $maxGrowth > 0 ? round($bar['value'] / $maxGrowth * 100) : 0 }}%;"></span>
                </span>
                <span class="bar-value">{{ $bar['value'] }}</span>
            </div>
        @endforeach
    </div>

    @foreach($studentsGrouped as $kelas => $students)
        <h3>{{ $kelas }} ({{ $students->count() }} siswa)</h3>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $i => $siswa)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $siswa->NIS ?? '-' }}</td>
                        <td>{{ $siswa->nama_siswa }}</td>
                        <td class="text-center">{{ $siswa->jenis_kelamin }}</td>
                        <td>{{ $siswa->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    @include('exports.partials.pdf-validation')
</body>
</html>
