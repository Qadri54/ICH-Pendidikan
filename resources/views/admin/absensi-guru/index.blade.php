@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Absensi Guru">

    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-ich-pink-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Absensi Guru</h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">Rekap dan input absensi guru</p>
            </div>
        </div>
        <a href="{{ route('admin.absensi-guru.recap') }}"
           class="text-sm font-ui font-bold text-ich-teal hover:underline">
            Rekap Bulanan →
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('error'))
        <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">
            {{ $errors->first('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6" x-data="{ photoUrl: '' }">

        {{-- Lightbox --}}
        <div x-show="photoUrl" x-cloak
             class="fixed inset-0 z-[9990] flex items-center justify-center bg-black/75 p-4"
             @click="photoUrl = ''" @keydown.escape.window="photoUrl && (photoUrl = '')">
            <img :src="photoUrl" @click.stop
                 class="max-h-[85vh] max-w-[90vw] rounded-xl shadow-2xl object-contain">
            <button @click="photoUrl = ''"
                    class="absolute top-4 right-4 w-10 h-10 bg-black/40 rounded-full flex items-center justify-center text-white hover:bg-black/60">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Tabel Rekap --}}
        <div class="xl:col-span-2 space-y-5">

            {{-- Filter --}}
            <form method="GET" action="{{ route('admin.absensi-guru.index') }}"
                  class="bg-white rounded-xl shadow-ich-card p-5 flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[160px]">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $filters['tanggal'] ?? '' }}"
                           class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                  font-sans text-sm focus:outline-none focus:border-ich-teal">
                </div>
                <div class="flex-1 min-w-[140px]">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Status</label>
                    <select name="status"
                            class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                   font-sans text-sm focus:outline-none focus:border-ich-teal">
                        <option value="">Semua Status</option>
                        <option value="Hadir"              {{ ($filters['status'] ?? '') === 'Hadir'              ? 'selected' : '' }}>Hadir</option>
                        <option value="Izin"               {{ ($filters['status'] ?? '') === 'Izin'               ? 'selected' : '' }}>Izin</option>
                        <option value="Sakit"              {{ ($filters['status'] ?? '') === 'Sakit'              ? 'selected' : '' }}>Sakit</option>
                        <option value="Tanpa Keterangan"   {{ ($filters['status'] ?? '') === 'Tanpa Keterangan'   ? 'selected' : '' }}>Tanpa Keterangan</option>
                    </select>
                </div>
                <button type="submit"
                        class="h-10 px-5 bg-ich-green text-white font-ui font-bold text-sm
                               rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Tampilkan
                </button>
            </form>

            {{-- Tabel Rekap --}}
            <div class="bg-white rounded-xl shadow-ich-card overflow-hidden" x-data="{ searchHarian: '' }">

                <div class="px-5 py-4 border-b border-ich-line flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="font-ui font-bold text-ich-ink-900">Rekap Absensi Harian</h2>
                        <p class="text-xs text-ich-ink-400 mt-0.5">
                            @if($records)
                                {{ $records->total() }} data ditemukan
                            @else
                                Pilih tanggal untuk menampilkan data
                            @endif
                        </p>
                    </div>
                    @if($records && $records->isNotEmpty())
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ich-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" x-model="searchHarian" placeholder="Cari nama guru..."
                                   class="pl-9 pr-3 py-1.5 w-56 border border-ich-line rounded-lg text-sm font-sans
                                          focus:outline-none focus:ring-2 focus:ring-ich-green/30">
                        </div>
                    @endif
                </div>

                @if(!$records)
                    <div class="px-5 py-12 text-center">
                        <x-ich-icon name="calendar" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
                        <p class="font-sans text-ich-ink-400">Pilih tanggal lalu klik Tampilkan.</p>
                    </div>
                @elseif($records->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <x-ich-icon name="calendar" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
                        <p class="font-sans text-ich-ink-400">Belum ada data absensi pada tanggal ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-ich-surface">
                                <tr>
                                    <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Nama Guru</th>
                                    <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Tipe</th>
                                    <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Tanggal</th>
                                    <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Jam Absensi</th>
                                    <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Geofence</th>
                                    <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Status</th>
                                    <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Keterangan</th>
                                    <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Image</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ich-line">
                                @foreach($records as $record)
                                    @php
                                        $nama  = $record->teacher?->user?->name ?? '-';
                                        $tipe  = $record->teacher?->tipe ?? '-';
                                        $stCfg = match($record->attendance_status) {
                                            'Hadir'             => ['label' => 'Hadir',            'bg' => 'bg-ich-success-soft', 'text' => 'text-ich-success'],
                                            'Izin'              => ['label' => 'Izin',             'bg' => 'bg-ich-purple-soft', 'text' => 'text-ich-purple'],
                                            'Sakit'             => ['label' => 'Sakit',            'bg' => 'bg-ich-error-soft', 'text' => 'text-ich-error'],
                                            'Tanpa Keterangan'  => ['label' => 'Tanpa Keterangan', 'bg' => 'bg-ich-error-soft', 'text' => 'text-ich-error'],
                                            'Diluar Jangkauan'  => ['label' => 'Diluar Jangkauan', 'bg' => 'bg-ich-warning-soft', 'text' => 'text-ich-warning'],
                                            default             => ['label' => $record->attendance_status, 'bg' => 'bg-ich-surface', 'text' => 'text-ich-ink-400'],
                                        };
                                    @endphp
                                    <tr class="hover:bg-ich-surface transition-colors"
                                        x-show="!searchHarian || '{{ strtolower($nama) }}'.includes(searchHarian.toLowerCase())"
                                        x-transition.opacity>
                                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $nama }}</td>
                                        <td class="px-4 py-3 font-sans text-ich-ink-600">{{ $tipe }}</td>
                                        <td class="px-4 py-3 font-sans text-ich-ink-600">
                                            {{ $record->created_at->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3 font-sans text-ich-ink-600">
                                            {{ $record->check_in_time ? $record->check_in_time->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($record->is_within_geofence === 'ya')
                                                <span class="text-xs font-ui font-bold text-ich-success">Dalam Area</span>
                                            @elseif($record->is_within_geofence === 'tidak')
                                                <span class="text-xs font-ui font-bold text-ich-error">Di Luar Area</span>
                                            @else
                                                <span class="text-xs text-ich-ink-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3" x-data="{ editing: false }">
                                            @if(! $isReadOnly)
                                                <div x-show="!editing" @click="editing = true" class="cursor-pointer group relative inline-block">
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-ui font-bold {{ $stCfg['bg'] }} {{ $stCfg['text'] }}">
                                                        {{ $stCfg['label'] }}
                                                    </span>
                                                    <span class="absolute -top-1 -right-1 hidden group-hover:block bg-white shadow rounded-full p-0.5 text-ich-teal">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    </span>
                                                </div>
                                                <form x-show="editing" method="POST" action="{{ route('admin.absensi-guru.update', $record->attendance_record_id) }}" class="flex items-center gap-1" x-cloak>
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" onchange="this.form.submit()" @click.away="editing = false" class="text-xs p-1 border border-ich-line rounded text-ich-ink-900 bg-white">
                                                        <option value="Hadir" {{ $record->attendance_status == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                                        <option value="Izin" {{ $record->attendance_status == 'Izin' ? 'selected' : '' }}>Izin</option>
                                                        <option value="Sakit" {{ $record->attendance_status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                                        <option value="Tanpa Keterangan" {{ $record->attendance_status == 'Tanpa Keterangan' ? 'selected' : '' }}>Tanpa Ket.</option>
                                                        <option value="Diluar Jangkauan" {{ $record->attendance_status == 'Diluar Jangkauan' ? 'selected' : '' }} disabled>Luar Jangkauan</option>
                                                    </select>
                                                </form>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-xs font-ui font-bold {{ $stCfg['bg'] }} {{ $stCfg['text'] }}">
                                                    {{ $stCfg['label'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-sans text-xs text-ich-ink-600">
                                            {{ $record->keterangan_izin ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($record->selfie_path)
                                                @php $selfieUrl = asset('storage/' . $record->selfie_path); @endphp
                                                <button type="button"
                                                        @click="photoUrl = '{{ $selfieUrl }}'"
                                                        class="block w-12 h-12 rounded-lg overflow-hidden border-2 border-ich-line hover:border-ich-teal transition-colors focus:outline-none">
                                                    <img src="{{ $selfieUrl }}" alt="Selfie {{ $nama }}"
                                                         class="w-full h-full object-cover">
                                                </button>
                                            @else
                                                <span class="text-xs text-ich-ink-300">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($records?->hasPages())
                        <div class="px-5 py-4 border-t border-ich-line">
                            {{ $records->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Form Input Absensi — hanya Admin --}}
        @if(! $isReadOnly)
        <div class="space-y-5">
            <div class="bg-white rounded-xl shadow-ich-card p-5">
                <h3 class="font-ui font-bold text-ich-ink-900 mb-4">Input Absensi Guru</h3>
                <p class="text-xs text-ich-ink-400 font-sans mb-4">
                    Catat absensi atas nama guru yang tidak bisa input sendiri.
                </p>

                <form method="POST" action="{{ route('admin.absensi-guru.store') }}" x-data="{ adminStatus: '' }">
                    @csrf

                    {{-- Pilih Guru --}}
                    <div class="mb-4">
                        <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Nama Guru</label>
                        <select name="teacher_id"
                                class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                       font-sans text-sm focus:outline-none focus:border-ich-teal">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->teacher_id }}">{{ $t->user?->name }} ({{ $t->tipe }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Status</label>
                        <select name="status" x-model="adminStatus"
                                class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                       font-sans text-sm focus:outline-none focus:border-ich-teal
                                       @error('status') border-ich-error @enderror">
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Tanpa Keterangan">Tanpa Keterangan</option>
                        </select>
                    </div>

                    {{-- Keterangan Izin --}}
                    <div class="mb-5" x-show="adminStatus === 'Izin'" x-transition>
                        <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Keterangan Izin</label>
                        <textarea name="keterangan_izin" rows="2" placeholder="Tuliskan alasan izin..."
                                  class="w-full px-3 py-2 bg-white border-2 border-ich-line rounded-ich-lg
                                         font-sans text-sm focus:outline-none focus:border-ich-teal resize-none"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                   rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                        Simpan Absensi
                    </button>
                </form>
            </div>

            {{-- Info Pengaturan --}}
            <div class="bg-ich-blue-soft rounded-xl p-4">
                <p class="font-ui font-bold text-xs text-ich-ink-600 mb-1">Pengaturan Geofence</p>
                <p class="font-sans text-xs text-ich-ink-500">
                    Untuk mengatur titik koordinat dan radius sekolah, buka
                    <a href="{{ route('admin.pengaturan.index') }}" class="text-ich-teal underline">Pengaturan</a>.
                </p>
            </div>
        </div>
        @endif
    </div>

    {{-- Rekap Absensi Per Guru --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden mt-6" x-data="{ searchBulanan: '' }">
        <div class="px-6 py-4 border-b border-ich-line">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-ich-pink-soft flex items-center justify-center">
                        <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-ui font-bold text-ich-ink-900">Rekap Absensi Per Guru</h2>
                        <p class="text-xs text-ich-ink-400 mt-0.5">Kehadiran per guru per bulan</p>
                    </div>
                </div>
                <form method="GET" action="{{ route('admin.absensi-guru.index') }}" class="flex items-center gap-2">
                    @if($filters['tanggal'] ?? false)
                        <input type="hidden" name="tanggal" value="{{ $filters['tanggal'] }}">
                    @endif
                    @if($filters['status'] ?? false)
                        <input type="hidden" name="status" value="{{ $filters['status'] }}">
                    @endif
                    <select name="guru" onchange="this.form.submit()"
                            class="border border-ich-line rounded-lg px-3 py-1.5 text-sm font-sans text-ich-ink-900 focus:outline-none focus:ring-2 focus:ring-ich-green/30">
                        <option value="">Pilih Guru</option>
                        @foreach($teachers as $guru)
                            <option value="{{ $guru->teacher_id }}" {{ $selectedGuru == $guru->teacher_id ? 'selected' : '' }}>
                                {{ $guru->user?->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @if($selectedGuru)
                        <input type="month" name="bulan" value="{{ $teacherRecap['bulan'] ?? now()->format('Y-m') }}"
                               onchange="this.form.submit()"
                               class="border border-ich-line rounded-lg px-3 py-1.5 text-sm font-sans text-ich-ink-900 focus:outline-none focus:ring-2 focus:ring-ich-green/30">
                    @endif
                </form>
            </div>
        </div>

        @if($selectedGuru && !empty($teacherRecap))
            <div class="px-6 py-3 bg-ich-surface flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-success-soft text-ich-success text-xs font-ui font-bold">
                        Hadir {{ $teacherRecap['summary']['hadir'] }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-info-soft text-ich-info text-xs font-ui font-bold">
                        Sakit {{ $teacherRecap['summary']['sakit'] }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-purple-soft text-ich-purple text-xs font-ui font-bold">
                        Izin {{ $teacherRecap['summary']['izin'] }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-error-soft text-ich-error text-xs font-ui font-bold">
                        Tanpa Keterangan {{ $teacherRecap['summary']['tanpa_keterangan'] }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-warning-soft text-ich-warning text-xs font-ui font-bold">
                        Luar Area {{ $teacherRecap['summary']['diluar_jangkauan'] ?? 0 }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-ich-ink-600 text-xs font-ui font-bold">
                        Total {{ $teacherRecap['summary']['total'] }} hari
                    </span>
                </div>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ich-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="searchBulanan" placeholder="Cari tanggal / status..."
                           class="pl-9 pr-3 py-1.5 w-56 border border-ich-line rounded-lg text-sm font-sans
                                  focus:outline-none focus:ring-2 focus:ring-ich-green/30">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-ich-surface">
                        <tr>
                            <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">No</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Tanggal</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Jam Masuk</th>
                            <th class="px-4 py-3 text-center font-ui font-bold text-xs text-ich-ink-500">Geofence</th>
                            <th class="px-4 py-3 text-center font-ui font-bold text-xs text-ich-ink-500">Status</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Keterangan</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Image</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ich-line">
                        @forelse($teacherRecap['records'] as $i => $rec)
                            @php
                                $recapNo = $teacherRecap['records']->firstItem() + $i;
                                $recTanggal = $rec->check_in_time?->translatedFormat('l, d M Y') ?? '-';
                                $recStatus  = $rec->attendance_status;
                                $stCfg = match($recStatus) {
                                    'Hadir'            => ['bg' => 'bg-ich-success-soft', 'text' => 'text-ich-success'],
                                    'Izin'             => ['bg' => 'bg-ich-purple-soft',  'text' => 'text-ich-purple'],
                                    'Sakit'            => ['bg' => 'bg-ich-error-soft',   'text' => 'text-ich-error'],
                                    'Tanpa Keterangan' => ['bg' => 'bg-ich-error-soft',   'text' => 'text-ich-error'],
                                    'Diluar Jangkauan' => ['bg' => 'bg-ich-warning-soft', 'text' => 'text-ich-warning'],
                                    default            => ['bg' => 'bg-ich-surface',      'text' => 'text-ich-ink-400'],
                                };
                            @endphp
                            <tr class="hover:bg-ich-surface transition-colors"
                                x-show="!searchBulanan || '{{ strtolower($recTanggal . ' ' . $recStatus) }}'.includes(searchBulanan.toLowerCase())"
                                x-transition.opacity>
                                <td class="px-4 py-3 text-ich-ink-400">{{ $recapNo }}</td>
                                <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                    {{ $recTanggal }}
                                </td>
                                <td class="px-4 py-3 font-sans text-ich-ink-600">
                                    {{ $rec->check_in_time?->format('H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($rec->is_within_geofence === 'ya')
                                        <span class="text-xs font-ui font-bold text-ich-success">Dalam Area</span>
                                    @elseif($rec->is_within_geofence === 'tidak')
                                        <span class="text-xs font-ui font-bold text-ich-error">Di Luar Area</span>
                                    @else
                                        <span class="text-xs text-ich-ink-300">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center" x-data="{ editing: false }">
                                    @if(! $isReadOnly)
                                        <div x-show="!editing" @click="editing = true" class="cursor-pointer group relative inline-block">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-ui font-bold {{ $stCfg['bg'] }} {{ $stCfg['text'] }}">
                                                {{ $recStatus === 'Tanpa Keterangan' ? 'Tanpa Ket.' : $recStatus }}
                                            </span>
                                            <span class="absolute -top-1 -right-1 hidden group-hover:block bg-white shadow rounded-full p-0.5 text-ich-teal">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </span>
                                        </div>
                                        <form x-show="editing" method="POST" action="{{ route('admin.absensi-guru.update', $rec->attendance_record_id) }}" class="flex items-center gap-1 justify-center" x-cloak>
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" @click.away="editing = false" class="text-xs p-1 border border-ich-line rounded text-ich-ink-900 bg-white">
                                                <option value="Hadir" {{ $rec->attendance_status == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                                <option value="Izin" {{ $rec->attendance_status == 'Izin' ? 'selected' : '' }}>Izin</option>
                                                <option value="Sakit" {{ $rec->attendance_status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                                <option value="Tanpa Keterangan" {{ $rec->attendance_status == 'Tanpa Keterangan' ? 'selected' : '' }}>Tanpa Ket.</option>
                                                <option value="Diluar Jangkauan" {{ $rec->attendance_status == 'Diluar Jangkauan' ? 'selected' : '' }} disabled>Luar Jangkauan</option>
                                            </select>
                                        </form>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-ui font-bold {{ $stCfg['bg'] }} {{ $stCfg['text'] }}">
                                            {{ $recStatus === 'Tanpa Keterangan' ? 'Tanpa Ket.' : $recStatus }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-sans text-xs text-ich-ink-600">
                                    {{ $rec->keterangan_izin ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($rec->selfie_path)
                                        @php $selfieUrl = asset('storage/' . $rec->selfie_path); @endphp
                                        <button type="button"
                                                @click="photoUrl = '{{ $selfieUrl }}'"
                                                class="block w-12 h-12 rounded-lg overflow-hidden border-2 border-ich-line hover:border-ich-teal transition-colors focus:outline-none">
                                            <img src="{{ $selfieUrl }}" alt="Selfie"
                                                 class="w-full h-full object-cover">
                                        </button>
                                    @else
                                        <span class="text-xs text-ich-ink-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                    Belum ada data absensi pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($teacherRecap['records']->hasPages())
                <div class="px-5 py-4 border-t border-ich-line">
                    {{ $teacherRecap['records']->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-10 text-center text-ich-ink-300 font-sans">
                Pilih guru untuk melihat rekap absensi bulanan.
            </div>
        @endif
    </div>

</x-main-layout>
