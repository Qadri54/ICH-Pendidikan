@props(['name', 'size' => 20, 'color' => 'currentColor'])
@php
$paths = [
    'dashboard'     => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
    'home'          => '<path d="M3 12 L12 4 L21 12 M5 10 V20 H19 V10"/>',
    'users'         => '<circle cx="9" cy="8" r="3.5"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><circle cx="17" cy="9" r="2.5"/><path d="M15 20c0-2 2-3.5 4-3.5s2.5 1 2.5 2"/>',
    'card'          => '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10 H21 M7 15 H11"/>',
    'banknote'      => '<rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M7 10v4 M17 10v4"/>',
    'wallet'        => '<path d="M3 7a2 2 0 012-2h13a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M17 12h3"/>',
    'calendar'      => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10 H21 M8 3 V7 M16 3 V7"/>',
    'book'          => '<path d="M4 4h7a4 4 0 014 4v12H8a4 4 0 01-4-4z M20 4h-7a4 4 0 00-4 4v12h7a4 4 0 004-4z"/>',
    'badge'         => '<path d="M12 2l2.5 4 4.5.5-3.5 3L16 14l-4-2-4 2 .5-4.5L5 6.5 9.5 6z"/><path d="M9 14v8l3-2 3 2v-8"/>',
    'bell'          => '<path d="M6 17V11a6 6 0 1112 0v6 M4 17h16 M10 20a2 2 0 004 0"/>',
    'search'        => '<circle cx="11" cy="11" r="7"/><path d="M17 17l4 4"/>',
    'menu'          => '<path d="M4 7h16 M4 12h16 M4 17h16"/>',
    'close'         => '<path d="M6 6l12 12 M18 6l-12 12"/>',
    'check'         => '<path d="M5 12l5 5L20 7"/>',
    'chevron_right' => '<path d="M9 6l6 6-6 6"/>',
    'chevron_left'  => '<path d="M15 6l-6 6 6 6"/>',
    'chevron_down'  => '<path d="M6 9l6 6 6-6"/>',
    'plus'          => '<path d="M12 5v14 M5 12h14"/>',
    'eye'           => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>',
    'eye_off'       => '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>',
    'lock'          => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/><circle cx="12" cy="16" r="1.5" fill="currentColor" stroke="none"/>',
    'user_circle'   => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="10" r="3"/><path d="M6 19c1-3 3.5-4 6-4s5 1 6 4"/>',
    'settings'      => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1.1-1.5 1.7 1.7 0 00-1.8.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1A1.7 1.7 0 004.6 9a1.7 1.7 0 00-.3-1.8l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.8.3H9a1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.8V9a1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z"/>',
    'logout'        => '<path d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h4"/><path d="M16 17l5-5-5-5 M21 12H9"/>',
    'chart'         => '<path d="M3 3v18h18 M7 15l4-4 3 3 5-6"/>',
    'school'        => '<path d="M12 3l10 5-10 5L2 8z"/><path d="M6 10v5c0 2 3 3 6 3s6-1 6-3v-5"/>',
    'trophy'        => '<path d="M8 4h8v5a4 4 0 01-8 0z"/><path d="M8 5H5a2 2 0 000 4h3 M16 5h3a2 2 0 010 4h-3 M10 13v3 M14 13v3 M7 20h10"/>',
    'clock'         => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
    'alert'         => '<circle cx="12" cy="12" r="9"/><path d="M12 7v6 M12 16.5v.1"/>',
    'download'      => '<path d="M12 3v12 M7 10l5 5 5-5 M5 20h14"/>',
    'phone'         => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/>',
    'mail'          => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 7 9-7"/>',
    'pencil'        => '<path d="M4 20h4l11-11-4-4L4 16z"/>',
    'activity'      => '<path d="M22 12h-4l-3 8-6-16-3 8H2"/>',
    'piggy'         => '<path d="M4 12a8 5 0 1116 0v4a2 2 0 01-2 2h-1v2h-2v-2H9v2H7v-2H6a2 2 0 01-2-2z"/><circle cx="16" cy="11" r="1"/>',
    'star'          => '<path d="M12 3l2.7 5.5 6 .9-4.3 4.2 1 6L12 17l-5.4 2.6 1-6L3.3 9.4l6-.9z"/>',
    'user'          => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    'grid'          => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
    'clipboard'     => '<rect x="9" y="2" width="6" height="4" rx="1"/><path d="M9 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V4a2 2 0 00-2-2h-2"/><path d="M9 12h6 M9 16h4"/>',
    'document'      => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6 M16 13H8 M16 17H8 M10 9H8"/>',
    'cog'           => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1.1-1.5 1.7 1.7 0 00-1.8.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1A1.7 1.7 0 004.6 9a1.7 1.7 0 00-.3-1.8l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.8.3H9a1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.8V9a1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z"/>',
];
$path = $paths[$name] ?? null;
@endphp

@if($path)
<svg xmlns="http://www.w3.org/2000/svg"
     width="{{ $size }}" height="{{ $size }}"
     viewBox="0 0 24 24"
     fill="none"
     stroke="{{ $color }}"
     stroke-width="1.8"
     stroke-linecap="round"
     stroke-linejoin="round"
     {{ $attributes }}>
    {!! $path !!}
</svg>
@endif
