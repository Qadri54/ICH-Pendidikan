<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Raport</title>
    @include('exports.partials.pdf-header-footer', ['reportTitle' => 'Laporan Rekap Raport — ' . $periodLabel])
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
        tfoot td { font-weight: bold; background: #f0f0f0; }
        .kpi-row { margin-bottom: 16px; }
        .kpi-box { display: inline-block; width: 18%; vertical-align: top; padding: 8px 4px; border: 1px solid #e5e7eb; border-radius: 6px; text-align: center; margin-right: 1%; }
        .kpi-box .kpi-value { font-size: 18px; font-weight: bold; color: #3DA746; }
        .kpi-box .kpi-label { font-size: 8px; color: #666; margin-top: 2px; }
        .kpi-box .kpi-desc { font-size: 7px; color: #999; margin-top: 1px; }
        .kpi-box.warning .kpi-value { color: #f59e0b; }
        .kpi-box.danger .kpi-value { color: #e53e3e; }
        .kpi-box.info .kpi-value { color: #3b82f6; }
        .bar-container { margin: 10px 0; }
        .bar-row { margin-bottom: 4px; }
        .bar-label { display: inline-block; width: 70px; font-size: 9px; text-align: right; padding-right: 6px; vertical-align: middle; }
        .bar-track { display: inline-block; width: 280px; height: 14px; background: #f0f0f0; vertical-align: middle; }
        .bar-fill { display: inline-block; height: 14px; min-width: 1px; vertical-align: top; }
        .bar-fill-draft { background: #d1d5db; }
        .bar-fill-submitted { background: #f59e0b; }
        .bar-fill-approved { background: #3DA746; }
        .bar-value { display: inline-block; font-size: 8px; padding-left: 6px; vertical-align: middle; }
        .insight-box { margin-top: 16px; padding: 10px 12px; background: #fffbeb; border-left: 3px solid #f59e0b; font-size: 10px; color: #92400e; }
        .insight-box strong { color: #78350f; }
        .status-badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: bold; }
        .badge-draft { background: #f3f4f6; color: #6b7280; }
        .badge-submitted { background: #fef3c7; color: #d97706; }
        .badge-approved { background: #d1fae5; color: #059669; }
    </style>
</head>
<body>
    <h3>Ringkasan Raport — {{ $periodLabel }}</h3>

    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-value">{{ $totalRaport }}</div>
            <div class="kpi-label">Total Raport</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $coverageRate }}%</div>
            <div class="kpi-label">Cakupan</div>
            <div class="kpi-desc">{{ $totalRaport }} dari {{ $siswaAktif }} siswa</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $completionRate }}%</div>
            <div class="kpi-label">Disetujui</div>
            <div class="kpi-desc">{{ $totalApproved }} dari {{ $totalRaport }} raport</div>
        </div>
        <div class="kpi-box warning">
            <div class="kpi-value">{{ $totalSubmitted }}</div>
            <div class="kpi-label">Menunggu</div>
            <div class="kpi-desc">Perlu persetujuan</div>
        </div>
        <div class="kpi-box danger">
            <div class="kpi-value">{{ $belumDibuatkan }}</div>
            <div class="kpi-label">Belum Dibuat</div>
            <div class="kpi-desc">Siswa tanpa raport</div>
        </div>
    </div>

    <h3>Progres Raport per Kelas</h3>
    @php $maxTotal = $perKelas->max('total') ?: 1; @endphp
    <div class="bar-container">
        @foreach($perKelas as $k)
            @php
                $wDraft     = $maxTotal > 0 ? round($k['draft'] / $maxTotal * 100) : 0;
                $wSubmitted = $maxTotal > 0 ? round($k['submitted'] / $maxTotal * 100) : 0;
                $wApproved  = $maxTotal > 0 ? round($k['approved'] / $maxTotal * 100) : 0;
            @endphp
            <div class="bar-row">
                <span class="bar-label">{{ $k['kelas'] }}</span>
                <span class="bar-track">
                    <span class="bar-fill bar-fill-approved" style="width: {{ $wApproved }}%;"></span><span class="bar-fill bar-fill-submitted" style="width: {{ $wSubmitted }}%;"></span><span class="bar-fill bar-fill-draft" style="width: {{ $wDraft }}%;"></span>
                </span>
                <span class="bar-value">{{ $k['total'] }} ({{ $k['approved'] }}✓ / {{ $k['submitted'] }}⏳ / {{ $k['draft'] }}✎)</span>
            </div>
        @endforeach
    </div>
    <div style="font-size: 8px; color: #666; margin-top: 4px;">
        <span style="display: inline-block; width: 10px; height: 10px; background: #3DA746; vertical-align: middle;"></span> Disetujui &nbsp;
        <span style="display: inline-block; width: 10px; height: 10px; background: #f59e0b; vertical-align: middle;"></span> Menunggu &nbsp;
        <span style="display: inline-block; width: 10px; height: 10px; background: #d1d5db; vertical-align: middle;"></span> Draft
    </div>

    <h3>Detail Raport per Kelas</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Kelas</th>
                <th class="text-center">Draft</th>
                <th class="text-center">Menunggu</th>
                <th class="text-center">Disetujui</th>
                <th class="text-center">Total</th>
                <th class="text-center">% Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perKelas as $i => $k)
                @php $pct = $k['total'] > 0 ? round($k['approved'] / $k['total'] * 100) : 0; @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $k['kelas'] }}</td>
                    <td class="text-center">{{ $k['draft'] }}</td>
                    <td class="text-center">{{ $k['submitted'] }}</td>
                    <td class="text-center">{{ $k['approved'] }}</td>
                    <td class="text-center"><strong>{{ $k['total'] }}</strong></td>
                    <td class="text-center" style="color: {{ $pct >= 80 ? '#3DA746' : ($pct >= 50 ? '#f59e0b' : '#e53e3e') }}; font-weight: bold;">{{ $pct }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total</td>
                <td class="text-center">{{ $totalDraft }}</td>
                <td class="text-center">{{ $totalSubmitted }}</td>
                <td class="text-center">{{ $totalApproved }}</td>
                <td class="text-center">{{ $totalRaport }}</td>
                <td class="text-center">{{ $completionRate }}%</td>
            </tr>
        </tfoot>
    </table>

    @if($belumDibuatkanList->isNotEmpty())
        <h3>Daftar Siswa Belum Dibuatkan Raport</h3>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($belumDibuatkanList as $i => $s)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $s->NIS ?? '-' }}</td>
                        <td>{{ $s->nama_siswa }}</td>
                        <td>{{ $s->classRoom?->nama_kelas ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($belumDibuatkan > 0 || $totalSubmitted > 0)
        <div class="insight-box">
            <strong>Catatan:</strong>
            @if($belumDibuatkan > 0)
                {{ $belumDibuatkan }} siswa aktif belum dibuatkan raport untuk periode ini.
            @endif
            @if($totalSubmitted > 0)
                {{ $totalSubmitted }} raport masih menunggu persetujuan.
            @endif
        </div>
    @endif

    @include('exports.partials.pdf-validation')
</body>
</html>
