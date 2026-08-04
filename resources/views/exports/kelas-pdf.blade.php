<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Kelas</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Laporan Data Kelas'])
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
        .text-center { text-align: center; }
        tfoot td { font-weight: bold; background: #f0f0f0; }
        .bar-container { margin: 10px 0; }
        .bar-row { margin-bottom: 3px; }
        .bar-label { display: inline-block; width: 60px; font-size: 9px; text-align: right; padding-right: 6px; vertical-align: middle; }
        .bar-track { display: inline-block; width: 300px; height: 14px; background: #f0f0f0; vertical-align: middle; }
        .bar-fill { display: inline-block; height: 14px; min-width: 1px; vertical-align: top; }
        .bar-fill-l { background: #3b82f6; }
        .bar-fill-p { background: #ec4899; }
        .bar-value { display: inline-block; font-size: 8px; padding-left: 6px; vertical-align: middle; }
    </style>
</head>
<body>
    <h3>Ringkasan Data Kelas</h3>
    <table class="summary">
        <tr>
            <td class="label">Total Kelas</td>
            <td class="value-highlight">{{ $totalClasses }}</td>
        </tr>
        <tr>
            <td class="label">Total Siswa Aktif</td>
            <td class="value">{{ $totalSiswa }}</td>
        </tr>
        <tr>
            <td class="label">Komposisi Gender</td>
            <td class="value">{{ $totalL }} L / {{ $totalP }} P</td>
        </tr>
        <tr>
            <td class="label">Rasio Guru : Siswa</td>
            <td class="value">1 : {{ $rasio }}</td>
        </tr>
        <tr>
            <td class="label">Rata-rata Siswa per Kelas</td>
            <td class="value">{{ $totalClasses > 0 ? round($totalSiswa / $totalClasses) : 0 }}</td>
        </tr>
    </table>

    <h3>Distribusi Siswa per Kelas</h3>
    @php $maxStudents = $classes->max(fn($c) => $c->students->count()) ?: 1; @endphp
    <div class="bar-container">
        @foreach($classes as $kelas)
            @php
                $countL = $kelas->students->where('jenis_kelamin', 'L')->count();
                $countP = $kelas->students->where('jenis_kelamin', 'P')->count();
                $countTotal = $countL + $countP;
                $widthL = $maxStudents > 0 ? round($countL / $maxStudents * 100) : 0;
                $widthP = $maxStudents > 0 ? round($countP / $maxStudents * 100) : 0;
            @endphp
            <div class="bar-row">
                <span class="bar-label">{{ $kelas->nama_kelas }}</span>
                <span class="bar-track">
                    <span class="bar-fill bar-fill-l" style="width: {{ $widthL }}%;"></span><span class="bar-fill bar-fill-p" style="width: {{ $widthP }}%;"></span>
                </span>
                <span class="bar-value">{{ $countTotal }} ({{ $countL }}L / {{ $countP }}P)</span>
            </div>
        @endforeach
    </div>
    <div style="font-size: 8px; color: #666; margin-top: 4px;">
        <span style="display: inline-block; width: 10px; height: 10px; background: #3b82f6; vertical-align: middle;"></span> Laki-laki &nbsp;
        <span style="display: inline-block; width: 10px; height: 10px; background: #ec4899; vertical-align: middle;"></span> Perempuan
    </div>

    <h3>Daftar Kelas</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Kelas</th>
                <th>Ruangan</th>
                <th>Wali Kelas</th>
                <th class="text-center">L</th>
                <th class="text-center">P</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classes as $i => $kelas)
                @php
                    $countL = $kelas->students->where('jenis_kelamin', 'L')->count();
                    $countP = $kelas->students->where('jenis_kelamin', 'P')->count();
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $kelas->nama_kelas }}</td>
                    <td>{{ $kelas->nama_ruangan ?? '-' }}</td>
                    <td>{{ $kelas->homeroomTeacher?->user?->name ?? '-' }}</td>
                    <td class="text-center">{{ $countL }}</td>
                    <td class="text-center">{{ $countP }}</td>
                    <td class="text-center"><strong>{{ $countL + $countP }}</strong></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td class="text-center">{{ $totalL }}</td>
                <td class="text-center">{{ $totalP }}</td>
                <td class="text-center">{{ $totalSiswa }}</td>
            </tr>
        </tfoot>
    </table>

    @include('exports.partials.pdf-validation')
</body>
</html>
