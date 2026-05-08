<?php

namespace App\Services\ReportCard;

use App\Models\StudentReportCard;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ReportCardPdfService
{
    // Generate PDF raport dan simpan sebagai file di storage.
    // Menggunakan eager loading untuk semua relasi agar view tidak melakukan query tambahan.
    // Alur:
    //   1. Load raport beserta semua relasinya.
    //   2. Render view 'raport.pdf' (Blade) menjadi HTML, lalu konversi ke PDF via Dompdf.
    //   3. Simpan file PDF ke storage/app/public/raport/pdf/{report_card_id}.pdf.
    //   4. Kembalikan path file untuk keperluan lain (misal: generate link download).
    // Dipakai saat ingin menyimpan PDF ke server terlebih dahulu sebelum dibagikan.
    public function generate(int $reportCardId): string
    {
        $raport = StudentReportCard::with([
            'student',
            'period',
            'classRoom',
            'homeroomTeacher.user',
            'narrativeAssessments.photos',
            'checklistAssessments.category',
            'physicalMeasurement',
            'healthCondition',
        ])->findOrFail($reportCardId);

        $pdf = Pdf::loadView('raport.pdf', compact('raport'));

        $path = "raport/pdf/{$reportCardId}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    // Generate PDF raport dan langsung kirim sebagai response download ke browser.
    // Tidak menyimpan file ke storage — PDF dibuat on-the-fly dan langsung dikirim.
    // Nama file mengikuti format: raport_{NIS}_{tahun_ajaran}_sem{semester}.pdf
    //   Contoh: raport_2024001_2025-2026_sem1.pdf
    //   str_replace('/', '-') → mengganti karakter '/' pada tahun_ajaran ('2025/2026')
    //   agar nama file valid di semua sistem operasi.
    // Dipakai saat tombol "Unduh Raport" ditekan oleh guru atau orangtua.
    public function download(int $reportCardId): Response
    {
        $raport = StudentReportCard::with([
            'student',
            'period',
            'classRoom',
            'homeroomTeacher.user',
            'narrativeAssessments.photos',
            'checklistAssessments.category',
            'physicalMeasurement',
            'healthCondition',
        ])->findOrFail($reportCardId);

        $filename = \sprintf(
            'raport_%s_%s_sem%s.pdf',
            $raport->student->NIS,
            str_replace('/', '-', $raport->period->tahun_ajaran),
            $raport->period->semester
        );

        return Pdf::loadView('raport.pdf', compact('raport'))
            ->download($filename);
    }
}
