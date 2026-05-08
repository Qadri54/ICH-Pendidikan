<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Services\Attendance\StudentAttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiSiswaController extends Controller
{
    public function __construct(private StudentAttendanceService $attendanceService) {}

    public function index(Request $request): View
    {
        $classes       = ClassRoom::with('homeroomTeacher.user')->orderBy('nama_kelas')->get();
        $selectedClass = $request->integer('class_id') ?: null;
        $selectedDate  = $request->input('tanggal', today()->format('Y-m-d'));
        $isToday       = $selectedDate === today()->format('Y-m-d');

        $classroom = null;
        $students  = collect();
        $absences  = collect();

        if ($selectedClass) {
            $classroom = ClassRoom::with('students')->find($selectedClass);
            if ($classroom) {
                $students = $classroom->students()->orderBy('nama_siswa')->get();
                $absences = $this->attendanceService
                    ->getAll(['class_id' => $selectedClass, 'tanggal' => $selectedDate])
                    ->keyBy('student_id');
            }
        }

        return view('admin.absensi.index', compact(
            'classes', 'selectedClass', 'selectedDate', 'isToday',
            'classroom', 'students', 'absences'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id'               => 'required|exists:classes,class_id',
            'absences'               => 'nullable|array',
            'absences.*.student_id'  => 'required|integer|exists:students,student_id',
            'absences.*.status'      => 'required|in:izin,sakit,tanpa keterangan',
        ]);

        $count = $this->attendanceService->recordBulk(
            null, // admin tidak punya teacher_id
            $validated['absences'] ?? []
        );

        $message = $count > 0
            ? "{$count} siswa berhasil diinput."
            : 'Tidak ada perubahan — mungkin sudah diinput sebelumnya.';

        return redirect()->route('admin.absensi.index', [
            'class_id' => $validated['class_id'],
            'tanggal'  => today()->format('Y-m-d'),
        ])->with('success', $message);
    }
}
