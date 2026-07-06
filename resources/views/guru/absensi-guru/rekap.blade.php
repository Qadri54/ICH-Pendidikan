<x-main-layout title="Rekap Absensi Saya">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('guru.absensi-guru.index') }}" class="text-ich-ink-400 hover:text-ich-ink-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Rekap Absensi Saya</h1>
            </div>
            <p class="text-sm text-ich-ink-400 mt-0.5 ml-8">Riwayat kehadiran per bulan</p>
        </div>
        <form method="GET" action="{{ route('guru.absensi-guru.rekap') }}">
            <input type="month" name="bulan" value="{{ $bulan }}"
                   onchange="this.form.submit()"
                   class="border border-ich-line rounded-lg px-3 py-1.5 text-sm font-sans text-ich-ink-900 focus:outline-none focus:ring-2 focus:ring-ich-green/30">
        </form>
    </div>

    @php
        $parsed = \Carbon\Carbon::createFromFormat('Y-m', $bulan);
        $weeks = [];
        $startOfMonth = $parsed->copy()->startOfMonth();
        $endOfMonth = $parsed->copy()->endOfMonth();
        $current = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekNum = 1;
        while ($current->lte($endOfMonth)) {
            $weekEnd = $current->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
            $wStart = $current->lt($startOfMonth) ? $startOfMonth->copy() : $current->copy();
            $wEnd = $weekEnd->gt($endOfMonth) ? $endOfMonth->copy() : $weekEnd->copy();
            $weeks[] = [
                'num' => $weekNum,
                'start' => $wStart->format('Y-m-d'),
                'end' => $wEnd->format('Y-m-d'),
                'label' => 'Minggu ' . $weekNum,
                'range' => $wStart->format('d') . '–' . $wEnd->format('d M'),
            ];
            $current->addWeek();
            $weekNum++;
        }

        $recordsJson = $records->map(fn ($r) => [
            'date' => $r->created_at->format('Y-m-d'),
            'day' => (int) $r->created_at->format('N'),
            'date_display' => $r->created_at->translatedFormat('l, d M Y'),
            'time' => $r->check_in_time?->format('H:i') ?? '-',
            'status' => $r->attendance_status,
            'keterangan' => $r->attendance_status === 'Izin'
                ? ($r->keterangan_izin ?? '-')
                : ($r->attendance_status === 'Hadir'
                    ? ($r->is_within_geofence === 'ya' ? 'Dalam area' : 'Di luar area') . ' · ±' . ($r->check_in_accuracy ? round((float) $r->check_in_accuracy) : '-') . 'm'
                    : '-'),
            'selfie' => $r->selfie_path ? asset('storage/' . $r->selfie_path) : null,
        ])->values();

        $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    @endphp

    <div x-data="{
        week: 'all',
        day: 'all',
        weeks: {{ json_encode($weeks) }},
        records: {{ $recordsJson->toJson() }},
        dayNames: {{ json_encode($dayNames) }},
        showSelfie: {},
        setWeek(w) { this.week = w; this.day = 'all'; },
        get filteredRecords() {
            return this.records.filter(r => {
                if (this.week !== 'all') {
                    const w = this.weeks.find(x => x.num === this.week);
                    if (r.date < w.start || r.date > w.end) return false;
                }
                if (this.day !== 'all' && r.day !== this.day) return false;
                return true;
            });
        },
        get availableDays() {
            let pool = this.records;
            if (this.week !== 'all') {
                const w = this.weeks.find(x => x.num === this.week);
                pool = pool.filter(r => r.date >= w.start && r.date <= w.end);
            }
            return [...new Set(pool.map(r => r.day))].sort();
        },
        count(status) { return this.filteredRecords.filter(r => r.status === status).length; },
    }">
        {{-- Filter Minggu --}}
        <div class="flex flex-wrap gap-2 mb-4">
            <button @click="setWeek('all')" type="button"
                    :class="week === 'all' ? 'bg-ich-green text-white' : 'bg-white text-ich-ink-600 border border-ich-line'"
                    class="px-3 py-1.5 rounded-lg text-xs font-ui font-bold transition-colors">
                Semua
            </button>
            <template x-for="w in weeks" :key="w.num">
                <button @click="setWeek(w.num)" type="button"
                        :class="week === w.num ? 'bg-ich-green text-white' : 'bg-white text-ich-ink-600 border border-ich-line'"
                        class="px-3 py-1.5 rounded-lg text-xs font-ui font-bold transition-colors">
                    <span x-text="w.label"></span>
                    <span class="opacity-70 ml-1" x-text="'(' + w.range + ')'"></span>
                </button>
            </template>
        </div>

        {{-- Filter Hari --}}
        <div class="flex flex-wrap gap-2 mb-6" x-show="week !== 'all'" x-transition>
            <button @click="day = 'all'" type="button"
                    :class="day === 'all' ? 'bg-ich-teal text-white' : 'bg-white text-ich-ink-600 border border-ich-line'"
                    class="px-3 py-1.5 rounded-lg text-xs font-ui font-bold transition-colors">
                Semua Hari
            </button>
            <template x-for="(name, idx) in dayNames" :key="idx">
                <button x-show="availableDays.includes(idx + 1)"
                        @click="day = idx + 1" type="button"
                        :class="day === (idx + 1) ? 'bg-ich-teal text-white' : 'bg-white text-ich-ink-600 border border-ich-line'"
                        class="px-3 py-1.5 rounded-lg text-xs font-ui font-bold transition-colors"
                        x-text="name">
                </button>
            </template>
        </div>

        {{-- Summary pills (reactive) --}}
        <div class="flex flex-wrap gap-2 mb-6">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-success-soft text-ich-success text-xs font-ui font-bold">
                Hadir <span x-text="count('Hadir')"></span>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-info-soft text-ich-info text-xs font-ui font-bold">
                Sakit <span x-text="count('Sakit')"></span>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-purple-soft text-ich-purple text-xs font-ui font-bold">
                Izin <span x-text="count('Izin')"></span>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-error-soft text-ich-error text-xs font-ui font-bold">
                Tanpa Ket. <span x-text="count('Tanpa Keterangan')"></span>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-ich-ink-600 text-xs font-ui font-bold">
                Total <span x-text="filteredRecords.length"></span> hari
            </span>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-ich-surface">
                        <tr>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">No</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Tanggal</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Jam</th>
                            <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Status</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Keterangan</th>
                            <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Selfie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ich-line">
                        <template x-for="(rec, i) in filteredRecords" :key="i">
                            <tr class="hover:bg-ich-surface transition-colors">
                                <td class="px-4 py-3 text-ich-ink-400" x-text="i + 1"></td>
                                <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900" x-text="rec.date_display"></td>
                                <td class="px-4 py-3 text-ich-ink-600" x-text="rec.time"></td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="{
                                        'bg-ich-success-soft text-ich-success': rec.status === 'Hadir',
                                        'bg-ich-info-soft text-ich-info': rec.status === 'Sakit',
                                        'bg-ich-purple-soft text-ich-purple': rec.status === 'Izin',
                                        'bg-ich-error-soft text-ich-error': rec.status === 'Tanpa Keterangan',
                                    }" class="px-2.5 py-1 font-ui font-bold text-xs rounded-full"
                                       x-text="rec.status === 'Tanpa Keterangan' ? 'Tanpa Ket.' : rec.status">
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-ich-ink-600 text-xs" x-text="rec.keterangan"></td>
                                <td class="px-4 py-3 text-center">
                                    <template x-if="rec.selfie">
                                        <div>
                                            <button @click="showSelfie[i] = !showSelfie[i]" type="button" class="text-ich-teal hover:text-ich-green text-xs font-ui font-bold">
                                                <span x-text="showSelfie[i] ? 'Tutup' : 'Lihat'"></span>
                                            </button>
                                            <div x-show="showSelfie[i]" class="mt-2">
                                                <img :src="rec.selfie" alt="Selfie" class="w-16 h-16 rounded-lg object-cover border border-ich-line mx-auto">
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!rec.selfie">
                                        <span class="text-ich-ink-300">-</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="filteredRecords.length === 0" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                    Belum ada data absensi pada periode ini.
                </div>
            </div>
        </div>
    </div>

</x-main-layout>
