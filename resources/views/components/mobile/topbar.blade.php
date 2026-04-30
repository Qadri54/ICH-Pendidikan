@props(['title', 'user' => null, 'notifCount' => 0])

<div class="sticky top-0 z-10 bg-ich-green text-white px-4 py-3.5 flex items-center gap-3">

    {{-- Hamburger: toggling drawer via Alpine parent scope --}}
    <button @click="drawerOpen = true"
            class="w-9 h-9 rounded-[10px] bg-white/15 border-none flex items-center justify-center cursor-pointer text-white shrink-0">
        <x-ich-icon name="menu" :size="20" color="#fff"/>
    </button>

    {{-- Title + user --}}
    <div class="flex-1 min-w-0">
        <div class="font-ui font-bold text-[15px] leading-tight truncate">{{ $title }}</div>
        @if($user)
            <div class="font-sans text-[11px] opacity-90 mt-0.5 truncate">{{ $user }}</div>
        @endif
    </div>

    {{-- Notification bell --}}
    <div class="relative w-9 h-9 bg-white/15 rounded-[10px] flex items-center justify-center shrink-0">
        <x-ich-icon name="bell" :size="18" color="#fff"/>
        @if($notifCount > 0)
            <div class="absolute top-1 right-1 w-[14px] h-[14px] bg-ich-yellow rounded-full
                        font-sans font-bold text-[9px] text-white flex items-center justify-center
                        border-[1.5px] border-ich-green">
                {{ $notifCount > 9 ? '9+' : $notifCount }}
            </div>
        @endif
    </div>

</div>
