@php
    $student   = $raport->student;
    $period    = $raport->period;
    $kelas     = $raport->classRoom;
    $waliKelas = $raport->homeroomTeacher?->user;
    $pm        = $raport->physicalMeasurement;
    $hc        = $raport->healthCondition;
    $intra     = $raport->narrativeAssessments->where('kategori', 'intrakurikuler');
    $koku      = $raport->narrativeAssessments->where('kategori', 'kokurikuler');

    $bgCover   = public_path('images/raport/image_bg1.png');
    $bgContent = public_path('images/raport/image_bg2.png');
    $garuda    = public_path('images/raport/burung_garuda.png');

    $kelasNama = $kelas?->nama_kelas ?? '-';
    $fase = str_contains(strtoupper($kelasNama), 'A') ? 'TK A' : 'TK B';

    $kepalaSekolah = $kepalaSekolah ?? 'Adli Qarin, S.S. M.Ikom';

    $grouped = $raport->checklistAssessments
        ->sortBy(fn($cl) => $cl->category?->urutan ?? 999)
        ->groupBy(fn($cl) => $cl->category?->parent?->nama ?? $cl->category?->nama ?? 'Lainnya');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @page {
        size: A4 portrait;
        margin: 0;
    }
    * { margin: 0; padding: 0; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #1a1a1a; }

    .page {
        position: relative;
        padding: 25mm 20mm 20mm 22mm;
        page-break-after: always;
    }
    .page:last-child { page-break-after: auto; }

    .page-bg {
        position: absolute;
        top: 0; left: 0;
        width: 210mm; height: 297mm;
        z-index: -1;
    }

    /* ─── COVER ─── */
    .cover-page {
        text-align: center;
        padding-top: 35mm;
    }
    .cover-page .garuda {
        width: 55px;
        margin-bottom: 10px;
        opacity: 0.35;
    }
    .cover-title {
        font-size: 12pt;
        font-weight: bold;
        line-height: 1.6;
        margin-bottom: 18px;
    }
    .cover-school {
        font-size: 13pt;
        font-weight: bold;
        color: #009966;
        margin-bottom: 4px;
    }
    .cover-address {
        font-size: 9pt;
        color: #555;
        margin-bottom: 30px;
    }
    .cover-student-box .label {
        font-size: 9pt;
        color: #555;
        padding-bottom: 4px;
    }
    .cover-student-box .value {
        font-size: 14pt;
        font-weight: bold;
        border-bottom: 2px dotted #999;
        padding-bottom: 4px;
    }

    /* ─── TABLES ─── */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    .info-table td {
        padding: 3px 6px;
        font-size: 9.5pt;
        vertical-align: top;
    }
    .info-table td:first-child { width: 160px; font-weight: bold; }
    .info-table td:nth-child(2) { width: 12px; }

    .identity-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    .identity-table td {
        padding: 4px 6px;
        font-size: 9.5pt;
        vertical-align: top;
    }
    .identity-table .num { width: 25px; font-weight: bold; }
    .identity-table .label-col { width: 160px; font-weight: bold; text-transform: uppercase; }
    .identity-table .sep { width: 12px; }

    /* ─── SECTION HEADERS ─── */
    .section-header {
        background: #009966;
        color: white;
        font-weight: bold;
        font-size: 10pt;
        padding: 5px 10px;
        margin-bottom: 8px;
        margin-top: 14px;
        text-transform: uppercase;
    }
    .sub-header {
        font-weight: bold;
        font-size: 10pt;
        color: #009966;
        margin-bottom: 6px;
        margin-top: 12px;
        text-transform: uppercase;
    }

    /* ─── NARASI ─── */
    .narasi-block {
        margin-bottom: 12px;
    }
    .narasi-label {
        font-weight: bold;
        font-size: 9.5pt;
        color: #009966;
        margin-bottom: 4px;
        text-transform: uppercase;
    }
    .narasi-text {
        font-size: 9pt;
        color: #333;
        line-height: 1.65;
        text-align: justify;
    }
    .foto-label {
        font-weight: bold;
        font-size: 8.5pt;
        color: #666;
        margin-top: 8px;
        margin-bottom: 4px;
    }
    .foto-grid {
        width: 100%;
        border-collapse: collapse;
    }
    .foto-grid td {
        padding: 3px;
        text-align: center;
        vertical-align: top;
    }
    .foto-grid img {
        max-width: 130px;
        max-height: 100px;
        border: 1px solid #ddd;
    }
    .foto-caption {
        font-size: 7pt;
        color: #888;
        margin-top: 2px;
    }

    /* ─── CHECKLIST ─── */
    .checklist-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        font-size: 9pt;
    }
    .checklist-table th {
        background: #009966;
        color: white;
        padding: 5px 6px;
        text-align: center;
        border: 1px solid #007a52;
        font-size: 8.5pt;
        font-weight: bold;
    }
    .checklist-table th:first-child { text-align: left; }
    .checklist-table td {
        padding: 4px 6px;
        border: 1px solid #ccc;
        vertical-align: middle;
    }
    .checklist-table tr:nth-child(even) td { background: #f8fdf8; }
    .group-header td {
        background: #e8f5ea !important;
        font-weight: bold;
        font-size: 9pt;
        color: #006644;
    }
    .check-mark { text-align: center; font-size: 12pt; color: #009966; }

    /* ─── FISIK & KESEHATAN ─── */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        font-size: 9pt;
    }
    .data-table th {
        background: #009966;
        color: white;
        padding: 5px 8px;
        text-align: center;
        border: 1px solid #007a52;
        font-size: 8.5pt;
    }
    .data-table th:first-child, .data-table th:nth-child(2) { text-align: left; }
    .data-table td {
        padding: 4px 8px;
        border: 1px solid #ccc;
        text-align: center;
    }
    .data-table td:first-child, .data-table td:nth-child(2) { text-align: left; }

    /* ─── REFLEKSI ─── */
    .refleksi-box {
        border: 1px solid #ccc;
        padding: 10px 12px 40px 12px;
        margin-bottom: 8px;
    }
    .refleksi-box p {
        font-size: 9pt;
        color: #555;
    }

    /* ─── TANDA TANGAN ─── */
    .sign-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .sign-table td {
        text-align: center;
        vertical-align: top;
        padding: 5px 10px;
        font-size: 9pt;
    }
    .sign-name {
        border-top: 1px solid #333;
        padding-top: 4px;
        font-weight: bold;
        font-size: 9pt;
        width: 160px;
        margin-left: auto;
        margin-right: auto;
    }

    /* ─── PETUNJUK ─── */
    .petunjuk ol {
        font-size: 9pt;
        color: #333;
        line-height: 1.7;
        padding-left: 20px;
    }
    .petunjuk li {
        margin-bottom: 6px;
    }

    .page-title {
        text-align: center;
        font-size: 12pt;
        font-weight: bold;
        margin-bottom: 14px;
        text-transform: uppercase;
    }

    .laporan-header {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        border: 1px solid #ccc;
    }
    .laporan-header td {
        padding: 4px 8px;
        font-size: 9.5pt;
        border: 1px solid #ccc;
    }
    .laporan-header td:first-child { width: 110px; font-weight: bold; }
    .laporan-header td:nth-child(2) { width: 12px; }
</style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════
     HALAMAN 1: COVER
     ═══════════════════════════════════════════════════════ --}}
