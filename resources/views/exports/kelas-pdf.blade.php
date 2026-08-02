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
        .summary .value-highlight { font-weight: bold; font-size: 15px; color: #3DA746; }
        h3 { font-size: 13px; margin: 20px 0 6px 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th { background: #f0f0f0; text-align: left; padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data tr:nth-child(even) { background: #fafafa; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h3>Ringkasan Data Kelas</h3>
    <table class="summary">
        <tr>
            <td class="label">Total Kelas</td>
            <td class="value-highlight">{{ $totalClasses }}</td>
        </tr>
    </table>

    <h3>Daftar Kelas</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Kelas</th>
                <th>Ruangan</th>
                <th>Wali Kelas</th>
                <th class="text-center">Jumlah Siswa Aktif</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classes as $i => $kelas)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $kelas->nama_kelas }}</td>
                    <td>{{ $kelas->nama_ruangan ?? '-' }}</td>
                    <td>{{ $kelas->homeroomTeacher?->user?->name ?? '-' }}</td>
                    <td class="text-center">{{ $kelas->students->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @include('exports.partials.pdf-validation')
</body>
</html>
