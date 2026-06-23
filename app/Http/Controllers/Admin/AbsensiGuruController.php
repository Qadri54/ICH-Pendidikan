<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Services\Attendance\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiGuruController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
    ) {}

    public function index(Request $request): View
    {
        $filters  = $request->only(['tanggal', 'status', 'teacher_id']);
        $records  = $this->attendanceService->getAll($filters);
        $teachers = Teacher::with('user')->orderBy('teacher_id')->get();

        return view('admin.absensi-guru.index', compact(
            'records', 'teachers', 'filters'
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

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'teacher_id' => 'required|exists:teachers,teacher_id',
            'status'     => 'required|in:Hadir,Izin,Sakit,Tanpa Keterangan',
        ]);

        try {
            $this->attendanceService->recordIzinSakit([
                'teacher_id' => (int) $data['teacher_id'],
                'status'     => $data['status'],
            ]);

            return redirect()->route('admin.absensi-guru.index')
                ->with('success', 'Absensi berhasil dicatat.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
