@props(['show', 'title' => '', 'maxWidth' => 'lg'])

@php
$widthClass = match($maxWidth) {
    'sm'  => 'max-w-sm',
    'md'  => 'max-w-md',
    'lg'  => 'max-w-lg',
    'xl'  => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    default => 'max-w-lg',
};
@endphp

<div x-show="{{ $show }}" x-cloak
     class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto px-4 py-8"
     x-transition:enter="ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="fixed inset-0 bg-black/50" @click="{{ $show }} = false"></div>

    <div class="relative w-full {{ $widthClass }} bg-white rounded-xl shadow-xl z-10"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @keydown.escape.window="{{ $show }} = false">

        <div class="flex items-center justify-between px-6 py-4 border-b border-ich-line">
            <h2 class="text-lg font-display font-bold text-ich-ink-900">{{ $title }}</h2>
            <button type="button" @click="{{ $show }} = false"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-ich-ink-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-6 py-5">
            {{ $slot }}
        </div>
    </div>
</div>
