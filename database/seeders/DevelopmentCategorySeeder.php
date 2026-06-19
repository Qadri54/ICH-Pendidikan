<?php

namespace Database\Seeders;

use App\Models\DevelopmentCategory;
use Illuminate\Database\Seeder;

class DevelopmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Struktur: setiap entry adalah kategori utama.
        // 'items' kosong → kategori itu sendiri yang dinilai (leaf).
        // 'items' berisi → sub-item di bawahnya yang dinilai.
        $categories = [
            [
                'nama'  => 'Mengenal Allah melalui Ciptaan-Nya',
                'urutan' => 1,
                'items' => [],
            ],
            [
                'nama'  => 'Rukun Iman',
                'urutan' => 2,
                'items' => [
                    'Percaya kepada Allah',
                    'Percaya kepada Malaikat',
                    'Percaya kepada Rasul',
                    'Percaya kepada Kitab',
                    'Percaya kepada Hari Kiamat',
                    'Percaya kepada Qadha dan Qadhar',
                ],
            ],
            [
                'nama'  => 'Rukun Islam',
                'urutan' => 3,
                'items' => [
                    'Dua Kalimat Syahadat',
                    'Shalat',
                    'Puasa',
                    'Zakat',
                    'Haji',
                ],
            ],
            [
                'nama'  => 'Menirukan dan Melafazkan Doa Harian',
                'urutan' => 4,
                'items' => [
                    'Doa Ibu Bapak',
                    'Doa Kebaikan Dunia Akhirat',
                    'Doa Mohon Keampunan',
                    'Doa Mau Tidur',
                    'Doa Bangun Tidur',
                    'Doa Mau Makan',
                    'Doa Selesai Makan',
                    'Doa Masuk Kamar Mandi',
                    'Doa Keluar Kamar Mandi',
                    'Doa Ketika Bercermin',
                    'Doa Ketika Hujan',
                    'Doa Naik Kendaraan',
                    'Doa Mau Belajar',
                    'Doa Selesai Belajar',
                ],
            ],
            [
                'nama'  => 'Menirukan dan Melafazkan Kalimat Toyibah',
                'urutan' => 5,
                'items' => [
                    'Kalimat Syahadat',
                    'Kalimat Ta\'awudz',
                    'Kalimat Basmalah',
                    'Kalimat Istighfar',
                    'Kalimat Tasbih',
                    'Kalimat Tahmid',
                    'Kalimat Takbir',
                    'Kalimat Tahlil',
                ],
            ],
            [
                'nama'  => 'Menirukan dan Melafazkan Surah Pendek',
                'urutan' => 6,
                'items' => [
                    'Surah Al-Fatihah',
                    'Surah An-Nas',
                    'Surah Al-Falaq',
                    'Surah Al-Ikhlas',
                    'Surah Al-Lahab',
                    'Surah An-Nasr',
                ],
            ],
            [
                'nama'  => 'Mengenal Aturan Wudhu',
                'urutan' => 7,
                'items' => [
                    'Berniat',
                    'Khusyu',
                    'Doa Sebelum Wudhu',
                    'Tertib Mengikuti Urutan Wudhu',
                    'Berdoa Selesai Wudhu',
                ],
            ],
            [
                'nama'  => 'Mengenal Gerakan Shalat',
                'urutan' => 8,
                'items' => [],
            ],
            [
                'nama'  => 'Mengenal Gerakan dan Bacaan Shalat',
                'urutan' => 9,
                'items' => [],
            ],
        ];

        foreach ($categories as $categoryData) {
            $parent = DevelopmentCategory::create([
                'parent_id' => null,
                'nama'      => $categoryData['nama'],
                'urutan'    => $categoryData['urutan'],
                'usia_min'  => 5,
                'usia_max'  => 6,
                'is_active' => true,
            ]);

            foreach ($categoryData['items'] as $index => $itemNama) {
                DevelopmentCategory::create([
                    'parent_id' => $parent->category_id,
                    'nama'      => $itemNama,
                    'urutan'    => $index + 1,
                    'usia_min'  => 5,
                    'usia_max'  => 6,
                    'is_active' => true,
                ]);
            }
        }
    }
}
