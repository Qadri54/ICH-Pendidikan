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
                    'Iman kepada Allah',
                    'Iman kepada Malaikat',
                    'Iman kepada Kitab-kitab Allah',
                    'Iman kepada Rasul',
                    'Iman kepada Hari Kiamat',
                    'Iman kepada Qada dan Qadar',
                ],
            ],
            [
                'nama'  => 'Rukun Islam',
                'urutan' => 3,
                'items' => [
                    'Syahadat',
                    'Sholat',
                    'Zakat',
                    'Puasa',
                    'Haji',
                ],
            ],
            [
                'nama'  => 'Menirukan dan Melafazkan Doa Harian',
                'urutan' => 4,
                'items' => [
                    'Doa sebelum tidur',
                    'Doa bangun tidur',
                    'Doa masuk kamar mandi',
                    'Doa keluar kamar mandi',
                    'Doa sebelum makan',
                    'Doa sesudah makan',
                    'Doa masuk rumah',
                    'Doa keluar rumah',
                    'Doa naik kendaraan',
                    'Doa untuk kedua orang tua',
                    'Doa belajar',
                    'Doa kebaikan dunia dan akhirat',
                    'Doa masuk masjid',
                    'Doa keluar masjid',
                ],
            ],
            [
                'nama'  => 'Menirukan dan Melafazkan Kalimat Toyibah',
                'urutan' => 5,
                'items' => [
                    'Basmalah',
                    'Hamdalah',
                    'Subhanallah',
                    'Allahu Akbar',
                    'Istighfar',
                    'Innalillahi wa innailaihi raji\'un',
                    'Insya Allah',
                    'Masya Allah',
                ],
            ],
            [
                'nama'  => 'Menirukan dan Melafazkan Surah Pendek',
                'urutan' => 6,
                'items' => [
                    'Al-Fatihah',
                    'An-Nas',
                    'Al-Falaq',
                    'Al-Ikhlas',
                    'Al-Lahab',
                    'An-Nashr',
                ],
            ],
            [
                'nama'  => 'Mengenal Aturan Wudhu',
                'urutan' => 7,
                'items' => [
                    'Niat wudhu',
                    'Membasuh muka',
                    'Membasuh kedua tangan hingga siku',
                    'Mengusap sebagian kepala',
                    'Membasuh kedua kaki hingga mata kaki',
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
