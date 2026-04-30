@props(['tone' => 'neutral'])
@php
$tones = [
    'success' => 'bg-[#D1FAE5] text-[#009966]',
    'warning' => 'bg-[#FEF5DC] text-[#E09F17]',
    'error'   => 'bg-[#FEE2E2] text-[#E7000B]',
    'info'    => 'bg-[#F4F7FC] text-[#155DFC]',
    'brand'   => 'bg-[#EDE9FE] text-[#8B5CF6]',
    'green'   => 'bg-[#E8F5EA] text-ich-green',
    'neutral' => 'bg-[#F3F4F6] text-ich-ink-500',
];
$cls = $tones[$tone] ?? $tones['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-[3px] rounded-full font-ui font-bold text-[11px] leading-snug $cls"]) }}>
    {{ $slot }}
</span>
