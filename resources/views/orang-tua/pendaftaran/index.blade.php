<x-mobile-layout title="Pendaftaran" page-title="Pendaftaran">

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-[#FEE2E2] text-ich-error rounded-lg text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header banner --}}
    @if($isRegistrationOpen)
        <div class="bg-ich-green rounded-xl p-5 text-white mb-5 flex items-center justify-between">
            <div>
                <h2 class="font-display font-bold text-base">Masa Pendaftaran Dibuka</h2>
                <p class="font-sans text-xs opacity-80 mt-1">
                    Daftarkan anak Anda sekarang.
                </p>
            </div>
            <a href="{{ route('pendaftaran.create') }}"
               class="shrink-0 flex items-center gap-1.5 px-4 py-2.5 bg-white text-ich-green
                      font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn">
                <x-ich-icon name="plus" :size="16" color="#009966"/>
                Daftar Anak
            </a>
        </div>
    @else
        <div class="bg-[#FEF5DC] rounded-xl p-5 flex items-start gap-3 mb-5">
            <x-ich-icon name="clock" :size="24" color="#E09F17"/>
            <div>
                <p class="font-ui font-bold text-sm text-[#E09F17]">Pendaftaran Sedang Ditutup</p>
                <p class="font-sans text-xs text-ich-ink-600 mt-1">
                    Saat ini penerimaan siswa baru belum dibuka. Silakan pantau halaman ini untuk informasi lebih lanjut.
                </p>
            </div>
        </div>
    @endif

    {{-- Daftar anak yang sudah didaftarkan --}}
    @if($registrations->isNotEmpty())
        <div class="space-y-3">
            <h3 class="font-ui font-bold text-sm text-ich-ink-500 uppercase tracking-wide">
                Anak Terdaftar ({{ $registrations->count() }})
            </h3>

            @foreach($registrations as $reg)
                @php
                    $cfg = match($reg->status) {
                        'accepted' => ['label'=>'Diterima','bg'=>'bg-[#D1FAE5]','text'=>'text-[#009966]','icon'=>'check_circle','color'=>'#009966'],
                        'rejected' => ['label'=>'Ditolak', 'bg'=>'bg-[#FEE2E2]','text'=>'text-ich-error','icon'=>'close',        'color'=>'#E7000B'],
                        default    => ['label'=>'Menunggu','bg'=>'bg-[#FEF5DC]','text'=>'text-[#E09F17]','icon'=>'clock',        'color'=>'#E09F17'],
                    };
                @endphp
                <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                    <div class="px-5 py-3 flex items-center justify-between border-b border-ich-line">
                        <div>
                            <p class="font-ui font-bold text-sm text-ich-ink-900">{{ $reg->nama_siswa }}</p>
                            <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                Dikirim {{ $reg->created_at->translatedFormat('d F Y') }}
                            </p>
                        </div>
                        <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-ui font-bold {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                            <x-ich-icon :name="$cfg['icon']" :size="14" :color="$cfg['color']"/>
                            {{ $cfg['label'] }}
                        </span>
                    </div>
                    <div class="divide-y divide-ich-line">
                        @foreach([
                            ['Tempat Lahir',  $reg->tempat_lahir],
                            ['Tanggal Lahir', $reg->tanggal_lahir?->translatedFormat('d F Y')],
                            ['Jenis Kelamin', $reg->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'],
                            ['Nama Ayah',     $reg->nama_ayah],
                            ['Nama Ibu',      $reg->nama_ibu],
                        ] as [$label, $value])
                            <div class="flex gap-3 px-5 py-2.5">
                                <span class="w-28 shrink-0 font-ui font-bold text-xs text-ich-ink-400">{{ $label }}</span>
                                <span class="font-sans text-sm text-ich-ink-900">{{ $value ?? '-' }}</span>
                            </div>
                        @endforeach

                        @if($reg->status === 'rejected' && $reg->rejection_reason)
                            <div class="px-5 py-3 bg-[#FEF2F2]">
                                <p class="font-ui font-bold text-xs text-ich-error mb-1">Alasan Penolakan:</p>
                                <p class="font-sans text-sm text-ich-ink-900">{{ $reg->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty state --}}
        <div class="bg-white rounded-xl shadow-ich-card p-8 text-center">
            <x-ich-icon name="clipboard" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-sm text-ich-ink-900 mb-1">Belum Ada Pendaftaran</p>
            <p class="font-sans text-xs text-ich-ink-400 mb-5">
                Anda belum mendaftarkan anak. Tekan tombol di atas untuk memulai pendaftaran.
            </p>
            @if($isRegistrationOpen)
                <a href="{{ route('pendaftaran.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-ich-green text-white
                          font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn">
                    <x-ich-icon name="plus" :size="16" color="white"/>
                    Daftar Anak Sekarang
                </a>
            @endif
        </div>
    @endif

</x-mobile-layout>
