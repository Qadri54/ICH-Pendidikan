<?php

namespace App\Services\ReportCard;

use App\Models\PhysicalMeasurement;

class PhysicalMeasurementService
{
    // Ambil data pengukuran fisik milik satu raport.
    // Mengembalikan null jika belum pernah diisi oleh guru.
    public function getByReportCard(int $reportCardId): ?PhysicalMeasurement
    {
        return PhysicalMeasurement::firstWhere('report_card_id', $reportCardId);
    }

    // Simpan atau update pengukuran fisik untuk satu raport.
    // Relasinya 1:1 — satu raport hanya punya satu data pengukuran fisik.
    // updateOrCreate dengan kunci report_card_id memastikan tidak ada duplikasi:
    //   jika sudah ada → update, jika belum ada → insert baru.
    // $data berisi: tinggi_badan, berat_badan, lingkar_kepala, tanggal_ukur.
    public function upsert(int $reportCardId, array $data): PhysicalMeasurement
    {
        return PhysicalMeasurement::updateOrCreate(
            ['report_card_id' => $reportCardId],
            $data
        );
    }
}
