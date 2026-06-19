@props(['tone' => 'neutral'])
@php
$tones = [
    'success' => 'bg-ich-success-soft text-ich-success',
    'warning' => 'bg-ich-warning-soft text-ich-warning',
    'error'   => 'bg-ich-error-soft text-ich-error',
    'info'    => 'bg-ich-info-soft text-ich-info',
    'brand'   => 'bg-ich-purple-soft text-ich-purple',
    'green'   => 'bg-ich-green-surface text-ich-green',
    'neutral' => 'bg-gray-100 text-ich-ink-500',
];
$cls = $tones[$tone] ?? $tones['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-[3px] rounded-full font-ui font-bold text-[11px] leading-snug $cls"]) }}>
    {{ $slot }}
</span>
