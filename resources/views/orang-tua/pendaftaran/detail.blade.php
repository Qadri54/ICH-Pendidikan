<x-mobile-layout title="Detail Pendaftaran" page-title="Pendaftaran">

    <div class="mb-4">
        <a href="{{ route('pendaftaran') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">&larr; Kembali</a>
    </div>

    @php
        $cfg = match($registration->status) {
            'accepted' => ['label'=>'Diterima','bg'=>'bg-ich-success-soft','text'=>'text-ich-success','icon'=>'check_circle','color'=>'#009966'],
            'rejected' => ['label'=>'Ditolak', 'bg'=>'bg-ich-error-soft','text'=>'text-ich-error','icon'=>'close','color'=>'#E7000B'],
            default    => ['label'=>'Menunggu','bg'=>'bg-ich-warning-soft','text'=>'text-ich-warning','icon'=>'clock','color'=>'#E09F17'],
        };
    @endphp

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">

        {{-- Header --}}
        <div class="px-5 py-4 flex items-center justify-between border-b border-ich-line">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-ich-blue-soft flex items-center justify-center shrink-0">
                    <span class="font-display font-bold text-lg text-ich-info">
                        {{ strtoupper(substr($registration->nama_siswa, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <p class="font-ui font-bold text-sm text-ich-ink-900">{{ $registration->nama_siswa }}</p>
                    <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                        Dikirim {{ $registration->created_at->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>
            <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-ui font-bold {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                <x-ich-icon :name="$cfg['icon']" :size="14" :color="$cfg['color']"/>
                {{ $cfg['label'] }}
            </span>
        </div>

        {{-- Biodata Siswa --}}
        <div class="px-5 py-3 bg-ich-surface border-b border-ich-line">
            <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Data Anak</p>
        </div>
        <div class="divide-y divide-ich-line">
            @foreach([
                ['Tempat Lahir',  $registration->tempat_lahir],
                ['Tanggal Lahir', $registration->tanggal_lahir?->translatedFormat('d F Y')],
                ['Jenis Kelamin', $registration->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'],
                ['Alamat',        $registration->alamat],
                ['Anak Ke',       $registration->anak_ke],
            ] as [$label, $value])
                <div class="flex gap-3 px-5 py-2.5">
                    <span class="w-28 shrink-0 font-ui font-bold text-xs text-ich-ink-400">{{ $label }}</span>
                    <span class="font-sans text-sm text-ich-ink-900">{{ $value ?? '-' }}</span>
                </div>
            @endforeach
        </div>

        {{-- Biodata Ayah --}}
        <div class="px-5 py-3 bg-ich-surface border-b border-ich-line">
            <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Data Ayah</p>
        </div>
        <div class="divide-y divide-ich-line">
            @foreach([
                ['Nama Ayah',     $registration->nama_ayah],
                ['Tempat Lahir',  $registration->tempat_lahir_ayah],
                ['Tanggal Lahir', $registration->tanggal_lahir_ayah?->translatedFormat('d F Y')],
                ['Pekerjaan',     $registration->pekerjaan_ayah],
                ['Pendidikan',    $registration->pendidikan_ayah],
                ['No. Telp',      $registration->no_telp_ayah],
            ] as [$label, $value])
                <div class="flex gap-3 px-5 py-2.5">
                    <span class="w-28 shrink-0 font-ui font-bold text-xs text-ich-ink-400">{{ $label }}</span>
                    <span class="font-sans text-sm text-ich-ink-900">{{ $value ?? '-' }}</span>
                </div>
            @endforeach
        </div>

        {{-- Biodata Ibu --}}
        <div class="px-5 py-3 bg-ich-surface border-b border-ich-line">
            <p class="font-ui font-bold text-xs text-ich-ink-500 uppercase tracking-wide">Data Ibu</p>
        </div>
        <div class="divide-y divide-ich-line">
            @foreach([
                ['Nama Ibu',      $registration->nama_ibu],
                ['Tempat Lahir',  $registration->tempat_lahir_ibu],
                ['Tanggal Lahir', $registration->tanggal_lahir_ibu?->translatedFormat('d F Y')],
                ['Pekerjaan',     $registration->pekerjaan_ibu],
                ['Pendidikan',    $registration->pendidikan_ibu],
                ['No. Telp',      $registration->no_telp_ibu],
            ] as [$label, $value])
                <div class="flex gap-3 px-5 py-2.5">
                    <span class="w-28 shrink-0 font-ui font-bold text-xs text-ich-ink-400">{{ $label }}</span>
                    <span class="font-sans text-sm text-ich-ink-900">{{ $value ?? '-' }}</span>
                </div>
            @endforeach
        </div>

        {{-- Alasan penolakan --}}
        @if($registration->status === 'rejected' && $registration->rejection_reason)
            <div class="px-5 py-3 bg-[#FEF2F2]">
                <p class="font-ui font-bold text-xs text-ich-error mb-1">Alasan Penolakan:</p>
                <p class="font-sans text-sm text-ich-ink-900">{{ $registration->rejection_reason }}</p>
            </div>
        @endif

    </div>

</x-mobile-layout>
