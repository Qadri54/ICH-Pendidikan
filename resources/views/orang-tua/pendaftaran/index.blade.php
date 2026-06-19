<x-mobile-layout title="Pendaftaran" page-title="Pendaftaran">

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">
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
        <div class="bg-ich-warning-soft rounded-xl p-5 flex items-start gap-3 mb-5">
            <x-ich-icon name="clock" :size="24" color="#E09F17"/>
            <div>
                <p class="font-ui font-bold text-sm text-ich-warning">Pendaftaran Sedang Ditutup</p>
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
                        'accepted' => ['label'=>'Diterima','bg'=>'bg-ich-success-soft','text'=>'text-ich-success'],
                        'rejected' => ['label'=>'Ditolak', 'bg'=>'bg-ich-error-soft','text'=>'text-ich-error'],
                        default    => ['label'=>'Menunggu','bg'=>'bg-ich-warning-soft','text'=>'text-ich-warning'],
                    };
                @endphp

                <a href="{{ route('pendaftaran.detail', $reg->registration_id) }}"
                   class="block bg-white rounded-xl shadow-ich-card p-5 hover:shadow-md transition-shadow active:scale-[0.98]">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-ich-blue-soft flex items-center justify-center shrink-0">
                            <span class="font-display font-bold text-lg text-ich-info">
                                {{ strtoupper(substr($reg->nama_siswa, 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-ui font-bold text-sm text-ich-ink-900 truncate">
                                {{ $reg->nama_siswa }}
                            </p>
                            <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                {{ $reg->jenis_pendaftaran ?? 'TK' }} · {{ $reg->created_at->translatedFormat('d M Y') }}
                            </p>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <span class="px-2 py-0.5 {{ $cfg['bg'] }} {{ $cfg['text'] }} text-xs font-ui font-bold rounded-full">
                                    {{ $cfg['label'] }}
                                </span>
                            </div>
                        </div>
                        <x-ich-icon name="chevron_right" :size="20" color="#99A1AF"/>
                    </div>
                </a>
            @endforeach
        </div>
    @else
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
