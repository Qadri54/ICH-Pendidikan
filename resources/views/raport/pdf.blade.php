<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #1a1a1a; }
  .page { padding: 20mm 20mm 20mm 25mm; }

  /* Header Sekolah */
  .header { border-bottom: 2px solid #009966; padding-bottom: 10px; margin-bottom: 14px; display: flex; align-items: center; gap: 12px; }
  .header-text h1 { font-size: 14pt; font-weight: bold; color: #009966; }
  .header-text p  { font-size: 9pt; color: #555; margin-top: 2px; }

  /* Judul Raport */
  .title-box { text-align: center; margin-bottom: 14px; }
  .title-box h2 { font-size: 13pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
  .title-box p  { font-size: 9pt; color: #555; margin-top: 3px; }

  /* Info Siswa */
  .info-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
  .info-table td { padding: 3px 6px; font-size: 9.5pt; vertical-align: top; }
  .info-table td:first-child { width: 140px; font-weight: bold; }
  .info-table td:nth-child(2) { width: 10px; }

  /* Section Header */
  .section-title { background: #009966; color: white; font-weight: bold; font-size: 9.5pt;
                   padding: 4px 8px; margin-bottom: 6px; margin-top: 14px; }

  /* Narasi */
  .narasi-block { margin-bottom: 10px; }
  .narasi-block .narasi-label { font-weight: bold; font-size: 9pt; margin-bottom: 3px; color: #333; }
  .narasi-block .narasi-text  { font-size: 9pt; color: #444; line-height: 1.5; background: #f9f9f9;
                                 padding: 6px 8px; border-left: 3px solid #009966; }
  .narasi-block .kategori-badge { display: inline-block; font-size: 7.5pt; padding: 1px 5px;
                                   background: #e8f5ea; color: #009966; border-radius: 3px;
                                   margin-bottom: 3px; font-weight: bold; }

  /* Checklist */
  .checklist-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9pt; }
  .checklist-table th { background: #f0f0f0; padding: 4px 6px; text-align: left; border: 1px solid #ddd; font-size: 8.5pt; }
  .checklist-table td { padding: 3.5px 6px; border: 1px solid #ddd; vertical-align: top; }
  .checklist-table tr:nth-child(even) td { background: #fafafa; }
  .status-bm { color: #EF4444; font-weight: bold; }
  .status-mm { color: #E09F17; font-weight: bold; }
  .status-sm { color: #009966; font-weight: bold; }

  /* Fisik & Kesehatan */
  .two-col { display: table; width: 100%; }
  .col { display: table-cell; width: 50%; vertical-align: top; padding-right: 10px; }
  .col:last-child { padding-right: 0; }
  .data-row { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #eee; font-size: 9pt; }
  .data-row .label { color: #555; }
  .data-row .value { font-weight: bold; }

  /* Tanda Tangan */
  .sign-section { margin-top: 28px; display: table; width: 100%; }
  .sign-col { display: table-cell; width: 33%; text-align: center; vertical-align: top; }
  .sign-col p  { font-size: 9pt; margin-bottom: 40px; }
  .sign-col .sign-line { border-top: 1px solid #555; padding-top: 4px; font-size: 9pt; font-weight: bold; }
</style>
</head>
<body>
<div class="page">

  {{-- Header --}}
  <div class="header">
    <div class="header-text">
      <h1>ICH Pendidikan</h1>
      <p>Laporan Perkembangan Anak Didik</p>
    </div>
  </div>

  {{-- Judul --}}
  <div class="title-box">
    <h2>Laporan Perkembangan Anak</h2>
    <p>Tahun Ajaran {{ $raport->period->tahun_ajaran }} — Semester {{ $raport->period->semester }}</p>
  </div>

  {{-- Info Siswa --}}
  <div class="section-title">Identitas Siswa</div>
  <table class="info-table">
    <tr><td>Nama Lengkap</td><td>:</td><td>{{ $raport->student->nama_siswa }}</td></tr>
    <tr><td>NIS</td><td>:</td><td>{{ $raport->student->NIS ?? '-' }}</td></tr>
    <tr><td>Kelas</td><td>:</td><td>{{ $raport->classRoom?->nama_kelas ?? '-' }}</td></tr>
    <tr><td>Wali Kelas</td><td>:</td><td>{{ $raport->homeroomTeacher?->user?->name ?? '-' }}</td></tr>
    <tr><td>Periode</td><td>:</td><td>
      {{ \Carbon\Carbon::parse($raport->period->tanggal_mulai)->translatedFormat('d F Y') }} —
      {{ \Carbon\Carbon::parse($raport->period->tanggal_selesai)->translatedFormat('d F Y') }}
    </td></tr>
  </table>

  {{-- Narasi Intrakurikuler --}}
  @php
    $intra = $raport->narrativeAssessments->where('kategori', 'intrakurikuler');
    $koku  = $raport->narrativeAssessments->where('kategori', 'kokurikuler');
  @endphp

  @if($intra->isNotEmpty())
    <div class="section-title">Penilaian Intrakurikuler</div>
    @foreach($intra as $n)
      <div class="narasi-block">
        <div class="narasi-label">{{ $n->judul }}</div>
        <div class="narasi-text">{{ $n->isi_naratif ?: '-' }}</div>
      </div>
    @endforeach
  @endif

  @if($koku->isNotEmpty())
    <div class="section-title">Penilaian Kokurikuler (P5)</div>
    @foreach($koku as $n)
      <div class="narasi-block">
        <div class="narasi-label">{{ $n->judul }}</div>
        <div class="narasi-text">{{ $n->isi_naratif ?: '-' }}</div>
      </div>
    @endforeach
  @endif

  {{-- Checklist Perkembangan --}}
  @if($raport->checklistAssessments->isNotEmpty())
    <div class="section-title">Checklist Perkembangan</div>
    <table class="checklist-table">
      <thead>
        <tr>
          <th style="width:70%">Aspek Perkembangan</th>
          <th style="width:15%; text-align:center">Status</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        @foreach($raport->checklistAssessments as $cl)
          <tr>
            <td>{{ $cl->category?->nama ?? '-' }}</td>
            <td style="text-align:center" class="status-{{ strtolower($cl->status) }}">{{ $cl->status }}</td>
            <td>{{ $cl->catatan ?? '-' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <p style="font-size:8pt; color:#666; margin-bottom:6px;">
      BM = Belum Muncul &nbsp;·&nbsp; MM = Mulai Muncul &nbsp;·&nbsp; SM = Sudah Muncul
    </p>
  @endif

  {{-- Fisik & Kesehatan --}}
  <div class="section-title">Data Fisik dan Kesehatan</div>
  <div class="two-col">
    <div class="col">
      <p style="font-weight:bold; font-size:9pt; margin-bottom:6px;">Pengukuran Fisik</p>
      @php $pm = $raport->physicalMeasurement; @endphp
      <div class="data-row"><span class="label">Tinggi Badan</span><span class="value">{{ $pm?->tinggi_badan ? $pm->tinggi_badan . ' cm' : '-' }}</span></div>
      <div class="data-row"><span class="label">Berat Badan</span><span class="value">{{ $pm?->berat_badan ? $pm->berat_badan . ' kg' : '-' }}</span></div>
      <div class="data-row"><span class="label">Lingkar Kepala</span><span class="value">{{ $pm?->lingkar_kepala ? $pm->lingkar_kepala . ' cm' : '-' }}</span></div>
      <div class="data-row"><span class="label">Tgl Ukur</span><span class="value">{{ $pm?->tanggal_ukur ? \Carbon\Carbon::parse($pm->tanggal_ukur)->translatedFormat('d M Y') : '-' }}</span></div>
    </div>
    <div class="col">
      <p style="font-weight:bold; font-size:9pt; margin-bottom:6px;">Kondisi Kesehatan</p>
      @php $hc = $raport->healthCondition; @endphp
      <div class="data-row"><span class="label">Pendengaran</span><span class="value">{{ $hc?->pendengaran ?? '-' }}</span></div>
      <div class="data-row"><span class="label">Penglihatan</span><span class="value">{{ $hc?->penglihatan ?? '-' }}</span></div>
      @if($hc?->catatan_tambahan)
        <p style="font-size:8.5pt; margin-top:6px; color:#444;">{{ $hc->catatan_tambahan }}</p>
      @endif
    </div>
  </div>

  {{-- Tanda Tangan --}}
  <div class="sign-section">
    <div class="sign-col">
      <p>Orang Tua / Wali</p>
      <div class="sign-line">(___________________)</div>
    </div>
    <div class="sign-col">
      <p style="text-align:center">
        {{ now()->translatedFormat('d F Y') }}
      </p>
      <div class="sign-line">{{ $raport->homeroomTeacher?->user?->name ?? 'Wali Kelas' }}</div>
    </div>
    <div class="sign-col">
      <p>Kepala Sekolah</p>
      <div class="sign-line">(___________________)</div>
    </div>
  </div>

</div>
</body>
</html>
