<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Siswa</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Rekap Absensi Siswa'])
    <style>
        .filter-info { color: #555; font-size: 11px; margin-bottom: 16px; background: #f9fafb; padding: 8px 12px; border-radius: 4px; }
        .filter-info strong { color: #333; }
        h3 { font-size: 13px; margin: 0 0 6px 0; color: #333; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th { background: #f0f0f0; text-align: left; padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data tr:nth-child(even) { background: #fafafa; }
        .text-center { text-align: center; }
        tfoot td { font-weight: bold; background: #f0f0f0; }
    </style>
</head>
<body>
    <h3>Rekap Absensi Siswa</h3>
    <div class="filter-info">
        <strong>Kelas:</strong> {{ $classroom->nama_kelas }} &nbsp;&middot;&nbsp;
        <strong>Periode:</strong> {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th class="text-center">Hadir</th>
                <th class="text-center">Izin</th>
                <th class="text-center">Sakit</th>
                <th class="text-center">Tanpa Ket.</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recap as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td class="text-center">{{ $row['hadir'] ?? 0 }}</td>
                    <td class="text-center">{{ $row['izin'] }}</td>
                    <td class="text-center">{{ $row['sakit'] }}</td>
                    <td class="text-center">{{ $row['tanpa_keterangan'] }}</td>
                    <td class="text-center">{{ ($row['hadir'] ?? 0) + $row['izin'] + $row['sakit'] + $row['tanpa_keterangan'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #999;">Tidak ada data absensi.</td>
                </tr>
            @endforelse
        </tbody>
        @if($recap->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    <td class="text-center">{{ $recap->sum(fn($r) => $r['hadir'] ?? 0) }}</td>
                    <td class="text-center">{{ $recap->sum('izin') }}</td>
                    <td class="text-center">{{ $recap->sum('sakit') }}</td>
                    <td class="text-center">{{ $recap->sum('tanpa_keterangan') }}</td>
                    <td class="text-center">{{ $recap->sum(fn($r) => ($r['hadir'] ?? 0) + $r['izin'] + $r['sakit'] + $r['tanpa_keterangan']) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    @include('exports.partials.pdf-validation')
</body>
</html>
