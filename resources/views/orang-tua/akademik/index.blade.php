<x-mobile-layout title="Akademik" page-title="Akademik">

    @if($data->isEmpty())
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
    @else
        <div class="space-y-3">
        @foreach($data as $item)
            @php
                $student       = $item['student'];
                $approvedCount = $item['approvedCount'];
                $totalCount    = $item['totalCount'];
            @endphp

            <a href="{{ route('akademik.detail', $student->student_id) }}"
               class="block bg-white rounded-xl shadow-ich-card p-5 hover:shadow-md transition-shadow active:scale-[0.98]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-ich-purple-soft flex items-center justify-center shrink-0">
                        <span class="font-display font-bold text-lg text-ich-purple">
                            {{ strtoupper(substr($student->nama_siswa, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-ui font-bold text-sm text-ich-ink-900 truncate">
                            {{ $student->nama_siswa }}
                        </p>
                        <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                            {{ $student->classRoom?->nama_kelas ?? 'Belum ada kelas' }}
                            @if($student->NIS) · NIS: {{ $student->NIS }} @endif
                        </p>

                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @if($totalCount > 0)
                                <span class="px-2 py-0.5 bg-ich-success-soft text-ich-success text-xs font-ui font-bold rounded-full">
                                    {{ $approvedCount }} raport tersedia
                                </span>
                                @if($totalCount - $approvedCount > 0)
                                    <span class="px-2 py-0.5 bg-ich-warning-soft text-ich-warning text-xs font-ui font-bold rounded-full">
                                        {{ $totalCount - $approvedCount }} proses
                                    </span>
                                @endif
                            @else
                                <span class="px-2 py-0.5 bg-ich-surface text-ich-ink-400 text-xs font-ui font-bold rounded-full">
                                    Belum ada raport
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
