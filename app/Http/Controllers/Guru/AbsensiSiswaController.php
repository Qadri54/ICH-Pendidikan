<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Services\Attendance\StudentAttendanceService;
use Carbon\Carbon;
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
            'absences'                    => 'required|array',
            'absences.*.student_id'       => 'required|integer|exists:students,student_id',
            'absences.*.status'           => 'required|in:hadir,izin,sakit,tanpa keterangan',
            'absences.*.keterangan_izin'  => 'nullable|string|max:255',
        ]);

        $count = $this->attendanceService->recordBulk(
            $teacher->teacher_id,
            $validated['absences']
        );

        $message = $count > 0
            ? "Absensi {$count} siswa berhasil disimpan."
            : 'Tidak ada perubahan — semua siswa sudah diinput hari ini.';

        return redirect()->route('guru.absensi.index')->with('success', $message);
    }

    public function rekap(): View
    {
        $teacher   = Teacher::where('user_id', auth()->id())->firstOrFail();
        $classroom = ClassRoom::where('homeroom_teacher_id', $teacher->teacher_id)->first();

        if (! $classroom) {
            return view('guru.absensi.rekap', [
                'classroom' => null,
                'records'   => collect(),
                'summary'   => ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0, 'total' => 0],
                'bulan'     => now()->format('Y-m'),
            ]);
        }

        $bulan  = request('bulan', now()->format('Y-m'));
        $parsed = Carbon::createFromFormat('Y-m', $bulan);

        $records = StudentAttendance::with(['student'])
            ->whereHas('student', fn ($q) => $q->where('class_id', $classroom->class_id))
            ->whereYear('created_at', $parsed->year)
            ->whereMonth('created_at', $parsed->month)
            ->latest('created_at')
            ->get();

        $summary = [
            'hadir' => $records->where('status', 'hadir')->count(),
            'izin'  => $records->where('status', 'izin')->count(),
            'sakit' => $records->where('status', 'sakit')->count(),
            'alpha' => $records->where('status', 'tanpa keterangan')->count(),
            'total' => $records->count(),
        ];

        return view('guru.absensi.rekap', compact('classroom', 'records', 'summary', 'bulan'));
    }
}
