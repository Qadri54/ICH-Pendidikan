<x-mobile-layout title="Akademik {{ $student->nama_siswa }}" page-title="Akademik">

    <div class="mb-4">
        <a href="{{ route('akademik') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">&larr; Kembali</a>
    </div>

    {{-- Student header --}}
    <div class="bg-white rounded-xl shadow-ich-card p-5 mb-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-ich-purple-soft flex items-center justify-center shrink-0">
                <span class="font-display font-bold text-lg text-ich-purple">
                    {{ strtoupper(substr($student->nama_siswa, 0, 1)) }}
                </span>
            </div>
            <div>
                <p class="font-ui font-bold text-sm text-ich-ink-900">{{ $student->nama_siswa }}</p>
                <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                    {{ $student->classRoom?->nama_kelas ?? 'Belum ada kelas' }}
                    @if($student->NIS) · NIS: {{ $student->NIS }} @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Daftar Raport --}}
    @if($raports->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card px-5 py-6 text-center">
            <x-ich-icon name="document" :size="36" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-sm text-ich-ink-600">Belum ada raport tersedia</p>
            <p class="font-sans text-xs text-ich-ink-400 mt-1">
                Raport akan tersedia setelah guru menyelesaikan penilaian.
            </p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="divide-y divide-ich-line">
                @foreach($raports as $raport)
                    @php
                        $stCfg = match($raport->status) {
                            'approved'  => ['label' => 'Disetujui',          'bg' => 'bg-ich-success-soft', 'text' => 'text-ich-success'],
                            'submitted' => ['label' => 'Menunggu Persetujuan','bg' => 'bg-ich-warning-soft', 'text' => 'text-ich-warning'],
                            default     => ['label' => 'Draft',              'bg' => 'bg-ich-surface',      'text' => 'text-ich-ink-500'],
                        };
                    @endphp
                    <div class="px-5 py-4 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-ui font-semibold text-sm text-ich-ink-900">
                                {{ $raport->period->tahun_ajaran }} — Semester {{ $raport->period->semester }}
                            </p>
                            <span class="inline-block mt-1.5 px-2 py-0.5 rounded-full text-xs font-ui font-bold
                                         {{ $stCfg['bg'] }} {{ $stCfg['text'] }}">
                                {{ $stCfg['label'] }}
                            </span>
                        </div>

                        @if($raport->status === 'approved')
                            <a href="{{ route('raport.download', $raport->report_card_id) }}"
                               class="flex items-center gap-1.5 px-3 py-1.5 bg-ich-green text-white
                                      font-ui font-bold text-xs rounded-lg shadow-ich-btn
                                      hover:bg-ich-green-dark transition-colors flex-shrink-0">
                                <x-ich-icon name="download" :size="13" color="white"/>
                                Unduh PDF
                            </a>
                        @else
                            <span class="text-xs font-sans text-ich-ink-300 italic">Belum tersedia</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</x-mobile-layout>
