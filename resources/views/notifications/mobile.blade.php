<x-mobile-layout page-title="Notifikasi">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-lg font-display font-bold text-ich-ink-900">Notifikasi</h1>
            <p class="text-xs text-ich-ink-400">{{ $notifications->total() }} notifikasi</p>
        </div>
        @if($notifications->total() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-1.5 h-8 px-3 bg-red-50 text-red-600 border border-red-200
                               font-ui font-bold text-xs rounded-ich-md hover:bg-red-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                $isSpp  = str_contains($notif->type, 'SppOverdue');
                $isFee  = str_contains($notif->type, 'RegistrationFeeOverdue');

                $iconBg = $isSpp ? 'bg-red-50 text-red-500'
                        : ($isFee ? 'bg-orange-50 text-orange-500'
                        : 'bg-gray-50 text-gray-500');

                $iconPath = $isSpp
                    ? 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    : ($isFee
                    ? 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
                    : 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9');
            @endphp

            <div class="flex items-start gap-3 px-4 py-3.5 border-b border-ich-line last:border-b-0">

                {{-- Icon --}}
                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 {{ $iconBg }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
                    </svg>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-ich-ink-900 font-medium leading-snug">
                        {{ $notif->data['message'] }}
                    </p>
                    <p class="text-xs text-ich-ink-400 mt-1">
                        {{ $notif->created_at->diffForHumans() }}
                    </p>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 mt-2">
                        <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                            @csrf
                            <button type="submit"
                                    class="h-7 px-3 bg-ich-green text-white font-ui font-bold text-xs rounded-ich-md">
                                Lihat
                            </button>
                        </form>
                        <form method="POST" action="{{ route('notifications.delete', $notif->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="h-7 px-3 bg-red-50 text-red-500 border border-red-200 font-ui font-bold text-xs rounded-ich-md">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-ich-ink-400 font-ui font-semibold text-sm">Tidak ada notifikasi</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif

</x-mobile-layout>
