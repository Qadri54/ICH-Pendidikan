<x-mobile-layout title="Akademik" page-title="Akademik">

    <div class="p-4 space-y-4">

        {{-- Header --}}
        <div class="bg-ich-green rounded-xl p-5 text-white">
            <p class="font-sans text-sm opacity-80">Raport Anak</p>
            <p class="font-display font-bold text-lg mt-0.5">Riwayat Akademik</p>
            <p class="font-sans text-xs opacity-70 mt-1">Unduh raport yang sudah disetujui sekolah</p>
        </div>

        @forelse($students as $student)
            @php $raports = $raportPerAnak[$student->student_id]; @endphp

            <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                {{-- Info Siswa --}}
                <div class="px-4 py-3 bg-ich-surface border-b border-ich-line">
                    <p class="font-ui font-bold text-sm text-ich-ink-900">{{ $student->nama_siswa }}</p>
                    <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                        {{ $student->classRoom?->nama_kelas ?? 'Belum ada kelas' }}
                        @if($student->NIS) · NIS: {{ $student->NIS }} @endif
                    </p>
                </div>

                {{-- Daftar Raport --}}
                @forelse($raports as $raport)
                    @php
                        $stCfg = match($raport->status) {
                            'approved'  => ['label' => 'Disetujui',         'bg' => 'bg-ich-success-soft', 'text' => 'text-ich-success'],
                            'submitted' => ['label' => 'Menunggu Persetujuan','bg' => 'bg-ich-warning-soft', 'text' => 'text-ich-warning'],
                            default     => ['label' => 'Draft',              'bg' => 'bg-ich-surface', 'text' => 'text-ich-ink-500'],
                        };
                    @endphp
                    <div class="px-4 py-3 border-b border-ich-line flex items-center justify-between gap-3">
                        <div>
                            <p class="font-ui font-semibold text-sm text-ich-ink-900">
                                {{ $raport->period->tahun_ajaran }} — Semester {{ $raport->period->semester }}
                            </p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-ui font-bold
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
                @empty
                    <div class="px-4 py-6 text-center">
                        <p class="font-sans text-sm text-ich-ink-400">Belum ada raport tersedia.</p>
                    </div>
                @endforelse
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-ich-card p-8 text-center">
                <x-ich-icon name="document" :size="36" color="#99A1AF" class="mx-auto mb-3"/>
                <p class="font-ui font-bold text-ich-ink-600">Belum ada data anak</p>
                <p class="font-sans text-sm text-ich-ink-400 mt-1">Daftarkan anak terlebih dahulu.</p>
                <a href="{{ route('pendaftaran') }}"
                   class="inline-block mt-3 px-4 py-2 bg-ich-green text-white font-ui font-bold text-sm
                          rounded-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Daftar Anak
                </a>
            </div>
        @endforelse

    </div>

</x-mobile-layout>
