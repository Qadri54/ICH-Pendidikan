<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Services\Attendance\AttendanceService;
use App\Services\Attendance\GeofenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiGuruController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
        private GeofenceService   $geofenceService,
    ) {}

    public function index(): View
    {
        $teacherId   = $this->resolveTeacherId();
        $todayRecord = $this->attendanceService->getTodayRecord($teacherId);
        $zone        = $this->geofenceService->getZone();

        return view('guru.absensi-guru.index', compact('todayRecord', 'zone'));
    }

    public function checkIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
            'selfie'    => 'required|image|max:5120',
        ]);

        $teacherId = $this->resolveTeacherId();

        try {
            $this->attendanceService->checkIn([
                ...$validated,
                'teacher_id' => $teacherId,
            ]);

            return redirect()->route('guru.absensi-guru.index')
                ->with('success', 'Check-in berhasil. Selamat bekerja!');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function izinSakit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status'          => 'required|in:Hadir,Izin,Sakit,Tanpa Keterangan',
            'keterangan_izin' => 'nullable|required_if:status,Izin|string|max:255',
        ]);

        $teacherId = $this->resolveTeacherId();

        try {
            $this->attendanceService->recordIzinSakit([
                'status'          => $validated['status'],
                'teacher_id'      => $teacherId,
                'keterangan_izin' => $validated['status'] === 'Izin' ? ($validated['keterangan_izin'] ?? null) : null,
            ]);

            return redirect()->route('guru.absensi-guru.index')
                ->with('success', 'Absensi berhasil dicatat.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function resolveTeacherId(): int
    {
        return Teacher::where('user_id', auth()->id())->firstOrFail()->teacher_id;
    }
}
