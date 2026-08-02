@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Detail Siswa">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.siswa.index') }}"
               class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Detail Siswa</h1>
        </div>
        @if(! $isReadOnly)
            <a href="{{ route('admin.siswa.edit', $siswa) }}"
               class="px-4 py-2 bg-ich-yellow text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-yellow-dark transition-colors">
                Edit
            </a>
        @endif
    </div>

    <div class="max-w-xl bg-white rounded-xl shadow-ich-card p-6 space-y-4">
        @foreach([
            ['NIS',           $siswa->NIS],
            ['Nama Siswa',    $siswa->nama_siswa],
            ['Kelas',         $siswa->classRoom?->nama_kelas ?? '-'],
            ['Jenis Kelamin', $siswa->jenis_kelamin],
            ['Tanggal Lahir', $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d M Y') : '-'],
            ['Tempat Lahir',  $siswa->tempat_lahir],
            ['Nama Ayah',     $siswa->nama_ayah],
            ['Nama Ibu',      $siswa->nama_ibu],
        ] as [$label, $value])
            <div class="flex items-start gap-4 py-2 border-b border-ich-line last:border-0">
                <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    @if($registration)
        <div class="max-w-xl bg-white rounded-xl shadow-ich-card p-6 space-y-4 mt-6">
            <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3">Data Formulir Pendaftaran</h2>
            @foreach([
                ['Jenis Pendaftaran', $registration->jenis_pendaftaran],
                ['Alamat',            $registration->alamat],
                ['Anak ke',           $registration->anak_ke ? 'Anak ke-' . $registration->anak_ke : '-'],
                ['Ukuran Baju',       $registration->ukuran_baju ?? '-'],
            ] as [$label, $value])
                <div class="flex items-start gap-4 py-2 border-b border-ich-line last:border-0">
                    <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                    <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                </div>
            @endforeach

            <h3 class="font-ui font-bold text-sm text-ich-ink-600 pt-2">Biodata Ayah</h3>
            @foreach([
                ['Nama Ayah',          $registration->nama_ayah],
                ['Tempat Lahir Ayah',  $registration->tempat_lahir_ayah ?? '-'],
                ['Tanggal Lahir Ayah', $registration->tanggal_lahir_ayah ? $registration->tanggal_lahir_ayah->format('d M Y') : '-'],
                ['Alamat Ayah',        $registration->alamat_ayah ?? '-'],
                ['Pendidikan Ayah',    $registration->pendidikan_ayah ?? '-'],
                ['Pekerjaan Ayah',     $registration->pekerjaan_ayah ?? '-'],
                ['No. Telp Ayah',      $registration->no_telp_ayah ?? '-'],
            ] as [$label, $value])
                <div class="flex items-start gap-4 py-2 border-b border-ich-line last:border-0">
                    <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                    <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                </div>
            @endforeach

            <h3 class="font-ui font-bold text-sm text-ich-ink-600 pt-2">Biodata Ibu</h3>
            @foreach([
                ['Nama Ibu',          $registration->nama_ibu],
                ['Tempat Lahir Ibu',  $registration->tempat_lahir_ibu ?? '-'],
                ['Tanggal Lahir Ibu', $registration->tanggal_lahir_ibu ? $registration->tanggal_lahir_ibu->format('d M Y') : '-'],
                ['Alamat Ibu',        $registration->alamat_ibu ?? '-'],
                ['Pendidikan Ibu',    $registration->pendidikan_ibu ?? '-'],
                ['Pekerjaan Ibu',     $registration->pekerjaan_ibu ?? '-'],
                ['No. Telp Ibu',      $registration->no_telp_ibu ?? '-'],
            ] as [$label, $value])
                <div class="flex items-start gap-4 py-2 border-b border-ich-line last:border-0">
                    <div class="w-36 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                    <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                </div>
            @endforeach
        </div>
    @endif

</x-main-layout>
