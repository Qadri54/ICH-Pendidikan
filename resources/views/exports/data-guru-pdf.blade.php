<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Guru</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Laporan Data Guru'])
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
        .status-aktif { color: #3DA746; font-weight: bold; }
        .status-nonaktif { color: #e53e3e; font-weight: bold; }
        .kpi-row { margin-bottom: 16px; }
        .kpi-box { display: inline-block; width: 24%; vertical-align: top; padding: 8px 6px; border: 1px solid #e5e7eb; border-radius: 6px; text-align: center; }
        .kpi-box .kpi-value { font-size: 16px; font-weight: bold; color: #3DA746; }
        .kpi-box .kpi-label { font-size: 8px; color: #666; margin-top: 2px; }
        .tipe-table { width: auto; border-collapse: collapse; margin-top: 6px; }
        .tipe-table td { padding: 4px 12px; font-size: 10px; }
        .tipe-bar { display: inline-block; height: 12px; background: #3DA746; min-width: 2px; vertical-align: middle; }
    </style>
</head>
<body>
    <h3>Ringkasan Data Guru</h3>

    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-value">{{ $totalAktif }}</div>
            <div class="kpi-label">Guru Aktif</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $guruNonaktif->count() }}</div>
            <div class="kpi-label">Guru Nonaktif</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">1 : {{ $rasio }}</div>
            <div class="kpi-label">Rasio Guru : Siswa</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $totalSiswaAktif }}</div>
            <div class="kpi-label">Total Siswa Aktif</div>
        </div>
    </div>

    @if($tipeBreakdown->count() > 0)
    <h3>Komposisi Guru berdasarkan Tipe</h3>
    @php $maxTipe = $tipeBreakdown->max() ?: 1; @endphp
    <table class="tipe-table">
        @foreach($tipeBreakdown as $tipe => $count)
            <tr>
                <td style="width: 100px; text-align: right; font-weight: bold;">{{ $tipe ?: '-' }}</td>
                <td>
                    <span class="tipe-bar" style="width: {{ round($count / $maxTipe * 150) }}px;"></span>
                    <span style="font-size: 9px; padding-left: 4px;">{{ $count }} guru</span>
                </td>
            </tr>
        @endforeach
    </table>
    @endif

    <h3>Daftar Guru</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Nama Guru</th>
                <th>NIP</th>
                <th>Tipe</th>
                <th>Wali Kelas</th>
                <th>Tanggal Masuk</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($guruAktif as $guru)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $guru->user?->name ?? '-' }}</td>
                    <td>{{ $guru->NIP ?? '-' }}</td>
                    <td>{{ $guru->tipe ?? '-' }}</td>
                    <td>{{ $guru->homeroomClass?->nama_kelas ?? '-' }}</td>
                    <td>{{ $guru->hire_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center status-aktif">Aktif</td>
                </tr>
            @endforeach
            @foreach($guruNonaktif as $guru)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $guru->user?->name ?? '-' }}</td>
                    <td>{{ $guru->NIP ?? '-' }}</td>
                    <td>{{ $guru->tipe ?? '-' }}</td>
                    <td>{{ $guru->homeroomClass?->nama_kelas ?? '-' }}</td>
                    <td>{{ $guru->hire_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center status-nonaktif">Nonaktif</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @include('exports.partials.pdf-validation')
</body>
</html>
