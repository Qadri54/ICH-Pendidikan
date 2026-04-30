<x-main-layout title="Detail Pendaftaran">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.pendaftaran.index') }}"
               class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Detail Pendaftaran</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Detail info --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-ich-card p-6 space-y-3">
            <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mb-2">Data Orang Tua</h2>
            @foreach([
                ['Nama',   $pendaftaran->user?->name ?? '-'],
                ['Email',  $pendaftaran->user?->email ?? '-'],
                ['No HP',  $pendaftaran->user?->no_hp ?? '-'],
            ] as [$label, $value])
                <div class="flex gap-4 py-1.5 border-b border-ich-line last:border-0">
                    <div class="w-28 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                    <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                </div>
            @endforeach

            <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mt-4 mb-2">Data Anak</h2>
            @foreach([
                ['Tempat Lahir',  $pendaftaran->tempat_lahir ?? '-'],
                ['Tanggal Lahir', $pendaftaran->tanggal_lahir ? \Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->format('d M Y') : '-'],
                ['Jenis Kelamin', $pendaftaran->jenis_kelamin ?? '-'],
                ['Nama Ayah',     $pendaftaran->nama_ayah ?? '-'],
                ['Nama Ibu',      $pendaftaran->nama_ibu ?? '-'],
                ['Alamat',        $pendaftaran->alamat ?? '-'],
            ] as [$label, $value])
                <div class="flex gap-4 py-1.5 border-b border-ich-line last:border-0">
                    <div class="w-28 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                    <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        {{-- Status update --}}
        <div class="bg-white rounded-xl shadow-ich-card p-6">
            <h2 class="font-ui font-bold text-ich-ink-900 mb-4">Update Status</h2>
            @php
                $statusColor = match($pendaftaran->status) {
                    'Diterima' => 'bg-[#D1FAE5] text-[#009966]',
                    'Ditolak'  => 'bg-[#FEE2E2] text-ich-error',
                    default    => 'bg-[#FEF5DC] text-[#E09F17]',
                };
            @endphp
            <div class="mb-4">
                <span class="px-3 py-1.5 font-ui font-bold text-sm rounded-full {{ $statusColor }}">
                    {{ $pendaftaran->status }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.pendaftaran.update', $pendaftaran) }}" class="space-y-3">
                @csrf @method('PATCH')
                <select name="status"
                        class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="Menunggu" {{ $pendaftaran->status === 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="Diterima" {{ $pendaftaran->status === 'Diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="Ditolak"  {{ $pendaftaran->status === 'Ditolak'  ? 'selected' : '' }}>Ditolak</option>
                </select>
                <button type="submit"
                        class="w-full py-2.5 bg-ich-teal text-white font-ui font-bold text-sm
                               rounded-ich-lg shadow-ich-btn hover:bg-ich-teal-dark transition-colors">
                    Simpan Status
                </button>
            </form>
        </div>

    </div>

</x-main-layout>