<div class="page cover-page">
    <img src="{{ $bgCover }}" class="page-bg">

    <div style="margin-top: 20px;">
        <img src="{{ $garuda }}" class="garuda">
    </div>

    <div class="cover-title">
        LAPORAN<br>
        CAPAIAN PERKEMBANGAN ANAK DIDIK<br>
        TAMAN KANAK-KANAK
    </div>

    <div class="cover-school">TK ICH</div>
    <div class="cover-school" style="font-size:11pt;">IQRA' CREATIVE HOUSE</div>
    <div class="cover-address">Jl. Datuk Kabu Gang Ridho No.11 E, Tembung</div>

    <table class="cover-student-box" style="margin: 20px auto; width: 70%; border-collapse: collapse;">
        <tr><td class="label">Nama Anak Didik</td></tr>
        <tr><td class="value">{{ strtoupper($student->nama_siswa) }}</td></tr>
        <tr><td class="label" style="padding-top: 16px;">Nomor Induk Siswa</td></tr>
        <tr><td class="value">{{ $student->NIS ?? '-' }}</td></tr>
    </table>
</div>

{{-- ═══════════════════════════════════════════════════════
     HALAMAN 2: IDENTITAS SEKOLAH + KETERANGAN DIRI ANAK
     ═══════════════════════════════════════════════════════ --}}
