<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\StudentReportCard;
use App\Services\ReportCard\ReportCardPdfService;
use App\Services\ReportCard\ReportCardService;
use App\Services\User\StudentProfileService;
use Illuminate\View\View;

class AkademikController extends Controller
{
    public function __construct(
        private StudentProfileService $studentProfileService,
        private ReportCardService     $reportCardService,
        private ReportCardPdfService  $pdfService,
    ) {}

    // Daftar raport per anak — hanya raport yang sudah approved yang bisa diunduh.
    public function index(): View
    {
        $students = $this->studentProfileService
            ->getAllByUserId(auth()->id())
            ->load('classRoom');

        // Map: student_id → collection raport approved milik anak itu
        $raportPerAnak = $students->mapWithKeys(fn($student) => [
            $student->student_id => $this->reportCardService
                ->getByStudent($student->student_id)
                ->load('period')
                ->sortByDesc(fn($r) => $r->period->tahun_ajaran . $r->period->semester),
        ]);

        return view('orang-tua.akademik.index', compact('students', 'raportPerAnak'));
    }

    // Download PDF raport. Hanya boleh jika raport sudah approved dan milik anak orang tua ini.
    public function download(int $id)
    {
        $raport = StudentReportCard::with('student')->findOrFail($id);

        abort_if($raport->student->user_id !== auth()->id(), 403);
        abort_if($raport->status !== 'approved', 403, 'Raport belum disetujui.');

        return $this->pdfService->download($id);
    }
}
