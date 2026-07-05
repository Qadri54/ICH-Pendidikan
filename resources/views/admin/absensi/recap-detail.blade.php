<x-main-layout title="Detail Absensi — {{ $student->nama_siswa }}">

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-ich-warning-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">{{ $student->nama_siswa }}</h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">
                    {{ $student->classRoom?->nama_kelas ?? '-' }} —
                    {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
                </p>
            </div>
        </div>
        <a href="{{ route('admin.absensi.recap', ['class_id' => $student->class_id, 'year' => $year, 'month' => $month]) }}"
           class="text-sm font-ui font-bold text-ich-teal hover:underline">
            ← Kembali ke Rekap
        </a>
    </div>

    {{-- Summary Pills --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-xl shadow-ich-card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-ich-success-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-xs font-ui text-ich-ink-400">Hadir</p>
                <p class="text-xl font-display font-bold text-ich-ink-900">{{ $summary['hadir'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-ich-purple-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
            </div>
            <div>
                <p class="text-xs font-ui text-ich-ink-400">Izin</p>
                <p class="text-xl font-display font-bold text-ich-ink-900">{{ $summary['izin'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-ich-error-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
            </div>
            <div>
                <p class="text-xs font-ui text-ich-ink-400">Sakit</p>
                <p class="text-xl font-display font-bold text-ich-ink-900">{{ $summary['sakit'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-ich-warning-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
            </div>
            <div>
                <p class="text-xs font-ui text-ich-ink-400">Tanpa Ket.</p>
                <p class="text-xl font-display font-bold text-ich-ink-900">{{ $summary['tanpa_keterangan'] }}</p>
            </div>
        </div>
    </div>

    {{-- Detail Table --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="px-6 py-4 border-b border-ich-line">
            <h2 class="font-ui font-bold text-ich-ink-900">Riwayat Absensi</h2>
            <p class="text-xs text-ich-ink-400 mt-0.5">{{ $records->count() }} catatan pada bulan ini</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-ich-surface">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500 w-12">No</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Tanggal</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Hari</th>
                        <th class="px-4 py-3 text-center font-ui font-bold text-xs text-ich-ink-500">Status</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($records as $i => $record)
                        @php
                            $stCfg = match($record->status) {
                                'hadir'            => ['label' => 'Hadir',            'bg' => 'bg-ich-success-soft', 'text' => 'text-ich-success'],
                                'izin'             => ['label' => 'Izin',             'bg' => 'bg-ich-purple-soft',  'text' => 'text-ich-purple'],
                                'sakit'            => ['label' => 'Sakit',            'bg' => 'bg-ich-error-soft',   'text' => 'text-ich-error'],
                                'tanpa keterangan' => ['label' => 'Tanpa Keterangan', 'bg' => 'bg-ich-warning-soft', 'text' => 'text-ich-warning'],
                                default            => ['label' => $record->status,    'bg' => 'bg-ich-surface',      'text' => 'text-ich-ink-400'],
                            };
                        @endphp
                        <tr class="hover:bg-ich-surface transition-colors">
                            <td class="px-4 py-3 text-ich-ink-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                {{ $record->created_at->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-4 py-3 font-sans text-ich-ink-600">
                                {{ $record->created_at->translatedFormat('l') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-ui font-bold {{ $stCfg['bg'] }} {{ $stCfg['text'] }}">
                                    {{ $stCfg['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-sans text-xs text-ich-ink-600">
                                {{ $record->keterangan_izin ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                Tidak ada catatan absensi pada bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-main-layout>
