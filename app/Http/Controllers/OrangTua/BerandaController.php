<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Services\Attendance\StudentAttendanceService;
use App\Services\User\StudentProfileService;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function __construct(
        private StudentProfileService    $studentProfileService,
        private StudentAttendanceService $attendanceService,
    ) {}

    // Dashboard orang tua: ringkasan absensi bulan ini per anak.
    public function index(): View
    {
        $students = $this->studentProfileService->getAllByUserId(auth()->id());

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

        return view('orang-tua.beranda', compact('absensiPerAnak'));
    }
}
