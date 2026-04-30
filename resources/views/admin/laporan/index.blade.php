<x-main-layout title="Laporan">

    <div class="mb-6">
        <h1 class="text-2xl font-display font-bold text-ich-ink-900">Laporan</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">Ringkasan data sistem</p>
    </div>

    {{-- Stats overview --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Total Siswa</div>
            <div class="text-3xl font-display font-bold text-ich-green">{{ $stats['total_siswa'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Tagihan Berjalan</div>
            <div class="text-3xl font-display font-bold text-ich-error">{{ $stats['tagihan_berjalan'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Tagihan Lunas</div>
            <div class="text-3xl font-display font-bold text-[#009966]">{{ $stats['tagihan_lunas'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <div class="text-ich-ink-400 text-xs font-sans mb-1">Total Tunggakan</div>
            <div class="text-2xl font-display font-bold text-ich-ink-900">
                Rp {{ number_format($stats['total_tagihan'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Export section --}}
    <div class="bg-white rounded-xl shadow-ich-card p-6">
        <h2 class="font-ui font-bold text-ich-ink-900 mb-4">Export Laporan</h2>
        <p class="text-sm text-ich-ink-400 font-sans">Fitur export laporan akan tersedia segera.</p>
    </div>

</x-main-layout>
