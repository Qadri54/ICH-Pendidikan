@props([
    'variant' => 'primary',
    'full'    => false,
    'type'    => 'button',
    'href'    => null,
])
@php
$variants = [
    'primary' => 'bg-ich-teal       text-white shadow-ich-btn hover:bg-ich-teal-dark',
    'accent'  => 'bg-ich-yellow     text-white shadow-ich-btn hover:bg-ich-yellow-dark',
    'green'   => 'bg-ich-green      text-white shadow-ich-btn hover:bg-ich-green-dark',
    'ghost'   => 'bg-white          text-ich-teal border border-ich-teal hover:bg-ich-info-soft',
    'dark'    => 'bg-ich-ink-800    text-white hover:bg-ich-ink-900',
];
$cls = $variants[$variant] ?? $variants['primary'];
$fullCls = $full ? 'w-full' : '';
$base = "inline-flex items-center justify-center gap-2 px-5 py-[13px] rounded-ich-lg font-sans font-bold text-[14px] cursor-pointer transition-colors border-none $cls $fullCls";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
    </button>
@endif
