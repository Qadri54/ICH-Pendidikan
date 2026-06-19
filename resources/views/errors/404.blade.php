@extends('errors.layout', [
    'code'    => '404',
    'title'   => 'Halaman Tidak Ditemukan',
    'message' => 'Halaman yang Anda cari tidak tersedia atau telah dipindahkan. Periksa kembali URL atau kembali ke halaman utama.',
    'iconBg'  => 'bg-ich-warning-soft',
    'icon'    => '<svg class="w-10 h-10 text-ich-warning" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5h-3"/>
                 </svg>',
])
