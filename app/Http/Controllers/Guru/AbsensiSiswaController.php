<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Services\Attendance\StudentAttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiSiswaController extends Controller
{
    public function __construct(private StudentAttendanceService $attendanceService) {}

    public function index(): View
    {
        $teacher   = Teacher::where('user_id', auth()->id())->firstOrFail();
        $classroom = ClassRoom::with('students')
            ->where('homeroom_teacher_id', $teacher->teacher_id)
            ->first();

        if (! $classroom) {
            return view('guru.absensi.index', [
                'classroom'     => null,
                'students'      => collect(),
                'todayAbsences' => collect(),
            ]);
        }

        $students      = $classroom->students()->orderBy('nama_siswa')->get();
        $todayAbsences = $this->attendanceService->getTodayByClass($classroom->class_id);

        return view('guru.absensi.index', compact('classroom', 'students', 'todayAbsences'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher   = Teacher::where('user_id', auth()->id())->firstOrFail();
        $classroom = ClassRoom::where('homeroom_teacher_id', $teacher->teacher_id)->firstOrFail();

        $validated = $request->validate([
            'absences'               => 'nullable|array',
            'absences.*.student_id'  => 'required|integer|exists:students,student_id',
            'absences.*.status'      => 'required|in:izin,sakit,tanpa keterangan',
        ]);

        $count = $this->attendanceService->recordBulk(
            $teacher->teacher_id,
            $validated['absences'] ?? []
        );

        $message = $count > 0
            ? "{$count} siswa berhasil diinput sebagai tidak hadir."
            : 'Tidak ada perubahan — semua siswa mungkin sudah diinput hari ini.';

        return redirect()->route('guru.absensi.index')->with('success', $message);
    }
}
