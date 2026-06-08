<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .subtitle { color: #666; font-size: 12px; margin-bottom: 4px; }
        .filter-info { color: #888; font-size: 11px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f0f0f0; text-align: left; padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; }
        td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        tr:nth-child(even) { background: #fafafa; }
        .text-center { text-align: center; }
        tfoot td { font-weight: bold; background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>TK IQRA' Creative House</h1>
    <div class="subtitle">Rekap Absensi Siswa</div>
    <div class="filter-info">
        Kelas: {{ $classroom->nama_kelas }} &middot;
        Periode: {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }} &middot;
        Dicetak: {{ now()->translatedFormat('d F Y') }}
    </div>

    <table>
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
</body>
</html>
