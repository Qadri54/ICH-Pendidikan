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
        .kpi-row { margin-bottom: 16px; }
        .kpi-box { display: inline-block; width: 19%; vertical-align: top; padding: 8px 4px; border: 1px solid #e5e7eb; border-radius: 6px; text-align: center; }
        .kpi-box .kpi-value { font-size: 16px; font-weight: bold; color: #3DA746; }
        .kpi-box .kpi-label { font-size: 8px; color: #666; margin-top: 2px; }
        .kpi-box.info .kpi-value { color: #3b82f6; }
        .kpi-box.danger .kpi-value { color: #e53e3e; }
        .gender-bar { margin: 8px 0 16px 0; height: 20px; background: #f0f0f0; border-radius: 4px; overflow: hidden; }
        .gender-bar-l { display: inline-block; height: 20px; background: #3b82f6; float: left; }
        .gender-bar-p { display: inline-block; height: 20px; background: #ec4899; float: left; }
    </style>
</head>
<body>
    <h3>Ringkasan Data Siswa</h3>

    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-value">{{ $totalAktif }}</div>
            <div class="kpi-label">Siswa Aktif</div>
        </div>
        <div class="kpi-box info">
            <div class="kpi-value">{{ $totalAlumni }}</div>
            <div class="kpi-label">Alumni</div>
        </div>
        <div class="kpi-box danger">
            <div class="kpi-value">{{ $totalKeluar }}</div>
            <div class="kpi-label">Keluar</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $retentionRate }}%</div>
            <div class="kpi-label">Tingkat Retensi</div>
            <div style="font-size: 7px; color: #999; margin-top: 1px;">% siswa yang bertahan</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $tanpaAkun }}</div>
            <div class="kpi-label">Tanpa Akun Ortu</div>
        </div>
    </div>

    <h3>Distribusi Gender</h3>
    @php
        $totalGender = $totalL + $totalP;
        $pctL = $totalGender > 0 ? round($totalL / $totalGender * 100) : 0;
        $pctP = $totalGender > 0 ? round($totalP / $totalGender * 100) : 0;
    @endphp
    <div class="gender-bar">
        <span class="gender-bar-l" style="width: {{ $pctL }}%;"></span>
        <span class="gender-bar-p" style="width: {{ $pctP }}%;"></span>
    </div>
    <div style="font-size: 9px; color: #555;">
        <span style="display: inline-block; width: 10px; height: 10px; background: #3b82f6; vertical-align: middle;"></span>
        Laki-laki: {{ $totalL }} ({{ $pctL }}%) &nbsp;&nbsp;
        <span style="display: inline-block; width: 10px; height: 10px; background: #ec4899; vertical-align: middle;"></span>
        Perempuan: {{ $totalP }} ({{ $pctP }}%)
    </div>

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
        @php
            $kelasL = $students->where('jenis_kelamin', 'Laki-laki')->count();
            $kelasP = $students->where('jenis_kelamin', 'Perempuan')->count();
        @endphp
        <h3>{{ $kelas }} ({{ $students->count() }} siswa — {{ $kelasL }}L / {{ $kelasP }}P)</h3>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th class="text-center">JK</th>
                    <th>Tanggal Lahir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $i => $siswa)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $siswa->NIS ?? '-' }}</td>
                        <td>{{ $siswa->nama_siswa }}</td>
                        <td class="text-center">{{ $siswa->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</td>
                        <td>{{ $siswa->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    @include('exports.partials.pdf-validation')
</body>
</html>
