<?php

namespace App\Services\ReportCard;

use App\Models\Role;
use App\Models\StudentAttendance;
use App\Models\StudentReportCard;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ReportCardPdfService
{
    private function loadRaport(int $reportCardId): StudentReportCard
    {
        return StudentReportCard::with([
            'student',
            'period',
            'classRoom',
            'homeroomTeacher.user',
            'narrativeAssessments.photos',
            'checklistAssessments.category.parent',
            'physicalMeasurement',
            'healthCondition',
        ])->findOrFail($reportCardId);
    }

    private function getAttendance(StudentReportCard $raport): array
    {
        $records = StudentAttendance::where('student_id', $raport->student_id)
            ->whereBetween('created_at', [
                $raport->period->tanggal_mulai,
                $raport->period->tanggal_selesai,
            ])
            ->get();

        return [
            'sakit'            => $records->where('status', 'sakit')->count(),
            'izin'             => $records->where('status', 'izin')->count(),
            'tanpa_keterangan' => $records->where('status', 'tanpa keterangan')->count(),
        ];
    }

    private function getKepalaSekolah(): string
    {
        $kepsek = User::whereHas('role', fn ($q) => $q->where('role_name', 'Kepala Sekolah'))
            ->first();

        return $kepsek?->name ?? 'Kepala Sekolah';
    }

    private function buildPdf(StudentReportCard $raport)
    {
        $attendance = $this->getAttendance($raport);
        $kepalaSekolah = $this->getKepalaSekolah();

        return Pdf::loadView('raport.pdf', compact('raport', 'attendance', 'kepalaSekolah'))
            ->setPaper('a4', 'portrait');
    }

    public function generate(int $reportCardId): string
    {
        $raport = $this->loadRaport($reportCardId);
        $pdf = $this->buildPdf($raport);

        $path = "raport/pdf/{$reportCardId}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    public function download(int $reportCardId): Response
    {
        $raport = $this->loadRaport($reportCardId);

        $filename = \sprintf(
            'raport_%s_%s_sem%s.pdf',
            $raport->student->NIS,
            str_replace('/', '-', $raport->period->tahun_ajaran),
            $raport->period->semester
        );

        return $this->buildPdf($raport)->download($filename);
    }
}
