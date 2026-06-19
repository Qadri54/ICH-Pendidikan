<x-main-layout title="Notifikasi">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Notifikasi</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">
                {{ $notifications->total() }} notifikasi
            </p>
        </div>
        @if($notifications->total() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 h-10 px-4 bg-red-50 text-red-600 border border-red-200
                               font-ui font-bold text-sm rounded-ich-md hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Semua
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        @forelse($notifications as $notif)
            @php
                $typeLabel = match(true) {
                    str_contains($notif->type, 'NewRegistration')        => 'Pendaftaran Baru',
                    str_contains($notif->type, 'SppPaymentUploaded')     => 'Bukti Bayar SPP',
                    str_contains($notif->type, 'SppOverdue')             => 'SPP Jatuh Tempo',
                    str_contains($notif->type, 'RegistrationFeeOverdue') => 'Biaya Pendaftaran',
                    default                                               => 'Notifikasi',
                };

                $iconPath = match(true) {
                    str_contains($notif->type, 'NewRegistration')        => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                    str_contains($notif->type, 'SppPaymentUploaded')     => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
                    str_contains($notif->type, 'SppOverdue')             => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    str_contains($notif->type, 'RegistrationFeeOverdue') => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    default                                               => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                };

                $iconBg = match(true) {
                    str_contains($notif->type, 'NewRegistration')        => 'bg-blue-50 text-blue-500',
                    str_contains($notif->type, 'SppPaymentUploaded')     => 'bg-green-50 text-green-500',
                    str_contains($notif->type, 'SppOverdue')             => 'bg-red-50 text-red-500',
                    str_contains($notif->type, 'RegistrationFeeOverdue') => 'bg-orange-50 text-orange-500',
                    default                                               => 'bg-gray-50 text-gray-500',
                };
            @endphp

            <div class="flex items-start gap-4 px-6 py-4 border-b border-ich-line last:border-b-0 hover:bg-gray-50 transition-colors">

                {{-- Icon --}}
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 {{ $iconBg }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
                    </svg>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="inline-block text-xs font-ui font-bold px-2 py-0.5 rounded-full mb-1
                                {{ str_contains($notif->type, 'NewRegistration')        ? 'bg-blue-100 text-blue-600' :
                                   (str_contains($notif->type, 'SppPaymentUploaded')   ? 'bg-green-100 text-green-600' :
                                   (str_contains($notif->type, 'SppOverdue')            ? 'bg-red-100 text-red-600' :
                                   (str_contains($notif->type, 'RegistrationFeeOverdue')? 'bg-orange-100 text-orange-600' :
                                   'bg-gray-100 text-gray-600'))) }}">
                                {{ $typeLabel }}
                            </span>
                            <p class="text-sm text-ich-ink-900 font-medium">
                                {{ $notif->data['message'] }}
                            </p>
                            <p class="text-xs text-ich-ink-400 mt-1">
                                {{ $notif->created_at->translatedFormat('d F Y, H:i') }}
                                &middot;
                                {{ $notif->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            {{-- Lihat --}}
                            <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center gap-1.5 h-8 px-3 bg-ich-green text-white
                                               font-ui font-bold text-xs rounded-ich-md hover:opacity-90 transition-opacity">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 5-4.5 9-9 9S3 17 3 12 7.5 3 12 3s9 4 9 9z"/>
                                    </svg>
                                    Lihat
                                </button>
                            </form>

                            {{-- Hapus --}}
                            <form method="POST" action="{{ route('notifications.delete', $notif->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="flex items-center gap-1.5 h-8 px-3 bg-red-50 text-red-500 border border-red-200
                                               font-ui font-bold text-xs rounded-ich-md hover:bg-red-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-ich-ink-400 font-ui font-semibold text-sm">Tidak ada notifikasi</p>
                <p class="text-ich-ink-300 text-xs mt-1">Semua notifikasi telah dibersihkan</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif

</x-main-layout>
