<?php

namespace App\Services\ReportCard;

use App\Models\ReportCardSnapshot;
use App\Models\StudentAttendance;
use App\Models\StudentReportCard;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ReportCardPdfService
{
    public function __construct(private ReportCardSnapshotService $snapshotService) {}

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

    private function buildPdfFromLive(StudentReportCard $raport)
    {
        $attendance = $this->getAttendance($raport);
        $kepalaSekolah = $this->getKepalaSekolah();

        return Pdf::loadView('raport.pdf', compact('raport', 'attendance', 'kepalaSekolah'))
            ->setPaper('a4', 'portrait');
    }

    private function buildPdfFromSnapshot(array $snapshotData)
    {
        $viewData = $this->snapshotService->hydrateForPdf($snapshotData);

        return Pdf::loadView('raport.pdf', $viewData)
            ->setPaper('a4', 'portrait');
    }

    private function buildPdf(int $reportCardId)
    {
        $snapshot = ReportCardSnapshot::where('report_card_id', $reportCardId)->first();

        if ($snapshot) {
            return $this->buildPdfFromSnapshot($snapshot->snapshot_data);
        }

        return $this->buildPdfFromLive($this->loadRaport($reportCardId));
    }

    public function generate(int $reportCardId): string
    {
        $pdf = $this->buildPdf($reportCardId);

        $path = "raport/pdf/{$reportCardId}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    public function download(int $reportCardId): Response
    {
        $snapshot = ReportCardSnapshot::where('report_card_id', $reportCardId)->first();

        if ($snapshot) {
            $data = $snapshot->snapshot_data;
            $filename = \sprintf(
                'raport_%s_%s_sem%s.pdf',
                $data['student']['NIS'] ?? 'unknown',
                str_replace('/', '-', $data['period']['tahun_ajaran'] ?? ''),
                $data['period']['semester'] ?? '',
            );

            return $this->buildPdfFromSnapshot($data)->download($filename);
        }

        $raport = $this->loadRaport($reportCardId);

        $filename = \sprintf(
            'raport_%s_%s_sem%s.pdf',
            $raport->student->NIS,
            str_replace('/', '-', $raport->period->tahun_ajaran),
            $raport->period->semester
        );

        return $this->buildPdfFromLive($raport)->download($filename);
    }
}
