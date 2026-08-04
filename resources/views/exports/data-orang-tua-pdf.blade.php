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
        .summary .value-danger { font-weight: bold; font-size: 13px; color: #e53e3e; }
        h3 { font-size: 13px; margin: 20px 0 6px 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th { background: #f0f0f0; text-align: left; padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        table.data tr:nth-child(even) { background: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .row-warning { background: #fff5f5 !important; }
        .text-danger { color: #e53e3e; font-weight: bold; }
        .aging-badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .aging-low { background: #fef3c7; color: #92400e; }
        .aging-mid { background: #fed7aa; color: #9a3412; }
        .aging-high { background: #fecaca; color: #991b1b; }
        .insight-box { margin-top: 16px; padding: 10px 12px; background: #fffbeb; border-left: 3px solid #f59e0b; font-size: 10px; color: #92400e; }
        .insight-box strong { color: #78350f; }
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
        @if($unpaidInvoiceParents->count() > 0)
        <tr>
            <td class="label">Orang Tua dengan Tunggakan SPP</td>
            <td class="value-danger">{{ $unpaidInvoiceParents->count() }}</td>
        </tr>
        <tr>
            <td class="label">Total Tunggakan</td>
            <td class="value-danger">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</td>
        </tr>
        @endif
    </table>

    <h3>Daftar Orang Tua Aktif</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Orang Tua</th>
                <th>Email</th>
                <th class="text-center">Jumlah Anak</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activeParents as $i => $parent)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $parent->name }}</td>
                    <td>{{ $parent->email }}</td>
                    <td class="text-center">{{ $parent->students_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($unpaidInvoiceParents->count() > 0)
        <h3>Orang Tua dengan Tunggakan SPP</h3>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Orang Tua</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th class="text-center">Tagihan</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-center">Aging</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($unpaidInvoiceParents as $data)
                    @php
                        $agingClass = match(true) {
                            $data['aging_months'] > 3 => 'aging-high',
                            $data['aging_months'] > 1 => 'aging-mid',
                            default => 'aging-low',
                        };
                    @endphp
                    @foreach($data['students'] as $student)
                        <tr class="{{ $data['aging_months'] > 3 ? 'row-warning' : '' }}">
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $data['parent']?->name ?? '-' }}</td>
                            <td>{{ $student->nama_siswa }}</td>
                            <td>{{ $student->classRoom?->nama_kelas ?? '-' }}</td>
                            <td class="text-center">{{ $student->sppInvoices->count() }} bln</td>
                            <td class="text-right">Rp {{ number_format($student->sppInvoices->sum('jumlah'), 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="aging-badge {{ $agingClass }}">{{ $data['aging_months'] }} bln</span>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        @php $highAging = $unpaidInvoiceParents->filter(fn($d) => $d['aging_months'] > 3)->count(); @endphp
        @if($highAging > 0)
            <div class="insight-box">
                <strong>Perhatian:</strong> {{ $highAging }} orang tua memiliki tunggakan lebih dari 3 bulan (ditandai merah).
                Perlu tindak lanjut segera berupa surat peringatan atau komunikasi langsung.
            </div>
        @endif
    @endif

    @include('exports.partials.pdf-validation')
</body>
</html>
