<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Guru</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Rekap Absensi Guru'])
    <style>
        .filter-info { color: #555; font-size: 11px; margin-bottom: 16px; background: #f9fafb; padding: 8px 12px; border-radius: 4px; }
        .filter-info strong { color: #333; }
        h3 { font-size: 13px; margin: 0 0 6px 0; color: #333; }
        .summary { margin-bottom: 16px; }
        .summary td { padding: 4px 12px 4px 0; }
        .summary .label { color: #666; font-size: 11px; }
        .summary .value { font-weight: bold; font-size: 13px; }
        .summary .value-highlight { font-weight: bold; font-size: 15px; color: #3DA746; }
        .summary .value-danger { font-weight: bold; font-size: 13px; color: #e53e3e; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th { background: #f0f0f0; text-align: left; padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data tr:nth-child(even) { background: #fafafa; }
        .text-center { text-align: center; }
        tfoot td { font-weight: bold; background: #f0f0f0; }
        .row-warning { background: #fff5f5 !important; }
        .text-danger { color: #e53e3e; font-weight: bold; }
        .text-success { color: #3DA746; }
        .insight-box { margin-top: 16px; padding: 10px 12px; background: #fffbeb; border-left: 3px solid #f59e0b; font-size: 10px; color: #92400e; }
        .insight-box strong { color: #78350f; }
    </style>
</head>
<body>
    <h3>Rekap Absensi Guru</h3>
    <div class="filter-info">
        <strong>Periode:</strong> {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
    </div>

    @php
        $totalGuru = $recap->count();
        $avgKehadiran = $hariEfektif > 0 && $totalGuru > 0
            ? round($recap->sum('hadir') / ($totalGuru * $hariEfektif) * 100, 1)
            : 0;
        $guruDibawah75 = $hariEfektif > 0
            ? $recap->filter(fn($r) => ($r['hadir'] / max($hariEfektif, 1) * 100) < 75)->count()
            : 0;
    @endphp

    <table class="summary">
        <tr>
            <td class="label">Hari Efektif Kerja</td>
            <td class="value-highlight">{{ $hariEfektif }} hari</td>
        </tr>
        <tr>
            <td class="label">Total Guru</td>
            <td class="value">{{ $totalGuru }}</td>
        </tr>
        <tr>
            <td class="label">Rata-rata Kehadiran</td>
            <td class="{{ $avgKehadiran >= 75 ? 'value' : 'value-danger' }}">{{ $avgKehadiran }}%</td>
        </tr>
        @if($guruDibawah75 > 0)
        <tr>
            <td class="label">Guru dengan Kehadiran &lt; 75%</td>
            <td class="value-danger">{{ $guruDibawah75 }} guru</td>
        </tr>
        @endif
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Guru</th>
                <th class="text-center">Hadir</th>
                <th class="text-center">Izin</th>
                <th class="text-center">Sakit</th>
                <th class="text-center">Tanpa Ket.</th>
                <th class="text-center">Total</th>
                <th class="text-center">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recap as $i => $row)
                @php
                    $total = $row['hadir'] + $row['izin'] + $row['sakit'] + $row['tanpa_keterangan'];
                    $pctHadir = $hariEfektif > 0 ? round($row['hadir'] / $hariEfektif * 100, 1) : 0;
                    $isLow = $pctHadir < 75;
                @endphp
                <tr class="{{ $isLow ? 'row-warning' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td class="text-center">{{ $row['hadir'] }}</td>
                    <td class="text-center">{{ $row['izin'] }}</td>
                    <td class="text-center">{{ $row['sakit'] }}</td>
                    <td class="text-center">{{ $row['tanpa_keterangan'] }}</td>
                    <td class="text-center">{{ $total }}</td>
                    <td class="text-center {{ $isLow ? 'text-danger' : 'text-success' }}">{{ $pctHadir }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #999;">Tidak ada data absensi.</td>
                </tr>
            @endforelse
        </tbody>
        @if($recap->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    <td class="text-center">{{ $recap->sum('hadir') }}</td>
                    <td class="text-center">{{ $recap->sum('izin') }}</td>
                    <td class="text-center">{{ $recap->sum('sakit') }}</td>
                    <td class="text-center">{{ $recap->sum('tanpa_keterangan') }}</td>
                    <td class="text-center">{{ $recap->sum(fn($r) => $r['hadir'] + $r['izin'] + $r['sakit'] + $r['tanpa_keterangan']) }}</td>
                    <td class="text-center">{{ $avgKehadiran }}%</td>
                </tr>
            </tfoot>
        @endif
    </table>

    @if($guruDibawah75 > 0)
        <div class="insight-box">
            <strong>Perhatian:</strong> {{ $guruDibawah75 }} guru memiliki tingkat kehadiran di bawah 75% (ditandai merah).
            Evaluasi diperlukan untuk memastikan kelangsungan proses belajar mengajar.
        </div>
    @endif

    @include('exports.partials.pdf-validation')
</body>
</html>
