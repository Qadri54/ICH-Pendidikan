<?php

namespace App\Services\Export;

use App\Models\ClassRoom;
use App\Models\ReligiousTeacher;
use App\Models\SavingLedger;
use App\Models\SppInvoice;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\Attendance\AttendanceService;
use App\Services\Attendance\StudentAttendanceService;
use App\Services\Registration\RegistrationFeeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanExportService
{
    public function __construct(
        private StudentAttendanceService $studentAttendanceService,
        private AttendanceService $attendanceService,
        private RegistrationFeeService $registrationFeeService,
    ) {}

    public function exportKeuanganPdf(): Response
    {
        $invoices         = SppInvoice::with('student.classRoom')->latest('jatuh_tempo')->get();
        $totalSpp         = $invoices->where('status', 'paid')->sum('jumlah');
        $totalPendaftaran = $this->registrationFeeService->getTotalCollected();
        $totalTabungan    = SavingLedger::sum('total_balance');

        $pdf = Pdf::loadView('exports.keuangan-pdf', compact(
            'invoices', 'totalSpp', 'totalPendaftaran', 'totalTabungan'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-keuangan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportKeuanganCsv(): StreamedResponse
    {
        $invoices = SppInvoice::with('student.classRoom')->latest('jatuh_tempo')->get();

        return new StreamedResponse(function () use ($invoices) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nama Siswa', 'Kelas', 'Periode', 'Jumlah', 'Jatuh Tempo', 'Status']);

            foreach ($invoices as $inv) {
                fputcsv($handle, [
                    $inv->student?->nama_siswa ?? '-',
                    $inv->student?->classRoom?->nama_kelas ?? '-',
                    $inv->tanggal_tahun?->format('Y-m') ?? '-',
                    $inv->jumlah,
                    $inv->jatuh_tempo?->format('Y-m-d') ?? '-',
                    $inv->status,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-keuangan-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function exportAbsensiSiswaPdf(int $classId, int $year, int $month): Response
    {
        $classroom = ClassRoom::findOrFail($classId);
        $recap     = $this->studentAttendanceService->getMonthlyRecap($classId, $year, $month);

        $pdf = Pdf::loadView('exports.absensi-siswa-pdf', compact(
            'classroom', 'recap', 'year', 'month'
        ));

        return $pdf->download("rekap-absensi-siswa-{$classroom->nama_kelas}-{$year}-{$month}.pdf");
    }

    public function exportAbsensiSiswaCsv(int $classId, int $year, int $month): StreamedResponse
    {
        $classroom = ClassRoom::findOrFail($classId);
        $recap     = $this->studentAttendanceService->getMonthlyRecap($classId, $year, $month);

        return new StreamedResponse(function () use ($recap) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Nama Siswa', 'Izin', 'Sakit', 'Tanpa Keterangan', 'Total']);

            foreach ($recap as $i => $row) {
                $total = $row['izin'] + $row['sakit'] + $row['tanpa_keterangan'];
                fputcsv($handle, [$i + 1, $row['nama'], $row['izin'], $row['sakit'], $row['tanpa_keterangan'], $total]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"rekap-absensi-siswa-{$classroom->nama_kelas}-{$year}-{$month}.csv\"",
        ]);
    }

    public function exportAbsensiGuruPdf(int $year, int $month): Response
    {
        $recap = $this->attendanceService->getMonthlyRecap($year, $month);

        $pdf = Pdf::loadView('exports.absensi-guru-pdf', compact('recap', 'year', 'month'));

        return $pdf->download("rekap-absensi-guru-{$year}-{$month}.pdf");
    }

    public function exportAbsensiGuruCsv(int $year, int $month): StreamedResponse
    {
        $recap = $this->attendanceService->getMonthlyRecap($year, $month);

        return new StreamedResponse(function () use ($recap) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Nama Guru', 'Masuk', 'Izin', 'Sakit']);

            foreach ($recap as $i => $row) {
                fputcsv($handle, [$i + 1, $row['nama'], $row['masuk'], $row['izin'], $row['sakit']]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"rekap-absensi-guru-{$year}-{$month}.csv\"",
        ]);
    }
}
