<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Teacher;
use App\Services\Attendance\AttendanceService;
use Carbon\Carbon;
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
        if (empty($filters['tanggal'])) {
            $filters['tanggal'] = now()->toDateString();
        }
        $teachers = Teacher::with('user')->orderBy('teacher_id')->get();

        $records = null;

        if (!empty($filters['tanggal'])) {
            $recordsQuery = AttendanceRecord::with(['teacher.user'])
                ->whereDate('created_at', $filters['tanggal'])
                ->latest();

            if (!empty($filters['status'])) {
                $recordsQuery->where('attendance_status', $filters['status']);
            }
            if (!empty($filters['teacher_id'])) {
                $recordsQuery->where('teacher_id', $filters['teacher_id']);
            }

            $records = $recordsQuery->paginate(15, ['*'], 'page')
                ->appends($request->only(['tanggal', 'status', 'teacher_id', 'guru', 'bulan']));
        }

        $selectedGuru = $request->integer('guru') ?: null;
        $teacherRecap = [];

        if ($selectedGuru) {
            $bulan  = $request->input('bulan', now()->format('Y-m'));
            $parsed = Carbon::createFromFormat('Y-m', $bulan);

            $baseQuery = AttendanceRecord::where('teacher_id', $selectedGuru)
                ->whereYear('check_in_time', $parsed->year)
                ->whereMonth('check_in_time', $parsed->month);

            $summaryRecords = (clone $baseQuery)->get();

            $recapRecords = $baseQuery->orderBy('check_in_time')
                ->paginate(10, ['*'], 'recap_page')
                ->appends($request->only(['tanggal', 'status', 'teacher_id', 'guru', 'bulan']));

            $teacherRecap = [
                'bulan'   => $bulan,
                'records' => $recapRecords,
                'summary' => [
                    'hadir'            => $summaryRecords->where('attendance_status', 'Hadir')->count(),
                    'izin'             => $summaryRecords->where('attendance_status', 'Izin')->count(),
                    'sakit'            => $summaryRecords->where('attendance_status', 'Sakit')->count(),
                    'tanpa_keterangan' => $summaryRecords->where('attendance_status', 'Tanpa Keterangan')->count(),
                    'diluar_jangkauan' => $summaryRecords->where('attendance_status', 'Diluar Jangkauan')->count(),
                    'total'            => $summaryRecords->count(),
                ],
            ];
        }

        return view('admin.absensi-guru.index', compact(
            'records', 'teachers', 'filters', 'selectedGuru', 'teacherRecap'
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
            'teacher_id'      => 'required|exists:teachers,teacher_id',
            'status'          => 'required|in:Hadir,Izin,Sakit,Tanpa Keterangan',
            'keterangan_izin' => 'nullable|required_if:status,Izin|string|max:255',
        ]);

        try {
            $this->attendanceService->recordIzinSakit([
                'teacher_id'      => (int) $data['teacher_id'],
                'status'          => $data['status'],
                'keterangan_izin' => $data['status'] === 'Izin' ? ($data['keterangan_izin'] ?? null) : null,
            ]);

            return redirect()->route('admin.absensi-guru.index')
                ->with('success', 'Absensi berhasil dicatat.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, AttendanceRecord $record): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:Hadir,Izin,Sakit,Tanpa Keterangan',
        ]);

        $record->update([
            'attendance_status' => $data['status'],
        ]);

        return back()->with('success', 'Status absensi berhasil diperbarui.');
    }
}
