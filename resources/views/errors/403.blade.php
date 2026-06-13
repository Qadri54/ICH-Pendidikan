@extends('errors.layout', [
    'code'    => '403',
    'title'   => 'Akses Ditolak',
    'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini. Hubungi administrator jika Anda merasa ini adalah kesalahan.',
    'iconBg'  => 'bg-ich-error-soft',
    'icon'    => '<svg class="w-10 h-10 text-ich-error" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                 </svg>',
])
