@extends('errors.layout', [
    'code'    => '503',
    'title'   => 'Sedang Dalam Pemeliharaan',
    'message' => 'Sistem sedang dalam pemeliharaan untuk peningkatan layanan. Silakan coba lagi dalam beberapa menit.',
    'iconBg'  => 'bg-ich-purple-soft',
    'icon'    => '<svg class="w-10 h-10 text-ich-purple" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.1-5.1a2.121 2.121 0 113-3l5.1 5.1m0 0l3.535 3.536a2.121 2.121 0 01-3 3L11.42 15.17zm0 0L8.88 17.71m7.16-12.15l1.768 1.768M14.12 9.88l1.768 1.768m-7.56 7.56l1.768 1.768"/>
                 </svg>',
])
