<x-mobile-layout title="Kehadiran" page-title="Kehadiran">

    @if($kehadiranData->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card p-8 text-center">
            <x-ich-icon name="calendar" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-sm text-ich-ink-900 mb-1">Belum Ada Data</p>
            <p class="font-sans text-xs text-ich-ink-400">
                Data kehadiran anak akan tersedia setelah terdaftar di sekolah.
            </p>
        </div>
    @else
        <div class="space-y-6">
        @foreach($kehadiranData as $data)
            @php
                $student = $data['student'];
                $records = $data['records'];
                $summary = $data['summary'];
            @endphp

            <div>
                <h2 class="font-display font-bold text-base text-ich-ink-900 mb-3">
                    {{ $student->nama_siswa }}
                    @if($student->classRoom)
                        <span class="text-xs font-ui font-semibold text-ich-ink-400 ml-1">
                            · {{ $student->classRoom->nama_kelas }}
                        </span>
                    @endif
                </h2>

                {{-- Ringkasan bulan ini --}}
                <div class="grid grid-cols-4 gap-2 mb-3">
                    <div class="bg-white rounded-xl shadow-ich-card p-3 text-center">
                        <p class="text-lg font-display font-bold text-ich-green">{{ $summary['hadir'] ?? 0 }}</p>
                        <p class="text-[10px] font-ui font-semibold text-ich-ink-400 mt-0.5">Hadir</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-ich-card p-3 text-center">
                        <p class="text-lg font-display font-bold text-[#8B5CF6]">{{ $summary['izin'] }}</p>
                        <p class="text-[10px] font-ui font-semibold text-ich-ink-400 mt-0.5">Izin</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-ich-card p-3 text-center">
                        <p class="text-lg font-display font-bold text-ich-error">{{ $summary['sakit'] }}</p>
                        <p class="text-[10px] font-ui font-semibold text-ich-ink-400 mt-0.5">Sakit</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-ich-card p-3 text-center">
                        <p class="text-lg font-display font-bold text-[#E09F17]">{{ $summary['tanpa_keterangan'] }}</p>
                        <p class="text-[10px] font-ui font-semibold text-ich-ink-400 mt-0.5">Alfa</p>
                    </div>
                </div>
                <p class="text-xs text-ich-ink-400 font-sans mb-3">*Ringkasan bulan {{ now()->translatedFormat('F Y') }}</p>

                {{-- Riwayat kehadiran --}}
                @if($records->isEmpty())
                    <div class="bg-white rounded-xl shadow-ich-card px-5 py-4 text-center">
                        <p class="font-sans text-sm text-ich-ink-400 font-semibold">
                            Belum ada data kehadiran.
                        </p>
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                        <div class="px-5 py-3 border-b border-ich-line">
                            <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">
                                Riwayat Kehadiran
                            </p>
                        </div>
                        <div class="divide-y divide-ich-line">
                            @foreach($records as $rec)
                                @php
                                    $statusCfg = match($rec->status) {
                                        'hadir'            => ['label'=>'Hadir',            'bg'=>'bg-[#D1FAE5]','text'=>'text-[#009966]'],
                                        'izin'             => ['label'=>'Izin',             'bg'=>'bg-[#EDE9FE]','text'=>'text-[#8B5CF6]'],
                                        'sakit'            => ['label'=>'Sakit',            'bg'=>'bg-[#FEE2E2]','text'=>'text-ich-error'],
                                        'tanpa keterangan' => ['label'=>'Alfa',             'bg'=>'bg-[#FEF5DC]','text'=>'text-[#E09F17]'],
                                        default            => ['label'=>$rec->status,       'bg'=>'bg-[#F5F6FA]','text'=>'text-ich-ink-400'],
                                    };
                                @endphp
                                <div class="px-5 py-3 flex items-center justify-between">
                                    <p class="font-sans text-sm text-ich-ink-900">
                                        {{ $rec->created_at->translatedFormat('d F Y') }}
                                    </p>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-ui font-bold
                                                 {{ $statusCfg['bg'] }} {{ $statusCfg['text'] }}">
                                        {{ $statusCfg['label'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        @endforeach
        </div>
    @endif

</x-mobile-layout>