<div class="page">
    <img src="{{ $bgContent }}" class="page-bg">

    <div class="page-title">
        LAPORAN<br>
        PERTUMBUHAN DAN PERKEMBANGAN ANAK DIDIK<br>
        TAMAN KANAK-KANAK IQRA' CREATIVE HOUSE
    </div>

    <table class="info-table" style="margin-bottom:16px;">
        <tr><td>NAMA TK</td><td>:</td><td>IQRA' CREATIVE HOUSE</td></tr>
        <tr><td>ALAMAT TK</td><td>:</td><td>JALAN DATUK KABU GANG RIDHO NO.11 E</td></tr>
        <tr><td>DESA/KELURAHAN</td><td>:</td><td>TEMBUNG</td></tr>
        <tr><td>KECAMATAN</td><td>:</td><td>PERCUT SEI TUAN</td></tr>
        <tr><td>KABUPATEN</td><td>:</td><td>DELI SERDANG</td></tr>
    </table>

    <div class="section-header">Keterangan Diri Anak</div>

    <table class="identity-table">
        <tr>
            <td class="num">1.</td>
            <td class="label-col">Nama</td>
            <td class="sep">:</td>
            <td>{{ $student->nama_siswa }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="label-col" style="font-weight:normal; text-transform:none; padding-left:12px;">Nama Panggilan</td>
            <td class="sep">:</td>
            <td>{{ explode(' ', $student->nama_siswa)[0] }}</td>
        </tr>
        <tr>
            <td class="num">2.</td>
            <td class="label-col">Nomor Induk Siswa</td>
            <td class="sep">:</td>
            <td>{{ $student->NIS ?? '-' }}</td>
        </tr>
        <tr>
            <td class="num">3.</td>
            <td class="label-col">Tempat Tgl Lahir</td>
            <td class="sep">:</td>
            <td>{{ $student->tempat_lahir ? $student->tempat_lahir . ', ' : '' }}{{ $student->tanggal_lahir?->translatedFormat('d F Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="num">4.</td>
            <td class="label-col">Jenis Kelamin</td>
            <td class="sep">:</td>
            <td>{{ $student->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td class="num">5.</td>
            <td class="label-col">Agama</td>
            <td class="sep">:</td>
            <td>Islam</td>
        </tr>
        <tr>
            <td class="num">6.</td>
            <td class="label-col">Orang Tua / Wali</td>
            <td class="sep">:</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td style="padding-left:12px;">Nama Ayah</td>
            <td class="sep">:</td>
            <td>{{ $student->nama_ayah ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="padding-left:12px;">Nama Ibu</td>
            <td class="sep">:</td>
            <td>{{ $student->nama_ibu ?? '-' }}</td>
        </tr>
    </table>

    <table class="sign-table" style="margin-top:30px;">
        <tr>
            <td style="width:60%;"></td>
            <td>
                Medan, {{ $raport->approved_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
                <br><br>Kepala Sekolah<br>TK Iqra' Creative House
                <br><br><br><br>
                <div class="sign-name">{{ $kepalaSekolah }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ═══════════════════════════════════════════════════════
     HALAMAN 3: PETUNJUK PENILAIAN
     ═══════════════════════════════════════════════════════ --}}
<div class="page">
    <img src="{{ $bgContent }}" class="page-bg">

    <div class="section-header">Petunjuk Penilaian</div>

    <div class="petunjuk">
        <ol>
            <li>Buku laporan perkembangan ini digunakan selama anak didik mengikuti program Pendidikan di sekolah Iqra' Creative House.</li>
            <li>Untuk melaporkan capaian Pendidikan anak digunakan sistem penilaian kualitatif untuk setiap kemampuan dengan menggunakan tiga kategori, yaitu <strong>Belum Muncul (BM)</strong>, <strong>Mulai Muncul (MM)</strong>, <strong>Sudah Muncul (SM)</strong>.</li>
            <li>Untuk melaporkan perkembangan dalam bidang Muatan Kearifan Lokal menggunakan sistem penilaian kualitatif untuk setiap kemampuan dengan menggunakan tiga kategori, yaitu Belum Muncul (BM), Mulai Muncul (MM), Sudah Muncul (SM).</li>
            <li>Buku laporan perkembangan ini disesuaikan dengan Kurikulum Merdeka yang dilengkapi dengan catatan guru yang secara diskriptif menjelaskan perkembangan siswa dalam aspek Nilai Agama dan Budi Pekerti, Jati Diri, Literasi dan STEAM, Projek Penguatan Profil Pelajar Pancasila.</li>
            <li>Sumber penilaian terhadap anak didik adalah pengamatan sehari-hari didalam kelas yang direkam didalam catatan harian siswa (Catatan Anekdot, Hasil Karya, Foto Berseri, dan Ceklis).</li>
        </ol>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     HALAMAN 4+: LAPORAN CAPAIAN PEMBELAJARAN
     ═══════════════════════════════════════════════════════ --}}
<div class="page">
    <img src="{{ $bgContent }}" class="page-bg">

    <div class="page-title">Laporan Capaian Pembelajaran</div>

    <table class="laporan-header">
        <tr>
            <td>Nama Sekolah</td>
            <td>:</td>
            <td>Iqra' Creative House</td>
            <td style="font-weight:bold; width:50px;">Kelas</td>
            <td style="width:10px;">:</td>
            <td>{{ $kelasNama }}</td>
        </tr>
        <tr>
            <td>Nama Siswa</td>
            <td>:</td>
            <td>{{ $student->nama_siswa }}</td>
            <td style="font-weight:bold;">Fase</td>
            <td>:</td>
            <td>{{ $fase }}</td>
        </tr>
        <tr>
            <td>Semester / TA</td>
            <td>:</td>
            <td>{{ $period->semester }} / {{ $period->tahun_ajaran }}</td>
            <td style="font-weight:bold;">TB</td>
            <td>:</td>
            <td>{{ $pm?->tinggi_badan ? $pm->tinggi_badan . ' cm' : '' }}</td>
        </tr>
        <tr>
            <td>Guru Kelas</td>
            <td>:</td>
            <td>{{ $waliKelas?->name ?? '-' }}</td>
            <td style="font-weight:bold;">BB</td>
            <td>:</td>
            <td>{{ $pm?->berat_badan ? $pm->berat_badan . ' kg' : '' }}</td>
        </tr>
    </table>

    {{-- A. INTRAKURIKULER --}}
    <div class="sub-header">A. Intrakurikuler</div>

    @if($intra->isNotEmpty())
        @foreach($intra as $n)
            <div class="narasi-block">
                <div class="narasi-label">{{ $n->judul }}</div>
                <div class="narasi-text">{!! nl2br(e($n->isi_naratif ?: '-')) !!}</div>

                @if($n->photos->isNotEmpty())
                    <div class="foto-label">FOTO KEGIATAN:</div>
                    <table class="foto-grid">
                        <tr>
                        @foreach($n->photos as $idx => $photo)
                            <td>
                                <img src="{{ storage_path('app/public/' . $photo->photo_path) }}">
                                @if($photo->caption)
                                    <div class="foto-caption">{{ $photo->caption }}</div>
                                @endif
                            </td>
                            @if(($idx + 1) % 3 === 0 && !$loop->last)
                                </tr><tr>
                            @endif
                        @endforeach
                        </tr>
                    </table>
                @else
                    <div class="foto-label">FOTO KEGIATAN: -</div>
                @endif
            </div>
        @endforeach
    @else
        <p style="font-size:9pt; color:#888;">Belum ada penilaian intrakurikuler.</p>
    @endif
</div>

{{-- Halaman Kokurikuler --}}
<div class="page">
    <img src="{{ $bgContent }}" class="page-bg">

    <div class="sub-header">B. Kokurikuler</div>

    @if($koku->isNotEmpty())
        @foreach($koku as $n)
            <div class="narasi-block">
                <div class="narasi-label">{{ $n->judul }}</div>
                <div class="narasi-text">{!! nl2br(e($n->isi_naratif ?: '-')) !!}</div>

                @if($n->photos->isNotEmpty())
                    <div class="foto-label">FOTO KEGIATAN:</div>
                    <table class="foto-grid">
                        <tr>
                        @foreach($n->photos as $idx => $photo)
                            <td>
                                <img src="{{ storage_path('app/public/' . $photo->photo_path) }}">
                                @if($photo->caption)
                                    <div class="foto-caption">{{ $photo->caption }}</div>
                                @endif
                            </td>
                            @if(($idx + 1) % 3 === 0 && !$loop->last)
                                </tr><tr>
                            @endif
                        @endforeach
                        </tr>
                    </table>
                @else
                    <div class="foto-label">FOTO KEGIATAN: -</div>
                @endif
            </div>
        @endforeach
    @else
        <p style="font-size:9pt; color:#888;">Belum ada penilaian kokurikuler.</p>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════
     HALAMAN: CEKLIS PERKEMBANGAN
     ═══════════════════════════════════════════════════════ --}}
@if($raport->checklistAssessments->isNotEmpty())
<div class="page">
    <img src="{{ $bgContent }}" class="page-bg">

    <div class="page-title" style="font-size:10pt; margin-bottom:10px;">
        Ceklis Perkembangan Anak Sesuai Kemampuan<br>
        <span style="font-size:9pt; font-weight:normal;">Kelompok Usia 5-6 Tahun</span>
    </div>

    <table class="checklist-table">
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:55%; text-align:left;">Perkembangan</th>
                <th style="width:13%;">BM</th>
                <th style="width:13%;">MM</th>
                <th style="width:14%;">SM</th>
            </tr>
        </thead>
        <tbody>
            @php $groupNum = 1; @endphp
            @foreach($grouped as $groupName => $items)
                <tr class="group-header">
                    <td>{{ $groupNum }}.</td>
                    <td colspan="4">{{ strtoupper($groupName) }}</td>
                </tr>
                @php $subNum = 'a'; @endphp
                @foreach($items as $cl)
                    <tr>
                        <td></td>
                        <td style="padding-left:20px;">{{ $subNum }}. {{ $cl->category?->nama ?? '-' }}</td>
                        <td class="check-mark">@if($cl->status === 'BM') ✓ @endif</td>
                        <td class="check-mark">@if($cl->status === 'MM') ✓ @endif</td>
                        <td class="check-mark">@if($cl->status === 'SM') ✓ @endif</td>
                    </tr>
                    @php $subNum++; @endphp
                @endforeach
                @php $groupNum++; @endphp
            @endforeach
        </tbody>
    </table>

    <p style="font-size:8pt; color:#666; margin-top:4px;">
        <strong>Keterangan:</strong> BM = Belum Muncul &nbsp;·&nbsp; MM = Mulai Muncul &nbsp;·&nbsp; SM = Sudah Muncul
    </p>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════
     HALAMAN: FISIK, KEHADIRAN, KESEHATAN
     ═══════════════════════════════════════════════════════ --}}
<div class="page">
    <img src="{{ $bgContent }}" class="page-bg">

    {{-- C. TINGGI DAN BERAT BADAN --}}
    <div class="section-header">C. Tinggi dan Berat Badan</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:8%;">No.</th>
                <th style="width:42%;">Aspek yang Dinilai</th>
                <th style="width:25%;">Semester 1</th>
                <th style="width:25%;">Semester 2</th>
            </tr>
        </thead>
        <tbody>
            @php
                $isSem1 = $period->semester == 1;
            @endphp
            <tr>
                <td>1.</td>
                <td>Tinggi Badan</td>
                <td>{{ $isSem1 ? ($pm?->tinggi_badan ? $pm->tinggi_badan . ' cm' : '-') : '-' }}</td>
                <td>{{ !$isSem1 ? ($pm?->tinggi_badan ? $pm->tinggi_badan . ' cm' : '-') : '-' }}</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>Berat Badan</td>
                <td>{{ $isSem1 ? ($pm?->berat_badan ? $pm->berat_badan . ' kg' : '-') : '-' }}</td>
                <td>{{ !$isSem1 ? ($pm?->berat_badan ? $pm->berat_badan . ' kg' : '-') : '-' }}</td>
            </tr>
            <tr>
                <td>3.</td>
                <td>Lingkar Kepala</td>
                <td>{{ $isSem1 ? ($pm?->lingkar_kepala ? $pm->lingkar_kepala . ' cm' : '-') : '-' }}</td>
                <td>{{ !$isSem1 ? ($pm?->lingkar_kepala ? $pm->lingkar_kepala . ' cm' : '-') : '-' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- D. KETIDAKHADIRAN --}}
    <div class="section-header">D. Ketidakhadiran</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align:left;">Keterangan</th>
                <th style="width:30%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sakit</td>
                <td>{{ $attendance['sakit'] ?? 0 }} hari</td>
            </tr>
            <tr>
                <td>Izin</td>
                <td>{{ $attendance['izin'] ?? 0 }} hari</td>
            </tr>
            <tr>
                <td>Tanpa Keterangan</td>
                <td>{{ $attendance['tanpa_keterangan'] ?? 0 }} hari</td>
            </tr>
        </tbody>
    </table>

    {{-- E. KONDISI KESEHATAN --}}
    <div class="section-header">E. Kondisi Kesehatan</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:8%;">No.</th>
                <th style="width:42%; text-align:left;">Aspek yang Dinilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1.</td>
                <td>Pendengaran</td>
                <td>{{ $hc?->pendengaran ?? 'Baik' }}</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>Penglihatan</td>
                <td>{{ $hc?->penglihatan ?? 'Baik' }}</td>
            </tr>
        </tbody>
    </table>
    @if($hc?->catatan_tambahan)
        <p style="font-size:8.5pt; color:#444; margin-top:6px;">Catatan: {{ $hc->catatan_tambahan }}</p>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════
     HALAMAN TERAKHIR: REFLEKSI ORANG TUA + TANDA TANGAN
     ═══════════════════════════════════════════════════════ --}}
<div class="page">
    <img src="{{ $bgContent }}" class="page-bg">

    <div class="section-header">Refleksi Orang Tua</div>

    <div class="refleksi-box">
        <p>1. Apa yang sudah berkembang yang saya amati dari anak?</p>
    </div>
    <div class="refleksi-box">
        <p>2. Apa yang perlu dikembangkan dari anak menurut yang saya amati?</p>
    </div>
    <div class="refleksi-box">
        <p>3. Langkah-langkah apa yang dapat saya lakukan?</p>
    </div>

    {{-- Tanda Tangan --}}
    <table class="sign-table" style="margin-top:30px;">
        <tr>
            <td style="width:33%;">&nbsp;</td>
            <td style="width:34%;">&nbsp;</td>
            <td style="width:33%; text-align:right; padding-bottom:5px;">
                Medan, {{ $raport->approved_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td style="width:33%;">
                <p style="margin-bottom:60px;">Orang Tua / Wali Murid</p>
                <div class="sign-name">(___________________)</div>
            </td>
            <td style="width:34%;">
                <p style="margin-bottom:60px;">Wali Kelas</p>
                <div class="sign-name">{{ $waliKelas?->name ?? '(____________________)' }}</div>
            </td>
            <td style="width:33%;">
                <p>Mengetahui,</p>
                <p style="margin-bottom:35px;">Kepala Sekolah</p>
                <div class="sign-name">{{ $kepalaSekolah }}</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
