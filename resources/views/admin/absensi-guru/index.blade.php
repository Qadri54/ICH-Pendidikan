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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

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
            <div class="bg-white rounded-xl shadow-ich-card overflow-hidden" x-data="{ photoUrl: '' }">

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

                <div class="px-5 py-4 border-b border-ich-line">
                    <h2 class="font-ui font-bold text-ich-ink-900">Rekap Absensi</h2>
                    <p class="text-xs text-ich-ink-400 mt-0.5">{{ $records->count() }} data ditemukan</p>
                </div>

                @if($records->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <x-ich-icon name="calendar" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
                        <p class="font-sans text-ich-ink-400">Belum ada data absensi.</p>
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
                                    <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Foto Selfie</th>
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
                                            'Tanpa Keterangan'  => ['label' => 'Tanpa Keterangan', 'bg' => 'bg-ich-warning-soft', 'text' => 'text-ich-warning'],
                                            default             => ['label' => $record->attendance_status, 'bg' => 'bg-ich-surface', 'text' => 'text-ich-ink-400'],
                                        };
                                    @endphp
                                    <tr class="hover:bg-ich-surface transition-colors">
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
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-ui font-bold
                                                         {{ $stCfg['bg'] }} {{ $stCfg['text'] }}">
                                                {{ $stCfg['label'] }}
                                            </span>
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

                <form method="POST" action="{{ route('admin.absensi-guru.store') }}">
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
                    <div class="mb-5">
                        <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Status</label>
                        <select name="status"
                                class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                       font-sans text-sm focus:outline-none focus:border-ich-teal
                                       @error('status') border-ich-error @enderror">
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Tanpa Keterangan">Tanpa Keterangan</option>
                        </select>
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

</x-main-layout>
