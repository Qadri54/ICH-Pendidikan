<x-main-layout title="Rekap Absensi Siswa">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Rekap Absensi Siswa</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Ringkasan kehadiran per bulan</p>
        </div>
        <a href="{{ route('admin.absensi.index') }}"
           class="text-sm font-ui font-bold text-ich-teal hover:underline">
            ← Absensi Harian
        </a>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <select name="class_id"
                class="h-10 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none focus:border-ich-teal">
            <option value="">-- Pilih Kelas --</option>
            @foreach($classes as $k)
                <option value="{{ $k->class_id }}" {{ $selectedClass == $k->class_id ? 'selected' : '' }}>
                    {{ $k->nama_kelas }}
                </option>
            @endforeach
        </select>
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

    @if($selectedClass && $classroom)
        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="px-6 py-4 border-b border-ich-line">
                <h2 class="font-ui font-bold text-ich-ink-900">
                    {{ $classroom->nama_kelas }} —
                    {{ \Carbon\Carbon::create($selectedYear, $selectedMonth)->translatedFormat('F Y') }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-ich-green text-white">
                        <tr>
                            <th class="px-4 py-3 text-left font-ui font-bold w-12">No</th>
                            <th class="px-4 py-3 text-left font-ui font-bold">Nama Siswa</th>
                            <th class="px-4 py-3 text-center font-ui font-bold">Hadir</th>
                            <th class="px-4 py-3 text-center font-ui font-bold">Izin</th>
                            <th class="px-4 py-3 text-center font-ui font-bold">Sakit</th>
                            <th class="px-4 py-3 text-center font-ui font-bold">Tanpa Ket.</th>
                            <th class="px-4 py-3 text-center font-ui font-bold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ich-line">
                        @forelse($recap as $i => $item)
                            @php $total = ($item['hadir'] ?? 0) + $item['izin'] + $item['sakit'] + $item['tanpa_keterangan']; @endphp
                            <tr class="hover:bg-[#F5F6FA]">
                                <td class="px-4 py-3 text-ich-ink-400">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $item['nama'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 bg-[#D1FAE5] text-[#009966] font-ui font-bold text-xs rounded-full">{{ $item['hadir'] ?? 0 }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 bg-[#EDE9FE] text-[#8B5CF6] font-ui font-bold text-xs rounded-full">{{ $item['izin'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 bg-[#FEE2E2] text-ich-error font-ui font-bold text-xs rounded-full">{{ $item['sakit'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 bg-[#FEF5DC] text-[#E09F17] font-ui font-bold text-xs rounded-full">{{ $item['tanpa_keterangan'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-ui font-bold text-ich-ink-900">{{ $total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                    Tidak ada data absensi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @elseif(!$selectedClass)
        <div class="bg-white rounded-xl shadow-ich-card p-10 text-center">
            <p class="text-ich-ink-300 font-sans">Pilih kelas untuk melihat rekap absensi.</p>
        </div>
    @endif

</x-main-layout>
