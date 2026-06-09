<x-main-layout title="Rekap Absensi Guru">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Rekap Absensi Guru</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Ringkasan kehadiran per bulan</p>
        </div>
        <a href="{{ route('admin.absensi-guru.index') }}"
           class="text-sm font-ui font-bold text-ich-teal hover:underline">
            ← Absensi Harian
        </a>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <select name="month"
                class="h-10 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endfor
        </select>
        <input type="number" name="year" value="{{ $selectedYear }}" min="2020" max="2099"
               class="h-10 w-24 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none">
        <button type="submit"
                class="h-10 px-4 bg-ich-teal text-white font-ui font-bold text-sm rounded-ich-md hover:bg-ich-teal-dark">
            Tampilkan
        </button>
    </form>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="px-6 py-4 border-b border-ich-line">
            <h2 class="font-ui font-bold text-ich-ink-900">
                {{ \Carbon\Carbon::create($selectedYear, $selectedMonth)->translatedFormat('F Y') }}
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-ich-surface">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600 w-12">No</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Guru</th>
                        <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Hadir</th>
                        <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Izin</th>
                        <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Sakit</th>
                        <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Tanpa Ket.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($recap as $i => $item)
                        <tr class="hover:bg-ich-surface transition-colors">
                            <td class="px-4 py-3 text-ich-ink-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $item['nama'] }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 bg-ich-success-soft text-ich-success font-ui font-bold text-xs rounded-full">{{ $item['hadir'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 bg-ich-purple-soft text-ich-purple font-ui font-bold text-xs rounded-full">{{ $item['izin'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 bg-ich-error-soft text-ich-error font-ui font-bold text-xs rounded-full">{{ $item['sakit'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 bg-ich-warning-soft text-ich-warning font-ui font-bold text-xs rounded-full">{{ $item['tanpa_keterangan'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                Tidak ada data absensi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($recap->isNotEmpty())
                    <tfoot class="bg-ich-surface">
                        <tr>
                            <td colspan="2" class="px-4 py-3 font-ui font-bold text-ich-ink-900">Total</td>
                            <td class="px-4 py-3 text-center font-ui font-bold text-ich-success">{{ $recap->sum('hadir') }}</td>
                            <td class="px-4 py-3 text-center font-ui font-bold text-ich-purple">{{ $recap->sum('izin') }}</td>
                            <td class="px-4 py-3 text-center font-ui font-bold text-ich-error">{{ $recap->sum('sakit') }}</td>
                            <td class="px-4 py-3 text-center font-ui font-bold text-ich-warning">{{ $recap->sum('tanpa_keterangan') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

</x-main-layout>
