@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Bukti Pembayaran SPP">
<div x-data="{ showImage: false, imageUrl: '', imageName: '' }">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Bukti Pembayaran SPP</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">
                <span class="font-bold text-ich-yellow">{{ $pendingCount }}</span> menunggu konfirmasi
            </p>
        </div>
        <a href="{{ route('admin.keuangan.index') }}"
           class="text-sm font-ui font-bold text-ich-teal hover:underline">
            &larr; Kembali ke Keuangan
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..."
               class="flex-1 max-w-xs h-10 px-3.5 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none focus:border-ich-teal">
        <select name="status" class="h-10 px-3 bg-white border border-ich-line rounded-ich-md font-sans text-sm focus:outline-none">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Disetujui</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="h-10 px-4 bg-ich-teal text-white font-ui font-bold text-sm rounded-ich-md hover:bg-ich-teal-dark">Cari</button>
    </form>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-ich-surface">
                <tr>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Siswa</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Periode</th>
                    <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Jumlah</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Bank</th>
                    <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Tanggal</th>
                    <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Bukti</th>
                    <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Status</th>
                    @if(! $isReadOnly)
                        <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                @forelse($payments as $p)
                    @php
                        $statusCfg = match($p->status) {
                            'pending'   => ['label' => 'Menunggu',  'class' => 'bg-ich-warning-soft text-ich-warning'],
                            'paid'      => ['label' => 'Disetujui', 'class' => 'bg-ich-success-soft text-ich-success'],
                            'cancelled' => ['label' => 'Ditolak',   'class' => 'bg-ich-error-soft text-ich-error'],
                            default     => ['label' => $p->status,  'class' => 'bg-gray-100 text-gray-600'],
                        };
                    @endphp
                    <tr class="hover:bg-ich-surface transition-colors">
                        <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">{{ $p->student?->nama_siswa ?? '-' }}</td>
                        <td class="px-4 py-3 text-ich-ink-500">{{ $p->student?->classRoom?->nama_kelas ?? '-' }}</td>
                        <td class="px-4 py-3 text-ich-ink-600">
                            {{ $p->invoice?->tanggal_tahun ? \Carbon\Carbon::parse($p->invoice->tanggal_tahun)->translatedFormat('F Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right font-ui font-semibold">
                            Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-ich-ink-600">{{ $p->nama_bank }}</td>
                        <td class="px-4 py-3 text-ich-ink-500">
                            {{ $p->payment_date?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($p->gambar_bukti_pembayaran)
                                <button type="button"
                                        @click="imageUrl = '{{ asset('storage/' . $p->gambar_bukti_pembayaran) }}'; imageName = '{{ $p->student?->nama_siswa ?? '' }}'; showImage = true"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-600 font-ui font-bold text-xs rounded hover:bg-blue-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Lihat
                                </button>
                            @else
                                <span class="text-ich-ink-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 font-ui font-bold text-xs rounded-full {{ $statusCfg['class'] }}">
                                {{ $statusCfg['label'] }}
                            </span>
                        </td>
                        @if(! $isReadOnly)
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    @if($p->status === 'pending')
                                        <form method="POST" action="{{ route('admin.keuangan.pembayaran.approve', $p->payment_id) }}"
                                              onsubmit="return confirm('Setujui pembayaran ini?')">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1 bg-ich-success-soft text-ich-success font-ui font-bold text-xs rounded hover:bg-ich-green hover:text-white transition-colors">
                                                Setujui
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.keuangan.pembayaran.reject', $p->payment_id) }}"
                                              onsubmit="return confirm('Tolak pembayaran ini?')">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1 bg-ich-error-soft text-ich-error font-ui font-bold text-xs rounded hover:bg-ich-error hover:text-white transition-colors">
                                                Tolak
                                            </button>
                                        </form>
                                    @elseif($p->status === 'paid')
                                        <span class="text-xs text-ich-ink-400">{{ $p->approvedBy?->name ?? '-' }}</span>
                                    @else
                                        <span class="text-xs text-ich-ink-300">-</span>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isReadOnly ? 8 : 9 }}" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                            Belum ada bukti pembayaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $payments->links() }}</div>

    {{-- Modal Lihat Bukti --}}
    <x-admin-modal show="showImage" title="Bukti Pembayaran" maxWidth="2xl">
        <p class="text-sm text-ich-ink-600 mb-3 font-ui font-semibold" x-text="imageName"></p>
        <div class="flex justify-center">
            <img :src="imageUrl" alt="Bukti Pembayaran" class="max-w-full max-h-[70vh] rounded-lg shadow">
        </div>
        <div class="flex justify-end mt-4">
            <button type="button" @click="showImage = false"
                    class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">
                Tutup
            </button>
        </div>
    </x-admin-modal>

</div>
</x-main-layout>
