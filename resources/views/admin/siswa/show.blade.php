<x-main-layout title="Detail Siswa">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.siswa.index') }}"
               class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Detail Siswa</h1>
        </div>
        <a href="{{ route('admin.siswa.edit', $siswa) }}"
           class="px-4 py-2 bg-ich-yellow text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-yellow-dark transition-colors">
            Edit
        </a>
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

</x-main-layout>
