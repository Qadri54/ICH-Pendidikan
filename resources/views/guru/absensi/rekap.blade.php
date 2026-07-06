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
                Tanpa Ket. {{ $summary['alpha'] }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-ich-ink-600 text-xs font-ui font-bold">
                Total {{ $summary['total'] }} data
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
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                            <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Status</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ich-line">
                        @forelse($records as $i => $rec)
                            <tr class="hover:bg-ich-surface transition-colors">
                                <td class="px-4 py-3 text-ich-ink-400">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                    {{ $rec->created_at->translatedFormat('l, d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-ich-ink-600">
                                    {{ $rec->student?->nama_siswa ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @switch($rec->status)
                                        @case('hadir')
                                            <span class="px-2.5 py-1 bg-ich-success-soft text-ich-success font-ui font-bold text-xs rounded-full">Hadir</span>
                                            @break
                                        @case('sakit')
                                            <span class="px-2.5 py-1 bg-ich-info-soft text-ich-info font-ui font-bold text-xs rounded-full">Sakit</span>
                                            @break
                                        @case('izin')
                                            <span class="px-2.5 py-1 bg-ich-purple-soft text-ich-purple font-ui font-bold text-xs rounded-full">Izin</span>
                                            @break
                                        @case('tanpa keterangan')
                                            <span class="px-2.5 py-1 bg-ich-error-soft text-ich-error font-ui font-bold text-xs rounded-full">Tanpa Ket.</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-3 text-ich-ink-600 text-xs">
                                    {{ $rec->keterangan_izin ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                    Belum ada data absensi pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</x-main-layout>
