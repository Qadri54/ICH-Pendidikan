@props(['tone' => 'green', 'size' => 40])
@php
$tones = [
    'green'  => 'bg-[#E8F5EA] text-[#009966]',
    'yellow' => 'bg-[#FEF5DC] text-[#E09F17]',
    'blue'   => 'bg-[#F4F7FC] text-[#155DFC]',
    'brand'  => 'bg-[#EDE9FE] text-[#8B5CF6]',
    'teal'   => 'bg-[#E0F0F5] text-ich-teal',
    'error'  => 'bg-[#FEE2E2] text-ich-error',
    'success'=> 'bg-[#D1FAE5] text-[#009966]',
    'warning'=> 'bg-[#FEF5DC] text-[#E09F17]',
    'info'   => 'bg-[#F4F7FC] text-[#155DFC]',
];
$cls = $tones[$tone] ?? $tones['green'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-[12px] flex items-center justify-center shrink-0 $cls"]) }}
     style="width:{{ $size }}px;height:{{ $size }}px">
    {{ $slot }}
</div>
