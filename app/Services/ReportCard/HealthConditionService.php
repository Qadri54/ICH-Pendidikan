<?php

namespace App\Services\ReportCard;

use App\Models\HealthCondition;

class HealthConditionService
{
    // Ambil data kondisi kesehatan milik satu raport.
    // Mengembalikan null jika belum pernah diisi oleh guru.
    public function getByReportCard(int $reportCardId): ?HealthCondition
    {
        return HealthCondition::firstWhere('report_card_id', $reportCardId);
    }

    // Simpan atau update kondisi kesehatan untuk satu raport.
    // Relasinya 1:1 — satu raport hanya punya satu data kondisi kesehatan.
    // updateOrCreate dengan kunci report_card_id memastikan tidak ada duplikasi:
    //   jika sudah ada → update, jika belum ada → insert baru.
    // $data berisi: pendengaran, penglihatan, catatan_tambahan.
    public function upsert(int $reportCardId, array $data): HealthCondition
    {
        return HealthCondition::updateOrCreate(
            ['report_card_id' => $reportCardId],
            $data
        );
    }
}
