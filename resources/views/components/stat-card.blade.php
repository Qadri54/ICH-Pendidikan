@props([
    'label',
    'value',
    'caption'  => null,
    'tone'     => 'green',
    'pill'     => null,
    'pillTone' => 'success',
])

<x-card class="p-3.5">
    <div class="flex justify-between items-start mb-3.5">
        <x-icon-chip :tone="$tone" :size="36">
            {{ $icon ?? '' }}
        </x-icon-chip>
        @if($pill)
            <x-pill :tone="$pillTone">{{ $pill }}</x-pill>
        @endif
    </div>
    <div class="font-sans text-[11px] text-ich-ink-400 mb-0.5">{{ $label }}</div>
    <div class="font-ui font-bold text-[18px] text-ich-ink-900 tracking-tight">{{ $value }}</div>
    @if($caption)
        <div class="font-sans text-[10px] text-ich-ink-300 mt-1">{{ $caption }}</div>
    @endif
</x-card>
