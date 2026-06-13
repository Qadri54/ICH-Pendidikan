@extends('errors.layout', [
    'code'    => '419',
    'title'   => 'Sesi Telah Berakhir',
    'message' => 'Sesi Anda telah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.',
    'iconBg'  => 'bg-ich-info-soft',
    'icon'    => '<svg class="w-10 h-10 text-ich-info" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                 </svg>',
])
