<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\ReligiousTeacher;
use App\Services\Attendance\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiGuruController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
    ) {}

    // Rekap absensi semua guru dengan filter tanggal dan status.
    public function index(Request $request): View
    {
        $filters           = $request->only(['tanggal', 'status', 'teacher_id', 'religious_teacher_id']);
        $records           = $this->attendanceService->getAll($filters);
        $teachers          = Teacher::with('user')->orderBy('teacher_id')->get();
        $religiousTeachers = ReligiousTeacher::with('user')->orderBy('religious_teacher_id')->get();

        return view('admin.absensi-guru.index', compact(
            'records', 'teachers', 'religiousTeachers', 'filters'
        ));
    }

    public function recap(Request $request): View
    {
        $selectedYear  = $request->integer('year', now()->year);
        $selectedMonth = $request->integer('month', now()->month);

        $recap = $this->attendanceService->getMonthlyRecap($selectedYear, $selectedMonth);

        return view('admin.absensi-guru.recap', compact(
            'selectedYear', 'selectedMonth', 'recap'
        ));
    }

    // Admin mencatat izin/sakit atas nama guru yang tidak bisa input sendiri.
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tipe_guru'    => 'required|in:guru,guru_ngaji',
            'teacher_id'   => 'required_if:tipe_guru,guru|nullable|exists:teachers,teacher_id',
            'religious_id' => 'required_if:tipe_guru,guru_ngaji|nullable|exists:religious_teachers,religious_teacher_id',
            'status'       => 'required|in:Hadir,Izin,Sakit,Tanpa Keterangan',
        ]);

        try {
            $this->attendanceService->recordIzinSakit([
                'teacher_id'           => $data['tipe_guru'] === 'guru' ? (int) $data['teacher_id'] : null,
                'religious_teacher_id' => $data['tipe_guru'] === 'guru_ngaji' ? (int) $data['religious_id'] : null,
                'status'               => $data['status'],
            ]);

            return redirect()->route('admin.absensi-guru.index')
                ->with('success', 'Absensi berhasil dicatat.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
