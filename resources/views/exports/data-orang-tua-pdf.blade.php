<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Orang Tua</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Laporan Data Orang Tua'])
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
    <h3>Ringkasan Data Orang Tua</h3>
    <table class="summary">
        <tr>
            <td class="label">Total Akun Orang Tua (Anak Masih Aktif)</td>
            <td class="value-highlight">{{ $totalAktif }}</td>
        </tr>
        <tr>
            <td class="label">Total Seluruh Akun Orang Tua</td>
            <td class="value">{{ $totalAkun }}</td>
        </tr>
    </table>

    <h3>Daftar Orang Tua Aktif</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Orang Tua</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activeParents as $i => $parent)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $parent->name }}</td>
                    <td>{{ $parent->email }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($unpaidInvoiceParents->count() > 0)
        <h3>Orang Tua yang Belum Membayar SPP</h3>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Orang Tua</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th class="text-right">Jumlah Tunggakan</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($unpaidInvoiceParents as $data)
                    @foreach($data['students'] as $student)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $data['parent']?->name ?? '-' }}</td>
                            <td>{{ $student->nama_siswa }}</td>
                            <td>{{ $student->classRoom?->nama_kelas ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($student->sppInvoices->sum('jumlah'), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif

    @include('exports.partials.pdf-validation')
</body>
</html>
