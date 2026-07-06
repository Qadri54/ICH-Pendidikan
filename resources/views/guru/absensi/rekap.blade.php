<x-main-layout title="Rekap Absensi Siswa">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('guru.absensi.index') }}" class="text-ich-ink-400 hover:text-ich-ink-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Rekap Absensi Siswa</h1>
            </div>
            <p class="text-sm text-ich-ink-400 mt-0.5 ml-8">
                @if($classroom)
                    {{ $classroom->nama_kelas }} · Riwayat kehadiran per bulan
                @else
                    Anda belum ditugaskan sebagai wali kelas
                @endif
            </p>
        </div>
        @if($classroom)
            <form method="GET" action="{{ route('guru.absensi.rekap') }}">
                <input type="month" name="bulan" value="{{ $bulan }}"
                       onchange="this.form.submit()"
                       class="border border-ich-line rounded-lg px-3 py-1.5 text-sm font-sans text-ich-ink-900 focus:outline-none focus:ring-2 focus:ring-ich-green/30">
            </form>
        @endif
    </div>

    @if(! $classroom)
        <div class="bg-white rounded-xl shadow-ich-card p-10 text-center">
            <x-ich-icon name="school" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-ich-ink-600">Anda belum ditugaskan sebagai wali kelas.</p>
        </div>
    @else
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
                'student' => $r->student?->nama_siswa ?? '-',
                'student_id' => $r->student?->student_id,
                'status' => $r->status,
                'keterangan' => $r->keterangan_izin ?? '-',
            ])->values();

            $overviewData = $records->groupBy(fn ($r) => $r->student?->student_id)->map(function ($group) {
                $student = $group->first()->student;
                return [
                    'student_id' => $student?->student_id,
                    'nama' => $student?->nama_siswa ?? '-',
                    'hadir' => $group->where('status', 'hadir')->count(),
                    'izin' => $group->where('status', 'izin')->count(),
                    'sakit' => $group->where('status', 'sakit')->count(),
                    'alpha' => $group->where('status', 'tanpa keterangan')->count(),
                    'total' => $group->count(),
                ];
            })->sortBy('nama')->values();

            $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        @endphp

        <div x-data="{
            week: 'all',
            day: 'all',
            selectedStudent: 'all',
            weeks: {{ json_encode($weeks) }},
            records: {{ $recordsJson->toJson() }},
            overview: {{ $overviewData->toJson() }},
            dayNames: {{ json_encode($dayNames) }},
            setWeek(w) { this.week = w; this.day = 'all'; },
            selectStudent(id) { this.selectedStudent = this.selectedStudent === id ? 'all' : id; },
            get filteredRecords() {
                return this.records.filter(r => {
                    if (this.week !== 'all') {
                        const w = this.weeks.find(x => x.num === this.week);
                        if (r.date < w.start || r.date > w.end) return false;
                    }
                    if (this.day !== 'all' && r.day !== this.day) return false;
                    if (this.selectedStudent !== 'all' && r.student_id !== this.selectedStudent) return false;
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

            {{-- Overview Per Siswa --}}
            <div class="bg-white rounded-xl shadow-ich-card overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-ich-line">
                    <h2 class="font-ui font-bold text-ich-ink-900">Ringkasan Per Siswa</h2>
                    <p class="text-xs text-ich-ink-400 mt-0.5">Klik nama siswa untuk filter detail di bawah</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-ich-surface">
                            <tr>
                                <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600 w-12">No</th>
                                <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                                <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Hadir</th>
                                <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Izin</th>
                                <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Sakit</th>
                                <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Tanpa Ket.</th>
                                <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ich-line">
                            <template x-for="(s, i) in overview" :key="s.student_id">
                                <tr class="hover:bg-ich-surface transition-colors cursor-pointer"
                                    :class="selectedStudent === s.student_id && 'bg-ich-green-surface'"
                                    @click="selectStudent(s.student_id)">
                                    <td class="px-4 py-3 text-ich-ink-400" x-text="i + 1"></td>
                                    <td class="px-4 py-3 font-ui font-semibold"
                                        :class="selectedStudent === s.student_id ? 'text-ich-green' : 'text-ich-teal'"
                                        x-text="s.nama"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 bg-ich-success-soft text-ich-success font-ui font-bold text-xs rounded-full" x-text="s.hadir"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 bg-ich-purple-soft text-ich-purple font-ui font-bold text-xs rounded-full" x-text="s.izin"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 bg-ich-info-soft text-ich-info font-ui font-bold text-xs rounded-full" x-text="s.sakit"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 bg-ich-error-soft text-ich-error font-ui font-bold text-xs rounded-full" x-text="s.alpha"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-ui font-bold text-ich-ink-900" x-text="s.total"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div x-show="overview.length === 0" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                        Belum ada data absensi pada bulan ini.
                    </div>
                </div>
            </div>

            {{-- Detail Section --}}
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-bold text-lg text-ich-ink-900">Detail Kehadiran</h2>
                <button x-show="selectedStudent !== 'all'" @click="selectedStudent = 'all'" type="button"
                        class="text-xs font-ui font-bold text-ich-error hover:underline" x-cloak>
                    Reset filter siswa
                </button>
            </div>

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
                    Hadir <span x-text="count('hadir')"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-info-soft text-ich-info text-xs font-ui font-bold">
                    Sakit <span x-text="count('sakit')"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-purple-soft text-ich-purple text-xs font-ui font-bold">
                    Izin <span x-text="count('izin')"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-error-soft text-ich-error text-xs font-ui font-bold">
                    Tanpa Ket. <span x-text="count('tanpa keterangan')"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-ich-ink-600 text-xs font-ui font-bold">
                    Total <span x-text="filteredRecords.length"></span> data
                </span>
            </div>

            {{-- Detail Table --}}
            <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-ich-surface">
                            <tr>
                                <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">No</th>
                                <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Tanggal</th>
                                <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                                <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Status</th>
                                <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ich-line">
                            <template x-for="(rec, i) in filteredRecords" :key="i">
                                <tr class="hover:bg-ich-surface transition-colors">
                                    <td class="px-4 py-3 text-ich-ink-400" x-text="i + 1"></td>
                                    <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900" x-text="rec.date_display"></td>
                                    <td class="px-4 py-3 text-ich-ink-600" x-text="rec.student"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span :class="{
                                            'bg-ich-success-soft text-ich-success': rec.status === 'hadir',
                                            'bg-ich-info-soft text-ich-info': rec.status === 'sakit',
                                            'bg-ich-purple-soft text-ich-purple': rec.status === 'izin',
                                            'bg-ich-error-soft text-ich-error': rec.status === 'tanpa keterangan',
                                        }" class="px-2.5 py-1 font-ui font-bold text-xs rounded-full"
                                           x-text="rec.status === 'tanpa keterangan' ? 'Tanpa Ket.' : rec.status.charAt(0).toUpperCase() + rec.status.slice(1)">
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-ich-ink-600 text-xs" x-text="rec.keterangan"></td>
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
    @endif

</x-main-layout>
