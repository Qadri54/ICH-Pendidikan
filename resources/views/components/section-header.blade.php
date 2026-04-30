@props(['title', 'action' => null, 'actionHref' => '#'])

<div class="flex items-center justify-between mx-1 mb-2.5">
    <div class="font-ui font-bold text-[15px] text-ich-ink-900">{{ $title }}</div>
    @if($action)
        <a href="{{ $actionHref }}" class="font-sans text-[12px] text-ich-teal font-semibold no-underline">
            {{ $action }}
        </a>
    @endif
</div>
