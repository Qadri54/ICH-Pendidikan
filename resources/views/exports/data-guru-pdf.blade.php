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
    </style>
</head>
<body>
    <h3>Ringkasan Data Guru</h3>
    <table class="summary">
        <tr>
            <td class="label">Total Guru Aktif</td>
            <td class="value-highlight">{{ $totalAktif }}</td>
        </tr>
        <tr>
            <td class="label">Total Guru Nonaktif</td>
            <td class="value">{{ $guruNonaktif->count() }}</td>
        </tr>
    </table>

    <h3>Daftar Guru</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Guru</th>
                <th>NIP</th>
                <th>Tipe</th>
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
                    <td>{{ $guru->hire_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center status-nonaktif">Nonaktif</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @include('exports.partials.pdf-validation')
</body>
</html>
