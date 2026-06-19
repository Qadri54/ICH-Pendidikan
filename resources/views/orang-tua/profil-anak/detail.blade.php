<x-mobile-layout title="Profil {{ $student->nama_siswa }}" page-title="Profil Anak">

    <div class="mb-4">
        <a href="{{ route('profil-anak') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">&larr; Kembali</a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">

        {{-- Header --}}
        <div class="px-5 pt-6 pb-4 flex flex-col items-center text-center"
             x-data="{ uploading: false }">
            {{-- Foto profil --}}
            <div class="relative mb-3">
                @if($student->foto)
                    <img src="{{ Storage::url($student->foto) }}"
                         alt="{{ $student->nama_siswa }}"
                         class="w-20 h-20 rounded-full object-cover border-3 border-ich-line">
                @else
                    <div class="w-20 h-20 rounded-full bg-ich-green-surface flex items-center justify-center border-3 border-ich-line">
                        <span class="font-display font-bold text-2xl text-ich-green">
                            {{ strtoupper(substr($student->nama_siswa, 0, 1)) }}
                        </span>
                    </div>
                @endif

                {{-- Tombol upload --}}
                <form method="POST"
                      action="{{ route('profil-anak.foto', $student->student_id) }}"
                      enctype="multipart/form-data"
                      x-ref="fotoForm"
                      @submit="uploading = true">
                    @csrf
                    <label class="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-ich-teal
                                  flex items-center justify-center cursor-pointer
                                  shadow-md hover:bg-ich-teal-dark transition-colors">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <input type="file" name="foto" accept="image/jpg,image/jpeg,image/png"
                               class="hidden"
                               @change="$refs.fotoForm.submit()">
                    </label>
                </form>
            </div>

            {{-- Loading indicator --}}
            <div x-show="uploading" x-cloak class="mb-2">
                <svg class="animate-spin h-5 w-5 text-ich-teal mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="font-sans text-xs text-ich-ink-400 mt-1">Mengupload foto...</p>
            </div>

            <p class="font-display font-bold text-lg text-ich-ink-900">
                {{ $student->nama_siswa }}
            </p>
            <div class="flex items-center gap-2 mt-1.5">
                @if($student->classRoom)
                    <span class="px-2.5 py-0.5 bg-ich-green-surface text-ich-green font-ui font-bold text-xs rounded-full">
                        {{ $student->classRoom->nama_kelas }}
                    </span>
                @else
                    <span class="px-2.5 py-0.5 bg-ich-surface text-ich-ink-400 font-ui text-xs rounded-full">
                        Belum Ditempatkan
                    </span>
                @endif
                @if($student->NIS)
                    <span class="text-ich-ink-400 text-xs font-sans">NIS: {{ $student->NIS }}</span>
                @endif
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

</x-mobile-layout>
