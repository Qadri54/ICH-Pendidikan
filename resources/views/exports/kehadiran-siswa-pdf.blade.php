<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran — {{ $student->nama_siswa }}</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Laporan Kehadiran Siswa — ' . $bulanLabel])
    <style>
        .profile { margin-bottom: 20px; }
        .profile td { padding: 3px 12px 3px 0; }
        .profile .label { color: #666; font-size: 11px; width: 120px; }
        .profile .value { font-weight: bold; font-size: 12px; }
        h3 { font-size: 13px; margin: 20px 0 6px 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th { background: #f0f0f0; text-align: left; padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data tr:nth-child(even) { background: #fafafa; }
        .text-center { text-align: center; }
        tfoot td { font-weight: bold; background: #f0f0f0; }
        .summary-box { display: inline-block; width: 23%; vertical-align: top; padding: 10px 6px; border: 1px solid #e5e7eb; border-radius: 6px; text-align: center; margin-right: 1%; }
        .summary-box .s-value { font-size: 22px; font-weight: bold; }
        .summary-box .s-label { font-size: 9px; color: #666; margin-top: 2px; }
        .green { color: #3DA746; }
        .orange { color: #f59e0b; }
        .blue { color: #3b82f6; }
        .red { color: #e53e3e; }
        .status-hadir { color: #3DA746; font-weight: bold; }
        .status-sakit { color: #f59e0b; font-weight: bold; }
        .status-izin { color: #3b82f6; font-weight: bold; }
        .status-alpha { color: #e53e3e; font-weight: bold; }
        .pct-box { text-align: center; margin: 16px 0; }
        .pct-value { font-size: 36px; font-weight: bold; }
        .pct-label { font-size: 11px; color: #666; }
        .bar-visual { height: 20px; background: #f0f0f0; border-radius: 4px; overflow: hidden; margin: 10px 0; }
        .bar-segment { display: inline-block; height: 20px; float: left; }
        .insight-box { margin-top: 16px; padding: 10px 12px; border-left: 3px solid; font-size: 10px; }
        .insight-good { background: #f0fdf4; border-color: #3DA746; color: #166534; }
        .insight-warn { background: #fffbeb; border-color: #f59e0b; color: #92400e; }
        .ttd-row { margin-top: 40px; width: 100%; }
        .ttd-col { display: inline-block; width: 45%; text-align: center; font-size: 10px; vertical-align: top; }
        .ttd-col .ttd-line { margin-top: 60px; border-top: 1px solid #333; display: inline-block; width: 150px; }
    </style>
</head>
<body>
    <h3>Data Siswa</h3>
    <table class="profile">
        <tr>
            <td class="label">Nama Siswa</td>
            <td class="value">{{ $student->nama_siswa }}</td>
        </tr>
        <tr>
            <td class="label">NIS</td>
            <td class="value">{{ $student->NIS ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="value">{{ $student->classRoom?->nama_kelas ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Wali Kelas</td>
            <td class="value">{{ $student->classRoom?->homeroomTeacher?->user?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Orang Tua</td>
            <td class="value">{{ $student->user?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td class="value">{{ $bulanLabel }}</td>
        </tr>
    </table>

    <h3>Ringkasan Kehadiran</h3>

    @if($total > 0)
        <div class="pct-box">
            <div class="pct-value {{ $pctHadir >= 75 ? 'green' : 'red' }}">{{ $pctHadir }}%</div>
            <div class="pct-label">Tingkat Kehadiran</div>
        </div>

        <div class="bar-visual">
            @if($hadir > 0)<span class="bar-segment" style="width: {{ round($hadir/$total*100) }}%; background: #3DA746;"></span>@endif
            @if($sakit > 0)<span class="bar-segment" style="width: {{ round($sakit/$total*100) }}%; background: #f59e0b;"></span>@endif
            @if($izin > 0)<span class="bar-segment" style="width: {{ round($izin/$total*100) }}%; background: #3b82f6;"></span>@endif
            @if($alpha > 0)<span class="bar-segment" style="width: {{ round($alpha/$total*100) }}%; background: #e53e3e;"></span>@endif
        </div>
        <div style="font-size: 8px; color: #666; margin-bottom: 12px;">
            <span style="display: inline-block; width: 10px; height: 10px; background: #3DA746; vertical-align: middle;"></span> Hadir &nbsp;
            <span style="display: inline-block; width: 10px; height: 10px; background: #f59e0b; vertical-align: middle;"></span> Sakit &nbsp;
            <span style="display: inline-block; width: 10px; height: 10px; background: #3b82f6; vertical-align: middle;"></span> Izin &nbsp;
            <span style="display: inline-block; width: 10px; height: 10px; background: #e53e3e; vertical-align: middle;"></span> Alpha
        </div>

        <div style="margin-bottom: 16px;">
            <div class="summary-box">
                <div class="s-value green">{{ $hadir }}</div>
                <div class="s-label">Hadir</div>
            </div>
            <div class="summary-box">
                <div class="s-value orange">{{ $sakit }}</div>
                <div class="s-label">Sakit</div>
            </div>
            <div class="summary-box">
                <div class="s-value blue">{{ $izin }}</div>
                <div class="s-label">Izin</div>
            </div>
            <div class="summary-box">
                <div class="s-value red">{{ $alpha }}</div>
                <div class="s-label">Alpha</div>
            </div>
        </div>

        <h3>Rincian Harian</h3>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Hari / Tanggal</th>
                    <th class="text-center">Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $i => $att)
                    @php
                        $statusClass = match($att->status) {
                            'Hadir' => 'status-hadir',
                            'Sakit' => 'status-sakit',
                            'Izin'  => 'status-izin',
                            'Alpha' => 'status-alpha',
                            default => '',
                        };
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $att->created_at->translatedFormat('l, d F Y') }}</td>
                        <td class="text-center {{ $statusClass }}">{{ $att->status }}</td>
                        <td>{{ $att->keterangan_izin ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total Hari Tercatat</td>
                    <td class="text-center">{{ $total }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        @if($pctHadir >= 75)
            <div class="insight-box insight-good">
                <strong>Baik!</strong> Tingkat kehadiran {{ $student->nama_siswa }} sebesar {{ $pctHadir }}% memenuhi standar minimal 75%.
            </div>
        @else
            <div class="insight-box insight-warn">
                <strong>Perhatian:</strong> Tingkat kehadiran {{ $student->nama_siswa }} sebesar {{ $pctHadir }}% di bawah standar minimal 75%.
                Tercatat {{ $alpha }} hari alpha dan {{ $sakit + $izin }} hari tidak hadir (sakit/izin).
            </div>
        @endif
    @else
        <p style="font-size: 11px; color: #999; text-align: center; padding: 20px 0;">Belum ada data kehadiran untuk periode ini.</p>
    @endif

    <div class="ttd-row">
        <div class="ttd-col">
            <p>Orang Tua / Wali</p>
            <div class="ttd-line"></div>
            <p>{{ $student->user?->name ?? '.........................' }}</p>
        </div>
        <div class="ttd-col" style="float: right;">
            <p>Wali Kelas</p>
            <div class="ttd-line"></div>
            <p>{{ $student->classRoom?->homeroomTeacher?->user?->name ?? '.........................' }}</p>
        </div>
    </div>

    @include('exports.partials.pdf-validation')
</body>
</html>
