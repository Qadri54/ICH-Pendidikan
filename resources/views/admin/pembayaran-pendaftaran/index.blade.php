@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Pembayaran Pendaftaran">
<div x-data="{ reject: { show: false, name: '', action: '' } }"
     @open-reject.window="reject = { show: true, ...$event.detail }">

    <div class="mb-6 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-ich-purple-soft flex items-center justify-center">
            <svg class="w-5 h-5 text-ich-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Pembayaran Pendaftaran</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Kelola tagihan dan bukti pembayaran biaya pendaftaran siswa</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="mb-5 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama siswa..."
               class="h-10 px-3.5 border-2 border-ich-line rounded-ich-lg font-sans text-sm
                      focus:outline-none focus:border-ich-teal bg-white w-56">

        <select name="status"
                class="h-10 px-3.5 border-2 border-ich-line rounded-ich-lg font-sans text-sm
                       focus:outline-none focus:border-ich-teal bg-white">
            <option value="">Semua Status</option>
            <option value="unpaid"       {{ request('status') === 'unpaid'       ? 'selected' : '' }}>Belum Bayar</option>
            <option value="installments" {{ request('status') === 'installments' ? 'selected' : '' }}>Cicilan</option>
            <option value="paid"         {{ request('status') === 'paid'         ? 'selected' : '' }}>Lunas</option>
        </select>

        <button type="submit"
                class="h-10 px-5 bg-ich-green text-white font-ui font-bold text-sm
                       rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
            Filter
        </button>

        @if(request('search') || request('status'))
            <a href="{{ route('admin.pembayaran-pendaftaran.index') }}"
               class="h-10 px-5 bg-white border-2 border-ich-line text-ich-ink-600 font-ui font-bold text-sm
                      rounded-ich-lg hover:bg-gray-50 transition-colors flex items-center">
                Reset
            </a>
        @endif
    </form>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-ich-surface">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Siswa</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Total Tagihan</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Sudah Dibayar</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Sisa Tagihan</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Status</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Bukti Terbaru</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($fees as $fee)
                        @php
                            $totalPaid  = $fee->transactions->where('status', 'approved')->sum('jumlah_bayar');
                            $remaining  = max(0, $fee->total_jumlah - $totalPaid);
                            $pendingTx  = $fee->transactions->firstWhere('status', 'pending');

                            $feeCfg = match($fee->status) {
                                'paid'         => ['label' => 'Lunas',       'class' => 'bg-ich-success-soft text-ich-success'],
                                'installments' => ['label' => 'Cicilan',     'class' => 'bg-ich-purple-soft text-ich-purple'],
                                default        => ['label' => 'Belum Bayar', 'class' => 'bg-ich-warning-soft text-ich-warning'],
                            };
                        @endphp
                        <tr class="hover:bg-ich-surface transition-colors">

                            {{-- Siswa --}}
                            <td class="px-5 py-4">
                                <p class="font-ui font-bold text-sm text-ich-ink-900">
                                    {{ $fee->student?->nama_siswa ?? '-' }}
                                </p>
                            </td>

                            {{-- Total Tagihan --}}
                            <td class="px-5 py-4 font-sans text-sm text-ich-ink-900">
                                Rp {{ number_format($fee->total_jumlah, 0, ',', '.') }}
                            </td>

                            {{-- Sudah Dibayar --}}
                            <td class="px-5 py-4 font-ui font-bold text-sm text-ich-success">
                                Rp {{ number_format($totalPaid, 0, ',', '.') }}
                            </td>

                            {{-- Sisa Tagihan --}}
                            <td class="px-5 py-4">
                                @if($remaining > 0)
                                    <span class="font-ui font-bold text-sm text-ich-warning">
                                        Rp {{ number_format($remaining, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="font-ui font-bold text-sm text-ich-success">Lunas</span>
                                @endif
                            </td>

                            {{-- Status Fee --}}
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-ui font-bold {{ $feeCfg['class'] }}">
                                    {{ $feeCfg['label'] }}
                                </span>
                            </td>

                            {{-- Bukti Terbaru (dari pending tx jika ada) --}}
                            <td class="px-5 py-4">
                                @if($pendingTx)
                                    <div class="space-y-0.5">
                                        <p class="font-sans text-xs text-ich-ink-600">
                                            {{ $pendingTx->payment_date?->format('d M Y') }}
                                            @if($pendingTx->nama_bank) · {{ $pendingTx->nama_bank }} @endif
                                        </p>
                                        <p class="font-ui font-semibold text-xs text-ich-ink-900">
                                            Rp {{ number_format($pendingTx->jumlah_bayar, 0, ',', '.') }}
                                            <span class="font-normal text-ich-ink-400">
                                                ({{ $pendingTx->payment_category === 'full' ? 'Lunas Penuh' : 'Cicilan' }})
                                            </span>
                                        </p>
                                        @if($pendingTx->gambar_bukti_pembayaran)
                                            <a href="{{ asset('storage/' . $pendingTx->gambar_bukti_pembayaran) }}"
                                               target="_blank"
                                               class="inline-flex items-center gap-1 text-ich-teal font-ui font-semibold text-xs hover:underline">
                                                <x-ich-icon name="document" :size="13" color="currentColor"/>
                                                Lihat Bukti
                                            </a>
                                        @endif
                                    </div>
                                @elseif($fee->status === 'paid')
                                    <span class="text-xs text-ich-success font-ui font-semibold">—</span>
                                @else
                                    <span class="text-xs text-ich-ink-400 font-sans italic">Belum ada bukti</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4">
                                @if($pendingTx && ! $isReadOnly)
                                    <div class="flex flex-col gap-2 min-w-[120px]">
                                        <button type="button"
                                                @click="$dispatch('open-confirm', {
                                                    title: 'Setujui Pembayaran',
                                                    message: 'Konfirmasi pembayaran pendaftaran dari {{ $fee->student?->nama_siswa ?? '-' }} sebesar Rp {{ number_format($pendingTx->jumlah_bayar, 0, ',', '.') }}?',
                                                    action: '{{ route('admin.pembayaran-pendaftaran.approve', $pendingTx) }}',
                                                    btnText: 'Setujui'
                                                })"
                                                class="w-full px-3 py-1.5 bg-ich-green text-white font-ui font-bold text-xs
                                                       rounded-lg hover:bg-ich-green-dark transition-colors">
                                            Setujui
                                        </button>

                                        <button type="button"
                                                @click="$dispatch('open-reject', {
                                                    name: '{{ $fee->student?->nama_siswa ?? '-' }}',
                                                    action: '{{ route('admin.pembayaran-pendaftaran.reject', $pendingTx) }}'
                                                })"
                                                class="w-full px-3 py-1.5 bg-white border-2 border-ich-error text-ich-error
                                                       font-ui font-bold text-xs rounded-lg
                                                       hover:bg-ich-error hover:text-white transition-colors">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-ich-ink-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-ich-ink-400 font-sans">
                                Tidak ada data pembayaran pendaftaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($fees->hasPages())
            <div class="px-5 py-4 border-t border-ich-line">
                {{ $fees->links() }}
            </div>
        @endif
    </div>

    {{-- Reject Modal --}}
    <x-admin-modal show="reject.show" title="Tolak Pembayaran" maxWidth="sm">
        <p class="text-sm text-ich-ink-600 mb-4">Tolak pembayaran dari <strong x-text="reject.name"></strong>?</p>
        <form :action="reject.action" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Alasan Penolakan
                </label>
                <textarea name="rejection_reason" rows="3"
                          placeholder="Tuliskan alasan penolakan (opsional)..."
                          class="w-full px-3 py-2.5 border-2 border-ich-line rounded-ich-lg font-sans text-sm
                                 focus:outline-none focus:border-ich-error resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 py-2.5 bg-ich-error text-white font-ui font-bold text-sm
                               rounded-ich-lg hover:opacity-90 transition-opacity">
                    Konfirmasi Tolak
                </button>
                <button type="button" @click="reject.show = false"
                        class="flex-1 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm
                               rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </x-admin-modal>

</div>
</x-main-layout>
