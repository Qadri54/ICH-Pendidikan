<x-main-layout title="Laporan">

    <div class="mb-6">
        <h1 class="text-2xl font-display font-bold text-ich-ink-900">Laporan</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">Ringkasan data sistem</p>
    </div>

    {{-- Stats: Orang --}}
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Total Siswa</div>
            <div class="text-3xl font-display font-bold text-ich-green">{{ $stats['total_siswa'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Total Guru</div>
            <div class="text-3xl font-display font-bold text-ich-teal">{{ $stats['total_guru'] }}</div>
        </div>
    </div>

    {{-- Stats: Total Pendapatan (highlight) --}}
    <div class="bg-ich-green rounded-xl shadow-ich-card p-5 mb-4">
        <div class="text-white/70 text-xs font-sans mb-1">Total Pendapatan (SPP + Pendaftaran)</div>
        <div class="text-3xl font-display font-bold text-white">
            Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}
        </div>
        <div class="flex gap-4 mt-3">
            <div>
                <div class="text-white/60 text-xs font-sans">SPP Terkumpul</div>
                <div class="text-base font-display font-bold text-white">
                    Rp {{ number_format($stats['total_spp_terkumpul'], 0, ',', '.') }}
                </div>
            </div>
            <div class="w-px bg-white/20"></div>
            <div>
                <div class="text-white/60 text-xs font-sans">Biaya Pendaftaran</div>
                <div class="text-base font-display font-bold text-white">
                    Rp {{ number_format($stats['total_pendaftaran'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Stats: SPP & Tabungan --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Tagihan SPP Berjalan</div>
            <div class="text-3xl font-display font-bold text-ich-error">{{ $stats['tagihan_berjalan'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Tagihan SPP Lunas</div>
            <div class="text-3xl font-display font-bold text-[#009966]">{{ $stats['tagihan_lunas'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Pendaftaran Lunas</div>
            <div class="text-3xl font-display font-bold text-[#8B5CF6]">{{ $lunasPendaftaran->count() }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Total Tabungan</div>
            <div class="text-xl font-display font-bold text-ich-teal">
                Rp {{ number_format($stats['total_tabungan'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Tabel Lunas Pendaftaran --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-ich-line flex items-center justify-between">
            <div>
                <h2 class="font-ui font-bold text-ich-ink-900">Pelunasan Biaya Pendaftaran</h2>
                <p class="text-xs text-ich-ink-400 mt-0.5">{{ $lunasPendaftaran->count() }} siswa telah lunas biaya pendaftaran</p>
            </div>
            <a href="{{ route('admin.pembayaran-pendaftaran.index') }}?status=approved"
               class="text-xs font-ui font-bold text-ich-teal hover:underline">
                Lihat Semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F6FA]">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Ayah</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                        <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Total Dibayar</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($lunasPendaftaran as $fee)
                        <tr class="hover:bg-[#F5F6FA]">
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                {{ $fee->student?->nama_siswa ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-ich-ink-600">
                                {{ $fee->student?->nama_ayah ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-[#EDE9FE] text-[#8B5CF6] font-ui font-bold text-xs rounded-full">
                                    {{ $fee->student?->classRoom?->nama_kelas ?? 'Belum Ditempatkan' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-ui font-semibold text-[#009966]">
                                Rp {{ number_format($fee->total_jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-[#D1FAE5] text-[#009966] font-ui font-bold text-xs rounded-full">
                                    Lunas
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                Belum ada siswa yang telah melunasi biaya pendaftaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabel Pembayaran SPP --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-ich-line flex items-center justify-between">
            <div>
                <h2 class="font-ui font-bold text-ich-ink-900">Pembayaran SPP</h2>
                <p class="text-xs text-ich-ink-400 mt-0.5">{{ $pembayaranSpp->count() }} transaksi lunas terbaru</p>
            </div>
            <a href="{{ route('admin.keuangan.index') }}?status=paid"
               class="text-xs font-ui font-bold text-ich-teal hover:underline">
                Lihat Semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F6FA]">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Ayah</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Periode</th>
                        <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($pembayaranSpp as $inv)
                        <tr class="hover:bg-[#F5F6FA]">
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                {{ $inv->student?->nama_siswa ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-ich-ink-600">
                                {{ $inv->student?->nama_ayah ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-[#E8F5EA] text-ich-green font-ui font-bold text-xs rounded-full">
                                    {{ $inv->student?->classRoom?->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-ich-ink-500">
                                {{ $inv->tanggal_tahun?->translatedFormat('F Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-ui font-semibold text-ich-green">
                                Rp {{ number_format($inv->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                Belum ada pembayaran SPP yang lunas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Export Laporan --}}
    <div class="bg-white rounded-xl shadow-ich-card p-6" x-data="{ tab: 'keuangan' }">
        <h2 class="font-ui font-bold text-ich-ink-900 mb-4">Export Laporan</h2>

        {{-- Tab buttons --}}
        <div class="flex gap-2 mb-5">
            <button @click="tab = 'keuangan'" :class="tab === 'keuangan' ? 'bg-ich-green text-white' : 'bg-[#F5F6FA] text-ich-ink-500'"
                    class="px-4 py-2 rounded-lg text-xs font-ui font-bold transition-colors">Keuangan</button>
            <button @click="tab = 'absensi-siswa'" :class="tab === 'absensi-siswa' ? 'bg-ich-green text-white' : 'bg-[#F5F6FA] text-ich-ink-500'"
                    class="px-4 py-2 rounded-lg text-xs font-ui font-bold transition-colors">Absensi Siswa</button>
            <button @click="tab = 'absensi-guru'" :class="tab === 'absensi-guru' ? 'bg-ich-green text-white' : 'bg-[#F5F6FA] text-ich-ink-500'"
                    class="px-4 py-2 rounded-lg text-xs font-ui font-bold transition-colors">Absensi Guru</button>
        </div>

        {{-- Keuangan export --}}
        <div x-show="tab === 'keuangan'" x-cloak>
            <p class="text-sm text-ich-ink-400 font-sans mb-4">Download laporan keuangan SPP lengkap.</p>
            <div class="flex gap-3">
                <a href="{{ route('admin.laporan.export.keuangan-pdf') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity">
                    <x-ich-icon name="document" :size="16" color="currentColor"/> PDF
                </a>
                <a href="{{ route('admin.laporan.export.keuangan-csv') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-ich-teal text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity">
                    <x-ich-icon name="document" :size="16" color="currentColor"/> CSV
                </a>
            </div>
        </div>

        {{-- Absensi Siswa export --}}
        <div x-show="tab === 'absensi-siswa'" x-cloak>
            <form id="formAbsensiSiswa" class="flex flex-wrap items-end gap-3 mb-4">
                <div>
                    <label class="block text-xs font-ui font-bold text-ich-ink-500 mb-1">Kelas</label>
                    <select name="class_id" required
                            class="h-10 px-3 bg-[#F9FAFB] border-2 border-ich-line rounded-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                        <option value="">Pilih Kelas</option>
                        @foreach($classes as $kelas)
                            <option value="{{ $kelas->class_id }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-ui font-bold text-ich-ink-500 mb-1">Bulan</label>
                    <select name="month" required
                            class="h-10 px-3 bg-[#F9FAFB] border-2 border-ich-line rounded-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m === now()->month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-ui font-bold text-ich-ink-500 mb-1">Tahun</label>
                    <input type="number" name="year" value="{{ now()->year }}" min="2020" required
                           class="h-10 w-24 px-3 bg-[#F9FAFB] border-2 border-ich-line rounded-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                </div>
            </form>
            <div class="flex gap-3">
                <button onclick="exportAbsensiSiswa('pdf')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity">
                    <x-ich-icon name="document" :size="16" color="currentColor"/> PDF
                </button>
                <button onclick="exportAbsensiSiswa('csv')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-ich-teal text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity">
                    <x-ich-icon name="document" :size="16" color="currentColor"/> CSV
                </button>
            </div>
        </div>

        {{-- Absensi Guru export --}}
        <div x-show="tab === 'absensi-guru'" x-cloak>
            <form id="formAbsensiGuru" class="flex flex-wrap items-end gap-3 mb-4">
                <div>
                    <label class="block text-xs font-ui font-bold text-ich-ink-500 mb-1">Bulan</label>
                    <select name="month" required
                            class="h-10 px-3 bg-[#F9FAFB] border-2 border-ich-line rounded-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m === now()->month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-ui font-bold text-ich-ink-500 mb-1">Tahun</label>
                    <input type="number" name="year" value="{{ now()->year }}" min="2020" required
                           class="h-10 w-24 px-3 bg-[#F9FAFB] border-2 border-ich-line rounded-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                </div>
            </form>
            <div class="flex gap-3">
                <button onclick="exportAbsensiGuru('pdf')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-ich-error text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity">
                    <x-ich-icon name="document" :size="16" color="currentColor"/> PDF
                </button>
                <button onclick="exportAbsensiGuru('csv')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-ich-teal text-white font-ui font-bold text-xs rounded-lg hover:opacity-90 transition-opacity">
                    <x-ich-icon name="document" :size="16" color="currentColor"/> CSV
                </button>
            </div>
        </div>
    </div>

    <script>
        function exportAbsensiSiswa(format) {
            const form = document.getElementById('formAbsensiSiswa');
            const classId = form.querySelector('[name=class_id]').value;
            const month = form.querySelector('[name=month]').value;
            const year = form.querySelector('[name=year]').value;
            if (!classId) { alert('Pilih kelas terlebih dahulu'); return; }
            const url = format === 'pdf'
                ? '{{ route("admin.laporan.export.absensi-siswa-pdf") }}'
                : '{{ route("admin.laporan.export.absensi-siswa-csv") }}';
            window.location.href = url + '?class_id=' + classId + '&year=' + year + '&month=' + month;
        }

        function exportAbsensiGuru(format) {
            const form = document.getElementById('formAbsensiGuru');
            const month = form.querySelector('[name=month]').value;
            const year = form.querySelector('[name=year]').value;
            const url = format === 'pdf'
                ? '{{ route("admin.laporan.export.absensi-guru-pdf") }}'
                : '{{ route("admin.laporan.export.absensi-guru-csv") }}';
            window.location.href = url + '?year=' + year + '&month=' + month;
        }
    </script>

</x-main-layout>
