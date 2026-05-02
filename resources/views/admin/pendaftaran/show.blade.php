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
                    'accepted' => 'bg-[#D1FAE5] text-[#009966]',
                    'rejected' => 'bg-[#FEE2E2] text-ich-error',
                    default    => 'bg-[#FEF5DC] text-[#E09F17]',
                };
                $statusLabel = match($pendaftaran->status) {
                    'accepted' => 'Diterima',
                    'rejected' => 'Ditolak',
                    default    => 'Menunggu',
                };
            @endphp
            <div class="mb-4">
                <span class="px-3 py-1.5 font-ui font-bold text-sm rounded-full {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            </div>

            @if($pendaftaran->status === 'pending')
                <form method="POST" action="{{ route('admin.pendaftaran.update', $pendaftaran) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <div class="flex flex-col gap-2">
                        <button type="submit" name="status" value="accepted"
                                onclick="return confirm('Terima pendaftaran ini? Siswa akan dibuat dan biaya pendaftaran akan digenerate otomatis.')"
                                class="w-full py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                       rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                            Terima Pendaftaran
                        </button>
                        <button type="submit" name="status" value="rejected"
                                onclick="return confirm('Tolak pendaftaran ini?')"
                                class="w-full py-2.5 bg-white border-2 border-ich-error text-ich-error font-ui font-bold text-sm
                                       rounded-ich-lg hover:bg-ich-error hover:text-white transition-colors">
                            Tolak Pendaftaran
                        </button>
                    </div>
                </form>
            @else
                <p class="text-sm text-ich-ink-400 font-sans">Status sudah final, tidak dapat diubah.</p>
            @endif
        </div>

    </div>

</x-main-layout>
