<x-main-layout title="Pembayaran Pendaftaran">

    <div class="mb-6">
        <h1 class="text-2xl font-display font-bold text-ich-ink-900">Pembayaran Pendaftaran</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">Kelola bukti pembayaran biaya pendaftaran siswa</p>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">
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
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Menunggu</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
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
                <thead>
                    <tr class="border-b border-ich-line bg-[#F9FAFB]">
                        <th class="px-5 py-3.5 text-left font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Siswa</th>
                        <th class="px-5 py-3.5 text-left font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Dibayar</th>
                        <th class="px-5 py-3.5 text-left font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Sisa Tagihan</th>
                        <th class="px-5 py-3.5 text-left font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Bank</th>
                        <th class="px-5 py-3.5 text-left font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Kategori</th>
                        <th class="px-5 py-3.5 text-left font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Tanggal</th>
                        <th class="px-5 py-3.5 text-left font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Bukti</th>
                        <th class="px-5 py-3.5 text-left font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3.5 text-left font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($transaksi as $tx)
                        @php
                            $statusCfg = match($tx->status) {
                                'approved' => ['label' => 'Disetujui', 'class' => 'bg-[#D1FAE5] text-[#009966]'],
                                'rejected' => ['label' => 'Ditolak',   'class' => 'bg-[#FEE2E2] text-ich-error'],
                                default    => ['label' => 'Menunggu',  'class' => 'bg-[#FEF5DC] text-[#E09F17]'],
                            };
                            $fee        = $tx->registrationFee;
                            $totalPaid  = $fee?->transactions?->where('status', 'approved')->sum('jumlah_bayar') ?? 0;
                            $remaining  = max(0, ($fee?->total_jumlah ?? 0) - $totalPaid);
                        @endphp
                        <tr class="hover:bg-[#F9FAFB] transition-colors" x-data="{ rejectOpen: false }">
                            <td class="px-5 py-4">
                                <p class="font-ui font-bold text-sm text-ich-ink-900">
                                    {{ $fee?->student?->nama_siswa ?? '-' }}
                                </p>
                            </td>
                            <td class="px-5 py-4 font-sans text-sm text-ich-ink-900">
                                Rp {{ number_format($tx->jumlah_bayar, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4">
                                @if($remaining > 0)
                                    <span class="font-ui font-bold text-sm text-[#E09F17]">
                                        Rp {{ number_format($remaining, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="font-ui font-bold text-sm text-[#009966]">Lunas</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 font-sans text-sm text-ich-ink-600">
                                {{ $tx->nama_bank ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-ui font-bold
                                             {{ $tx->payment_category === 'full' ? 'bg-[#EDE9FE] text-[#8B5CF6]' : 'bg-[#F4F7FC] text-ich-teal' }}">
                                    {{ $tx->payment_category === 'full' ? 'Lunas Penuh' : 'Cicilan' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-sans text-sm text-ich-ink-600 whitespace-nowrap">
                                {{ $tx->payment_date?->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4">
                                @if($tx->gambar_bukti_pembayaran)
                                    <a href="{{ asset('storage/' . $tx->gambar_bukti_pembayaran) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 text-ich-teal font-ui font-semibold text-xs hover:underline">
                                        <x-ich-icon name="document" :size="14" color="currentColor"/>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-xs text-ich-ink-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-ui font-bold {{ $statusCfg['class'] }}">
                                        {{ $statusCfg['label'] }}
                                    </span>
                                    @if($tx->status === 'rejected' && $tx->rejection_reason)
                                        <p class="text-xs text-ich-ink-400 mt-1 max-w-[160px]" title="{{ $tx->rejection_reason }}">
                                            {{ Str::limit($tx->rejection_reason, 40) }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if($tx->status === 'pending')
                                    <div class="flex flex-col gap-2 min-w-[120px]">
                                        {{-- Setujui --}}
                                        <form method="POST"
                                              action="{{ route('admin.pembayaran-pendaftaran.approve', $tx) }}"
                                              onsubmit="return confirm('Konfirmasi pembayaran ini?')">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full px-3 py-1.5 bg-ich-green text-white font-ui font-bold text-xs
                                                           rounded-lg hover:bg-ich-green-dark transition-colors">
                                                Setujui
                                            </button>
                                        </form>

                                        {{-- Tombol buka form tolak --}}
                                        <button type="button" x-show="!rejectOpen" @click="rejectOpen = true"
                                                class="w-full px-3 py-1.5 bg-white border-2 border-ich-error text-ich-error
                                                       font-ui font-bold text-xs rounded-lg
                                                       hover:bg-ich-error hover:text-white transition-colors">
                                            Tolak
                                        </button>

                                        {{-- Form tolak dengan alasan --}}
                                        <form x-show="rejectOpen" method="POST"
                                              action="{{ route('admin.pembayaran-pendaftaran.reject', $tx) }}"
                                              class="space-y-1.5">
                                            @csrf
                                            <textarea name="rejection_reason" rows="2"
                                                      placeholder="Alasan penolakan..."
                                                      class="w-full px-2 py-1.5 border-2 border-ich-error rounded-lg
                                                             font-sans text-xs focus:outline-none resize-none"></textarea>
                                            <div class="flex gap-1.5">
                                                <button type="submit"
                                                        class="flex-1 py-1.5 bg-ich-error text-white font-ui font-bold text-xs
                                                               rounded-lg hover:opacity-90 transition-opacity">
                                                    Konfirmasi
                                                </button>
                                                <button type="button" @click="rejectOpen = false"
                                                        class="px-2 py-1.5 border border-ich-line text-ich-ink-600
                                                               font-ui text-xs rounded-lg hover:bg-gray-50 transition-colors">
                                                    Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-ich-ink-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-sm text-ich-ink-400 font-sans">
                                Tidak ada data pembayaran pendaftaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksi->hasPages())
            <div class="px-5 py-4 border-t border-ich-line">
                {{ $transaksi->links() }}
            </div>
        @endif
    </div>

</x-main-layout>
