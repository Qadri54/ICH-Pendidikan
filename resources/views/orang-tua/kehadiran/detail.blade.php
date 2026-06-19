<x-mobile-layout title="Kehadiran {{ $student->nama_siswa }}" page-title="Kehadiran">

    <div class="mb-4">
        <a href="{{ route('kehadiran') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">&larr; Kembali</a>
    </div>

    {{-- Student header --}}
    <div class="bg-white rounded-xl shadow-ich-card p-5 mb-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-ich-blue-soft flex items-center justify-center shrink-0">
                <span class="font-display font-bold text-lg text-ich-info">
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

    {{-- Ringkasan bulan ini --}}
    <div class="grid grid-cols-4 gap-2 mb-4">
        <div class="bg-white rounded-xl shadow-ich-card p-3 text-center">
            <p class="text-lg font-display font-bold text-ich-green">{{ $summary['hadir'] }}</p>
            <p class="text-[10px] font-ui font-semibold text-ich-ink-400 mt-0.5">Hadir</p>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-3 text-center">
            <p class="text-lg font-display font-bold text-ich-purple">{{ $summary['izin'] }}</p>
            <p class="text-[10px] font-ui font-semibold text-ich-ink-400 mt-0.5">Izin</p>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-3 text-center">
            <p class="text-lg font-display font-bold text-ich-error">{{ $summary['sakit'] }}</p>
            <p class="text-[10px] font-ui font-semibold text-ich-ink-400 mt-0.5">Sakit</p>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-3 text-center">
            <p class="text-lg font-display font-bold text-ich-warning">{{ $summary['tanpa_keterangan'] }}</p>
            <p class="text-[10px] font-ui font-semibold text-ich-ink-400 mt-0.5">Alfa</p>
        </div>
    </div>
    <p class="text-xs text-ich-ink-400 font-sans mb-4">*Ringkasan bulan {{ now()->translatedFormat('F Y') }}</p>

    {{-- Riwayat kehadiran --}}
    @if($records->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card px-5 py-6 text-center">
            <p class="font-sans text-sm text-ich-ink-400">Belum ada data kehadiran.</p>
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
                            'hadir'            => ['label'=>'Hadir', 'bg'=>'bg-ich-success-soft','text'=>'text-ich-success'],
                            'izin'             => ['label'=>'Izin',  'bg'=>'bg-ich-purple-soft','text'=>'text-ich-purple'],
                            'sakit'            => ['label'=>'Sakit', 'bg'=>'bg-ich-error-soft','text'=>'text-ich-error'],
                            'tanpa keterangan' => ['label'=>'Alfa',  'bg'=>'bg-ich-warning-soft','text'=>'text-ich-warning'],
                            default            => ['label'=>$rec->status, 'bg'=>'bg-ich-surface','text'=>'text-ich-ink-400'],
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

</x-mobile-layout>
