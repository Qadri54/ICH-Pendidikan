<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Services\Attendance\StudentAttendanceService;
use App\Services\User\StudentProfileService;
use App\Models\SppInvoice;
use App\Models\StudentReportCard;
use App\Models\StudentPassbook;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function __construct(
        private StudentProfileService    $studentProfileService,
        private StudentAttendanceService $attendanceService,
    ) {}

    // Dashboard orang tua: ringkasan absensi, notifikasi tunggakan SPP, info raport, dan tabungan.
    public function index(): View
    {
        $students = $this->studentProfileService->getAllByUserId(auth()->id());
        $studentIds = $students->pluck('student_id')->toArray();

        // Absensi per anak
        $absensiPerAnak = $students->map(function ($student) {
            $records   = $this->attendanceService->getAll(['student_id' => $student->student_id]);
            $thisMonth = $records->filter(fn($r) => $r->created_at->isCurrentMonth());

            return [
                'student' => $student,
                'izin'    => $thisMonth->where('status', 'izin')->count(),
                'sakit'   => $thisMonth->where('status', 'sakit')->count(),
                'alfa'    => $thisMonth->where('status', 'tanpa keterangan')->count(),
            ];
        });

        // Tunggakan SPP
        $unpaidSpp = SppInvoice::whereIn('student_id', $studentIds)
            ->whereIn('status', ['unpaid', 'overdue'])
            ->with('student')
            ->get();

        // Raport Terbaru (Approved)
        $latestRaports = StudentReportCard::whereIn('student_id', $studentIds)
            ->where('status', 'approved')
            ->with(['student', 'period'])
            ->orderByDesc('updated_at')
            ->take(1)
            ->get();

        // Total Tabungan
        $totalTabungan = StudentPassbook::whereIn('student_id', $studentIds)
            ->sum('current_balance');

        return view('orang-tua.beranda', compact('absensiPerAnak', 'unpaidSpp', 'latestRaports', 'totalTabungan'));
    }
}
