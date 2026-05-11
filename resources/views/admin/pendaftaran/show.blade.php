@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Detail Pendaftaran">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.pendaftaran.index') }}"
               class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Detail Pendaftaran</h1>
        </div>
        @php
            $jenisLabel = $pendaftaran->jenis_pendaftaran === 'TK' ? 'PG / TK ICH' : 'Magrib Mengaji';
            $jenisBg    = $pendaftaran->jenis_pendaftaran === 'TK' ? 'bg-[#EDE9FE] text-[#8B5CF6]' : 'bg-[#FEF5DC] text-[#E09F17]';
        @endphp
        <span class="px-3 py-1.5 rounded-full text-xs font-ui font-bold {{ $jenisBg }}">
            {{ $jenisLabel }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Detail info --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Data Orang Tua (akun) --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Data Akun Orang Tua</h2>
                @foreach([
                    ['Nama',   $pendaftaran->user?->name ?? '-'],
                    ['Email',  $pendaftaran->user?->email ?? '-'],
                    ['No HP',  $pendaftaran->user?->no_hp ?? '-'],
                ] as [$label, $value])
                    <div class="flex gap-4 py-1.5 border-b border-ich-line last:border-0">
                        <div class="w-32 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                        <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Biodata Siswa --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Biodata Siswa</h2>
                @php
                    $siswaRows = [
                        ['Nama Lengkap',    $pendaftaran->nama_siswa ?? '-'],
                        ['Tempat Lahir',    $pendaftaran->tempat_lahir ?? '-'],
                        ['Tanggal Lahir',   $pendaftaran->tanggal_lahir?->format('d M Y') ?? '-'],
                        ['Jenis Kelamin',   $pendaftaran->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'],
                        ['Alamat',          $pendaftaran->alamat ?? '-'],
                        ['Anak ke',         $pendaftaran->anak_ke ? 'Anak ke-'.$pendaftaran->anak_ke : '-'],
                    ];
                    if ($pendaftaran->jenis_pendaftaran === 'TK') {
                        $siswaRows[] = ['Ukuran Baju', $pendaftaran->ukuran_baju ?? '-'];
                    }
                @endphp
                @foreach($siswaRows as [$label, $value])
                    <div class="flex gap-4 py-1.5 border-b border-ich-line last:border-0">
                        <div class="w-32 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                        <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Biodata Ayah --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Biodata Ayah</h2>
                @foreach([
                    ['Nama',          $pendaftaran->nama_ayah ?? '-'],
                    ['Tempat Lahir',  $pendaftaran->tempat_lahir_ayah ?? '-'],
                    ['Tanggal Lahir', $pendaftaran->tanggal_lahir_ayah?->format('d M Y') ?? '-'],
                    ['Alamat',        $pendaftaran->alamat_ayah ?? '-'],
                    ['Pendidikan',    $pendaftaran->pendidikan_ayah ?? '-'],
                    ['Pekerjaan',     $pendaftaran->pekerjaan_ayah ?? '-'],
                    ['No. Telp/HP',   $pendaftaran->no_telp_ayah ?? '-'],
                ] as [$label, $value])
                    <div class="flex gap-4 py-1.5 border-b border-ich-line last:border-0">
                        <div class="w-32 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                        <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Biodata Ibu --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <h2 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Biodata Ibu</h2>
                @foreach([
                    ['Nama',          $pendaftaran->nama_ibu ?? '-'],
                    ['Tempat Lahir',  $pendaftaran->tempat_lahir_ibu ?? '-'],
                    ['Tanggal Lahir', $pendaftaran->tanggal_lahir_ibu?->format('d M Y') ?? '-'],
                    ['Alamat',        $pendaftaran->alamat_ibu ?? '-'],
                    ['Pekerjaan',     $pendaftaran->pekerjaan_ibu ?? '-'],
                    ['Pendidikan',    $pendaftaran->pendidikan_ibu ?? '-'],
                    ['No. Telp/HP',   $pendaftaran->no_telp_ibu ?? '-'],
                ] as [$label, $value])
                    <div class="flex gap-4 py-1.5 border-b border-ich-line last:border-0">
                        <div class="w-32 font-ui font-bold text-sm text-ich-ink-400 shrink-0">{{ $label }}</div>
                        <div class="font-sans text-sm text-ich-ink-900">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Status update --}}
        <div class="bg-white rounded-xl shadow-ich-card p-6 h-fit">
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

            @if($pendaftaran->status === 'pending' && ! $isReadOnly)
                <form method="POST" action="{{ route('admin.pendaftaran.update', $pendaftaran) }}"
                      class="space-y-3" x-data="{ showReject: false }">
                    @csrf @method('PATCH')

                    <button type="submit" name="status" value="accepted"
                            onclick="return confirm('Terima pendaftaran ini? Siswa akan dibuat dan biaya pendaftaran akan digenerate otomatis.')"
                            class="w-full py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                   rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                        Terima Pendaftaran
                    </button>

                    <div x-show="!showReject">
                        <button type="button" @click="showReject = true"
                                class="w-full py-2.5 bg-white border-2 border-ich-error text-ich-error font-ui font-bold text-sm
                                       rounded-ich-lg hover:bg-[#FEE2E2] transition-colors">
                            Tolak Pendaftaran
                        </button>
                    </div>

                    <div x-show="showReject" class="space-y-2">
                        <label class="block font-ui font-bold text-xs text-ich-ink-600">
                            Alasan Penolakan <span class="text-ich-error">*</span>
                        </label>
                        <textarea name="rejection_reason" rows="3"
                                  placeholder="Tuliskan alasan penolakan yang akan dilihat oleh orang tua..."
                                  class="w-full px-3 py-2 border-2 border-ich-error rounded-ich-lg font-sans text-sm
                                         focus:outline-none resize-none">{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason')
                            <p class="text-ich-error text-xs">{{ $message }}</p>
                        @enderror
                        <div class="flex gap-2">
                            <button type="submit" name="status" value="rejected"
                                    class="flex-1 py-2.5 bg-ich-error text-white font-ui font-bold text-sm
                                           rounded-ich-lg hover:opacity-90 transition-opacity">
                                Konfirmasi Tolak
                            </button>
                            <button type="button" @click="showReject = false"
                                    class="px-4 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm
                                           rounded-ich-lg hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            @elseif($pendaftaran->status === 'pending' && $isReadOnly)
                <p class="text-sm text-ich-ink-400 font-sans">Anda tidak memiliki akses untuk mengubah status.</p>
            @else
                <p class="text-sm text-ich-ink-400 font-sans">Status sudah final, tidak dapat diubah.</p>
                @if($pendaftaran->status === 'rejected' && $pendaftaran->rejection_reason)
                    <div class="mt-3 p-3 bg-[#FEE2E2] rounded-lg">
                        <p class="font-ui font-bold text-xs text-ich-error mb-1">Alasan Penolakan:</p>
                        <p class="font-sans text-sm text-ich-ink-900">{{ $pendaftaran->rejection_reason }}</p>
                    </div>
                @endif
            @endif
        </div>

    </div>

</x-main-layout>
