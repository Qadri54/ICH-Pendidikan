@props(['tone' => 'green', 'size' => 40])
@php
$tones = [
    'green'  => 'bg-ich-green-surface text-ich-success',
    'yellow' => 'bg-ich-warning-soft text-ich-warning',
    'blue'   => 'bg-ich-info-soft text-ich-info',
    'brand'  => 'bg-ich-purple-soft text-ich-purple',
    'teal'   => 'bg-ich-info-soft text-ich-teal',
    'error'  => 'bg-ich-error-soft text-ich-error',
    'success'=> 'bg-ich-success-soft text-ich-success',
    'warning'=> 'bg-ich-warning-soft text-ich-warning',
    'info'   => 'bg-ich-info-soft text-ich-info',
];
$cls = $tones[$tone] ?? $tones['green'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-[12px] flex items-center justify-center shrink-0 $cls"]) }}
     style="width:{{ $size }}px;height:{{ $size }}px">
    {{ $slot }}
</div>
