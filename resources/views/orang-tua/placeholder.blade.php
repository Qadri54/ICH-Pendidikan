<x-mobile-layout :title="$pageTitle" :page-title="$pageTitle">

    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
        <div class="w-16 h-16 rounded-2xl bg-[#E8F5EA] flex items-center justify-center mb-4">
            <x-ich-icon name="settings" :size="28" color="#3DA746"/>
        </div>
        <h2 class="font-display font-bold text-lg text-ich-ink-900">Segera Hadir</h2>
        <p class="font-sans text-sm text-ich-ink-400 mt-1">
            Fitur <span class="font-semibold text-ich-ink-600">{{ $pageTitle }}</span>
            sedang dalam pengembangan.
        </p>
    </div>

</x-mobile-layout>
