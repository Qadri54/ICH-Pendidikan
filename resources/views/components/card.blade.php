@props(['noPad' => false])

<div {{ $attributes->merge(['class' => 'bg-white rounded-[14px] shadow-ich-card ' . ($noPad ? 'overflow-hidden' : 'p-4')]) }}>
    {{ $slot }}
</div>
