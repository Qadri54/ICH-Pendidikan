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

    {{-- Summary pills --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-success-soft text-ich-success text-xs font-ui font-bold">
            Hadir {{ $summary['hadir'] }}
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-info-soft text-ich-info text-xs font-ui font-bold">
            Sakit {{ $summary['sakit'] }}
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-purple-soft text-ich-purple text-xs font-ui font-bold">
            Izin {{ $summary['izin'] }}
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ich-error-soft text-ich-error text-xs font-ui font-bold">
            Tanpa Ket. {{ $summary['tanpa_keterangan'] }}
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-ich-ink-600 text-xs font-ui font-bold">
            Total {{ $summary['total'] }} hari
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
                    @forelse($records as $i => $rec)
                        <tr class="hover:bg-ich-surface transition-colors" x-data="{ show: false }">
                            <td class="px-4 py-3 text-ich-ink-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                {{ $rec->created_at->translatedFormat('l, d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-ich-ink-600">
                                {{ $rec->check_in_time?->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @switch($rec->attendance_status)
                                    @case('Hadir')
                                        <span class="px-2.5 py-1 bg-ich-success-soft text-ich-success font-ui font-bold text-xs rounded-full">Hadir</span>
                                        @break
                                    @case('Sakit')
                                        <span class="px-2.5 py-1 bg-ich-info-soft text-ich-info font-ui font-bold text-xs rounded-full">Sakit</span>
                                        @break
                                    @case('Izin')
                                        <span class="px-2.5 py-1 bg-ich-purple-soft text-ich-purple font-ui font-bold text-xs rounded-full">Izin</span>
                                        @break
                                    @case('Tanpa Keterangan')
                                        <span class="px-2.5 py-1 bg-ich-error-soft text-ich-error font-ui font-bold text-xs rounded-full">Tanpa Ket.</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-ich-ink-600 text-xs">
                                @if($rec->attendance_status === 'Izin')
                                    {{ $rec->keterangan_izin ?? '-' }}
                                @elseif($rec->attendance_status === 'Hadir')
                                    {{ $rec->is_within_geofence === 'ya' ? 'Dalam area' : 'Di luar area' }} · ±{{ $rec->check_in_accuracy ? round((float) $rec->check_in_accuracy) : '-' }}m
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($rec->selfie_path)
                                    <button @click="show = !show" type="button" class="text-ich-teal hover:text-ich-green text-xs font-ui font-bold">
                                        <span x-text="show ? 'Tutup' : 'Lihat'"></span>
                                    </button>
                                    <div x-show="show" x-collapse class="mt-2">
                                        <img src="{{ asset('storage/' . $rec->selfie_path) }}" alt="Selfie"
                                             class="w-16 h-16 rounded-lg object-cover border border-ich-line mx-auto">
                                    </div>
                                @else
                                    <span class="text-ich-ink-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                Belum ada data absensi pada bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-main-layout>
