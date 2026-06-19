<x-mobile-layout title="Profil Anak" page-title="Profil Anak">

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

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
        <div class="space-y-3">
        @foreach($students as $student)
            <a href="{{ route('profil-anak.detail', $student->student_id) }}"
               class="block bg-white rounded-xl shadow-ich-card p-5 hover:shadow-md transition-shadow active:scale-[0.98]">
                <div class="flex items-center gap-4">
                    <div class="shrink-0">
                        @if($student->foto)
                            <img src="{{ Storage::url($student->foto) }}"
                                 alt="{{ $student->nama_siswa }}"
                                 class="w-12 h-12 rounded-xl object-cover">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-ich-green-surface flex items-center justify-center">
                                <span class="font-display font-bold text-lg text-ich-green">
                                    {{ strtoupper(substr($student->nama_siswa, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-ui font-bold text-sm text-ich-ink-900 truncate">
                            {{ $student->nama_siswa }}
                        </p>
                        <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                            {{ $student->classRoom?->nama_kelas ?? 'Belum Ditempatkan' }}
                            @if($student->NIS) · NIS: {{ $student->NIS }} @endif
                        </p>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <span class="px-2 py-0.5 bg-ich-info-soft text-ich-info text-xs font-ui font-bold rounded-full">
                                {{ $student->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                            @if($student->tanggal_lahir)
                                <span class="px-2 py-0.5 bg-ich-surface text-ich-ink-500 text-xs font-ui font-bold rounded-full">
                                    {{ $student->tanggal_lahir->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <x-ich-icon name="chevron_right" :size="20" color="#99A1AF"/>
                </div>
            </a>
        @endforeach
        </div>
    @endif

</x-mobile-layout>
