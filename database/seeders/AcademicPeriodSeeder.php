<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use Illuminate\Database\Seeder;

class AcademicPeriodSeeder extends Seeder
{
    public function run(): void
    {
        $periods = [
            [
                'tahun_ajaran'    => '2024/2025',
                'semester'        => 1,
                'tanggal_mulai'   => '2024-07-15',
                'tanggal_selesai' => '2024-12-20',
                'is_active'       => false,
            ],
            [
                'tahun_ajaran'    => '2024/2025',
                'semester'        => 2,
                'tanggal_mulai'   => '2025-01-06',
                'tanggal_selesai' => '2025-06-20',
                'is_active'       => false,
            ],
            [
                'tahun_ajaran'    => '2025/2026',
                'semester'        => 1,
                'tanggal_mulai'   => '2025-07-14',
                'tanggal_selesai' => '2025-12-19',
                'is_active'       => true, // periode yang sedang berjalan
            ],
            [
                'tahun_ajaran'    => '2025/2026',
                'semester'        => 2,
                'tanggal_mulai'   => '2026-01-05',
                'tanggal_selesai' => '2026-06-19',
                'is_active'       => false,
            ],
        ];

        foreach ($periods as $period) {
            AcademicPeriod::create($period);
        }
    }
}
