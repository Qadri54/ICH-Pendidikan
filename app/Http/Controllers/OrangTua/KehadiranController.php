<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Services\Attendance\StudentAttendanceService;
use App\Services\User\StudentProfileService;
use Illuminate\View\View;

class KehadiranController extends Controller
{
    public function __construct(
        private StudentProfileService $studentService,
        private StudentAttendanceService $attendanceService,
    ) {}

    public function index(): View
    {
        $students = $this->studentService->getAllByUserId(auth()->id())->load('classRoom');

        $kehadiranData = $students->map(function ($student) {
            $records = $this->attendanceService->getAll(['student_id' => $student->student_id]);

            $thisMonth = $records->filter(fn ($r) => $r->created_at->isCurrentMonth());

            return [
                'student' => $student,
                'records' => $records->take(30),
                'summary' => [
                    'izin'              => $thisMonth->where('status', 'izin')->count(),
                    'sakit'             => $thisMonth->where('status', 'sakit')->count(),
                    'tanpa_keterangan'  => $thisMonth->where('status', 'tanpa keterangan')->count(),
                ],
            ];
        });

        return view('orang-tua.kehadiran.index', compact('kehadiranData'));
    }
}
