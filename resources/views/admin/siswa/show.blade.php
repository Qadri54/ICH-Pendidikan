@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Detail Siswa — {{ $siswa->nama_siswa }}">

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.siswa.index') }}"
               class="text-ich-teal text-sm font-ui font-semibold hover:underline">&larr; Kembali</a>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Detail Siswa</h1>
        </div>
        @php
            $statusColor = match($siswa->status) {
                'aktif'  => 'bg-ich-success-soft text-ich-success',
                'alumni' => 'bg-ich-info-soft text-ich-teal',
                'keluar' => 'bg-ich-error-soft text-ich-error',
                default  => 'bg-gray-100 text-gray-600',
            };
        @endphp
        <span class="px-3 py-1.5 rounded-full text-xs font-ui font-bold {{ $statusColor }}">
            {{ ucfirst($siswa->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Utama (2/3) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Biodata Siswa --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Biodata Siswa</h2>
                @php
                    $usia = $siswa->tanggal_lahir ? $siswa->tanggal_lahir->age . ' tahun' : '-';
                @endphp
                @foreach([
                    ['NIS',            $siswa->NIS ?? '-'],
                    ['Nama Lengkap',   $siswa->nama_siswa],
                    ['Jenis Kelamin',  $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'],
                    ['Tempat Lahir',   $siswa->tempat_lahir ?? '-'],
                    ['Tanggal Lahir',  $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d M Y') : '-'],
                    ['Usia',           $usia],
                    ['Kelas',          $siswa->classRoom?->nama_kelas ?? '-'],
                    ['Wali Kelas',     $siswa->classRoom?->homeroomTeacher?->user?->name ?? '-'],
                ] as [$label, $value])
                    <div class="flex gap-4 py-1.5 border-b border-ich-line last:border-0">
                        <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                        <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Data Orang Tua --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Data Orang Tua</h2>

                {{-- Akun Orang Tua --}}
                @if($siswa->user)
                    <h3 class="font-ui font-bold text-sm text-ich-ink-600 mb-2">Akun Orang Tua</h3>
                    @foreach([
                        ['Nama',  $siswa->user->name],
                        ['Email', $siswa->user->email],
                        ['No HP', $siswa->user->no_hp ?? '-'],
                    ] as [$label, $value])
                        <div class="flex gap-4 py-1.5 border-b border-ich-line last:border-0">
                            <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                            <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-ich-ink-300 font-sans mb-3">Belum terhubung dengan akun orang tua.</p>
                @endif

                {{-- Biodata Ayah --}}
                <h3 class="font-ui font-bold text-sm text-ich-ink-600 mt-5 mb-2">Biodata Ayah</h3>
                @if($registration)
                    @foreach([
                        ['Nama',          $registration->nama_ayah ?? $siswa->nama_ayah ?? '-'],
                        ['Tempat Lahir',  $registration->tempat_lahir_ayah ?? '-'],
                        ['Tanggal Lahir', $registration->tanggal_lahir_ayah?->format('d M Y') ?? '-'],
                        ['Alamat',        $registration->alamat_ayah ?? '-'],
                        ['Pendidikan',    $registration->pendidikan_ayah ?? '-'],
                        ['Pekerjaan',     $registration->pekerjaan_ayah ?? '-'],
                        ['No. Telp/HP',   $registration->no_telp_ayah ?? '-'],
                    ] as [$label, $value])
                        <div class="flex gap-4 py-1.5 border-b border-ich-line last:border-0">
                            <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                            <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="flex gap-4 py-1.5">
                        <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0">Nama</div>
                        <div class="font-sans text-sm text-ich-ink-900">{{ $siswa->nama_ayah ?? '-' }}</div>
                    </div>
                    <p class="text-xs text-ich-ink-300 font-sans mt-1">Data lengkap tidak tersedia (tidak ada formulir pendaftaran).</p>
                @endif

                {{-- Biodata Ibu --}}
                <h3 class="font-ui font-bold text-sm text-ich-ink-600 mt-5 mb-2">Biodata Ibu</h3>
                @if($registration)
                    @foreach([
                        ['Nama',          $registration->nama_ibu ?? $siswa->nama_ibu ?? '-'],
                        ['Tempat Lahir',  $registration->tempat_lahir_ibu ?? '-'],
                        ['Tanggal Lahir', $registration->tanggal_lahir_ibu?->format('d M Y') ?? '-'],
                        ['Alamat',        $registration->alamat_ibu ?? '-'],
                        ['Pendidikan',    $registration->pendidikan_ibu ?? '-'],
                        ['Pekerjaan',     $registration->pekerjaan_ibu ?? '-'],
                        ['No. Telp/HP',   $registration->no_telp_ibu ?? '-'],
                    ] as [$label, $value])
                        <div class="flex gap-4 py-1.5 border-b border-ich-line last:border-0">
                            <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                            <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="flex gap-4 py-1.5">
                        <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0">Nama</div>
                        <div class="font-sans text-sm text-ich-ink-900">{{ $siswa->nama_ibu ?? '-' }}</div>
                    </div>
                    <p class="text-xs text-ich-ink-300 font-sans mt-1">Data lengkap tidak tersedia (tidak ada formulir pendaftaran).</p>
                @endif
            </div>

            {{-- Data Pendaftaran (jika ada) --}}
            @if($registration)
                <div class="bg-white rounded-xl shadow-ich-card p-6">
                    <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Data Formulir Pendaftaran</h2>
                    @php
                        $jenisLabel = $registration->jenis_pendaftaran === 'TK' ? 'PG / TK ICH' : 'Magrib Mengaji';
                        $jenisBg    = $registration->jenis_pendaftaran === 'TK' ? 'bg-ich-purple-soft text-ich-purple' : 'bg-ich-warning-soft text-ich-warning';
                        $regStatusColor = match($registration->status) {
                            'accepted' => 'bg-ich-success-soft text-ich-success',
                            'rejected' => 'bg-ich-error-soft text-ich-error',
                            default    => 'bg-ich-warning-soft text-ich-warning',
                        };
                        $regStatusLabel = match($registration->status) {
                            'accepted' => 'Diterima',
                            'rejected' => 'Ditolak',
                            default    => 'Menunggu',
                        };
                    @endphp
                    @foreach([
                        ['Jenis Pendaftaran', $jenisLabel],
                        ['Status',            $regStatusLabel],
                        ['Alamat',            $registration->alamat ?? '-'],
                        ['Anak ke',           $registration->anak_ke ? 'Anak ke-' . $registration->anak_ke : '-'],
                        ['Ukuran Baju',       $registration->ukuran_baju ?? '-'],
                    ] as [$label, $value])
                        <div class="flex gap-4 py-1.5 border-b border-ich-line last:border-0">
                            <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                            <div class="font-sans text-sm text-ich-ink-900">
                                @if($label === 'Jenis Pendaftaran')
                                    <span class="px-2 py-0.5 rounded-full text-xs font-ui font-bold {{ $jenisBg }}">{{ $value }}</span>
                                @elseif($label === 'Status')
                                    <span class="px-2 py-0.5 rounded-full text-xs font-ui font-bold {{ $regStatusColor }}">{{ $value }}</span>
                                @else
                                    {{ $value }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="mt-3">
                        <a href="{{ route('admin.pendaftaran.show', $registration) }}"
                           class="text-ich-teal text-sm font-ui font-semibold hover:underline">
                            Lihat detail pendaftaran &rarr;
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar (1/3) --}}
        <div class="space-y-6">

            {{-- Aksi --}}
            @if(! $isReadOnly)
                <div class="bg-white rounded-xl shadow-ich-card p-6 space-y-3">
                    <a href="{{ route('admin.siswa.edit', $siswa) }}"
                       class="block w-full py-2.5 bg-ich-yellow text-white text-center font-ui font-bold text-sm
                              rounded-ich-lg shadow-ich-btn hover:bg-ich-yellow-dark transition-colors">
                        Edit Data Siswa
                    </a>
                </div>
            @endif

            {{-- Ringkasan Kehadiran --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <h2 class="font-ui font-bold text-ich-ink-900 mb-4">Kehadiran</h2>
                @php
                    $hadir = $attendance['Hadir'] ?? 0;
                    $sakit = $attendance['Sakit'] ?? 0;
                    $izin  = $attendance['Izin'] ?? 0;
                    $alpha = $attendance['Alpha'] ?? 0;
                    $totalAbsensi = $hadir + $sakit + $izin + $alpha;
                    $pctHadir = $totalAbsensi > 0 ? round($hadir / $totalAbsensi * 100) : 0;
                @endphp
                @if($totalAbsensi > 0)
                    <div class="text-center mb-4">
                        <div class="text-3xl font-bold {{ $pctHadir >= 75 ? 'text-ich-success' : 'text-ich-error' }}">{{ $pctHadir }}%</div>
                        <div class="text-xs text-ich-ink-400 font-ui">Tingkat Kehadiran</div>
                    </div>
                    <div class="space-y-2">
                        @foreach([
                            ['Hadir', $hadir, 'bg-ich-success'],
                            ['Sakit', $sakit, 'bg-ich-warning'],
                            ['Izin',  $izin,  'bg-ich-teal'],
                            ['Alpha', $alpha, 'bg-ich-error'],
                        ] as [$lbl, $val, $color])
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $color }}"></span>
                                    <span class="font-ui text-ich-ink-600">{{ $lbl }}</span>
                                </div>
                                <span class="font-bold text-ich-ink-900">{{ $val }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 pt-3 border-t border-ich-line text-xs text-ich-ink-400 font-sans">
                        Total {{ $totalAbsensi }} hari tercatat
                    </div>
                @else
                    <p class="text-sm text-ich-ink-300 font-sans text-center">Belum ada data kehadiran.</p>
                @endif
            </div>

            {{-- Ringkasan SPP --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <h2 class="font-ui font-bold text-ich-ink-900 mb-4">SPP</h2>
                @php
                    $sppPaid     = $sppSummary->get('paid');
                    $sppUnpaid   = $sppSummary->get('unpaid');
                    $sppOverdue  = $sppSummary->get('overdue');
                    $sppPending  = $sppSummary->get('pending');
                    $totalTagihan = $sppSummary->sum('jumlah');
                    $totalBayar   = ($sppPaid->jumlah ?? 0);
                @endphp
                @if($sppSummary->count() > 0)
                    <div class="space-y-2">
                        @foreach([
                            ['Lunas',    $sppPaid->total ?? 0,    'text-ich-success'],
                            ['Belum Bayar', ($sppUnpaid->total ?? 0), 'text-ich-warning'],
                            ['Terlambat', $sppOverdue->total ?? 0, 'text-ich-error'],
                            ['Menunggu',  $sppPending->total ?? 0, 'text-ich-teal'],
                        ] as [$lbl, $val, $color])
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-ui text-ich-ink-600">{{ $lbl }}</span>
                                <span class="font-bold {{ $color }}">{{ $val }} tagihan</span>
                            </div>
                        @endforeach
                    </div>
                    @php $tunggakan = ($sppUnpaid->jumlah ?? 0) + ($sppOverdue->jumlah ?? 0); @endphp
                    @if($tunggakan > 0)
                        <div class="mt-3 pt-3 border-t border-ich-line">
                            <div class="text-xs text-ich-ink-400 font-ui">Total Tunggakan</div>
                            <div class="text-lg font-bold text-ich-error">Rp {{ number_format($tunggakan, 0, ',', '.') }}</div>
                        </div>
                    @endif
                @else
                    <p class="text-sm text-ich-ink-300 font-sans text-center">Belum ada tagihan SPP.</p>
                @endif
            </div>

            {{-- Tabungan --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <h2 class="font-ui font-bold text-ich-ink-900 mb-4">Tabungan</h2>
                @if($siswa->passbooks->count() > 0)
                    <div class="text-center">
                        <div class="text-2xl font-bold text-ich-success">Rp {{ number_format($tabunganSaldo, 0, ',', '.') }}</div>
                        <div class="text-xs text-ich-ink-400 font-ui mt-1">Saldo Total ({{ $siswa->passbooks->count() }} buku)</div>
                    </div>
                @else
                    <p class="text-sm text-ich-ink-300 font-sans text-center">Belum memiliki buku tabungan.</p>
                @endif
            </div>

            {{-- Raport --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <h2 class="font-ui font-bold text-ich-ink-900 mb-4">Raport</h2>
                @if($siswa->reportCards->count() > 0)
                    <div class="space-y-2">
                        @foreach($siswa->reportCards->sortByDesc('created_at') as $rc)
                            @php
                                $rcColor = match($rc->status) {
                                    'approved'  => 'bg-ich-success-soft text-ich-success',
                                    'submitted' => 'bg-ich-info-soft text-ich-teal',
                                    default     => 'bg-ich-warning-soft text-ich-warning',
                                };
                                $rcLabel = match($rc->status) {
                                    'approved'  => 'Disetujui',
                                    'submitted' => 'Diajukan',
                                    default     => 'Draft',
                                };
                            @endphp
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-ui text-ich-ink-600">{{ $rc->period?->nama_periode ?? 'Periode -' }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-ui font-bold {{ $rcColor }}">{{ $rcLabel }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-ich-ink-300 font-sans text-center">Belum ada raport.</p>
                @endif
            </div>
        </div>

    </div>

</x-main-layout>
