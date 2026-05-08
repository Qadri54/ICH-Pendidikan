<x-mobile-layout title="Profil Anak" page-title="Profil Anak">

    @if($students->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card p-8 text-center">
            <x-ich-icon name="user_circle" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-sm text-ich-ink-900 mb-1">Belum Ada Data Anak</p>
            <p class="font-sans text-xs text-ich-ink-400 mb-5">
                Data anak akan tersedia setelah pendaftaran disetujui oleh pihak sekolah.
            </p>
            <a href="{{ route('pendaftaran') }}"
               class="inline-block px-5 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg">
                Daftar Sekarang
            </a>
        </div>
    @else
        <div class="space-y-5">
        @foreach($students as $student)
            <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">

                {{-- Header --}}
                <div class="bg-ich-green px-5 py-4 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                        <x-ich-icon name="user" :size="24" color="white"/>
                    </div>
                    <div class="min-w-0">
                        <p class="font-display font-bold text-base text-white leading-tight">
                            {{ $student->nama_siswa }}
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            @if($student->classRoom)
                                <span class="px-2 py-0.5 bg-white/20 text-white font-ui font-bold text-xs rounded-full">
                                    {{ $student->classRoom->nama_kelas }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-white/20 text-white/70 font-ui text-xs rounded-full">
                                    Belum Ditempatkan
                                </span>
                            @endif
                            @if($student->NIS)
                                <span class="text-white/60 text-xs font-sans">NIS: {{ $student->NIS }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Detail --}}
                <div class="divide-y divide-ich-line">
                    @foreach([
                        ['Jenis Kelamin', $student->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'],
                        ['Tempat Lahir',  $student->tempat_lahir ?? '-'],
                        ['Tanggal Lahir', $student->tanggal_lahir?->translatedFormat('d F Y') ?? '-'],
                        ['Nama Ayah',     $student->nama_ayah ?? '-'],
                        ['Nama Ibu',      $student->nama_ibu ?? '-'],
                    ] as [$label, $value])
                        <div class="flex gap-3 px-5 py-3">
                            <span class="w-28 shrink-0 font-ui font-bold text-xs text-ich-ink-400 pt-0.5">
                                {{ $label }}
                            </span>
                            <span class="font-sans text-sm text-ich-ink-900">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>

            </div>
        @endforeach
        </div>
    @endif

</x-mobile-layout>
