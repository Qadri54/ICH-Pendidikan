<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Student;
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

    public function index(): View
    {
        $students = $this->studentProfileService
            ->getAllByUserId(auth()->id())
            ->load('classRoom');

        $data = $students->map(function ($student) {
            $raports = $this->reportCardService
                ->getByStudent($student->student_id)
                ->load('period');

            $approvedCount = $raports->where('status', 'approved')->count();
            $totalCount = $raports->count();

            return [
                'student' => $student,
                'approvedCount' => $approvedCount,
                'totalCount' => $totalCount,
            ];
        });

        return view('orang-tua.akademik.index', compact('data'));
    }

    public function detail(Student $student): View
    {
        abort_if($student->user_id !== auth()->id(), 403);

        $student->load('classRoom');

        $raports = $this->reportCardService
            ->getByStudent($student->student_id)
            ->load('period')
            ->sortByDesc(fn($r) => $r->period->tahun_ajaran . $r->period->semester);

        return view('orang-tua.akademik.detail', compact('student', 'raports'));
    }

    public function download(int $id)
    {
        $raport = StudentReportCard::with('student')->findOrFail($id);

        abort_if($raport->student->user_id !== auth()->id(), 403);
        abort_if($raport->status !== 'approved', 403, 'Raport belum disetujui.');

        return $this->pdfService->download($id);
    }
}
