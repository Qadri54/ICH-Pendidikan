<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ReligiousTeacher;
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

    // Tampilkan status absensi guru hari ini beserta form check-in/out.
    public function index(): View
    {
        [$teacherId, $religiousTeacherId] = $this->resolveIds();
        $todayRecord = $this->attendanceService->getTodayRecord($teacherId, $religiousTeacherId);
        $zone        = $this->geofenceService->getZone();

        return view('guru.absensi-guru.index', compact('todayRecord', 'zone'));
    }

    // Proses check-in dengan GPS dan selfie (koordinat dikirim dari JavaScript browser).
    public function checkIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
            'selfie'    => 'required|image|max:5120',
        ]);

        [$teacherId, $religiousTeacherId] = $this->resolveIds();

        try {
            $this->attendanceService->checkIn([
                ...$validated,
                'teacher_id'           => $teacherId,
                'religious_teacher_id' => $religiousTeacherId,
            ]);

            return redirect()->route('guru.absensi-guru.index')
                ->with('success', 'Check-in berhasil. Selamat bekerja!');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Proses check-out dengan GPS.
    public function checkOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'record_id' => 'required|exists:attendance_records,attendance_record_id',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            $this->attendanceService->checkOut(
                (int) $validated['record_id'],
                (float) $validated['latitude'],
                (float) $validated['longitude']
            );

            return redirect()->route('guru.absensi-guru.index')
                ->with('success', 'Check-out berhasil. Sampai jumpa!');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Guru mencatat izin atau sakit sendiri (tanpa GPS).
    public function izinSakit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Hadir,Izin,Sakit,Tanpa Keterangan',
        ]);

        [$teacherId, $religiousTeacherId] = $this->resolveIds();

        try {
            $this->attendanceService->recordIzinSakit([
                'status'               => $validated['status'],
                'teacher_id'           => $teacherId,
                'religious_teacher_id' => $religiousTeacherId,
            ]);

            return redirect()->route('guru.absensi-guru.index')
                ->with('success', 'Absensi berhasil dicatat.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Tentukan teacher_id atau religious_teacher_id berdasarkan profil user.
    // Guru TK → teachers; Guru Ngaji/Quran/Iqra → religious_teachers.
    private function resolveIds(): array
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();

        if ($teacher) {
            return [$teacher->teacher_id, null];
        }

        $religious = ReligiousTeacher::where('user_id', auth()->id())->firstOrFail();

        return [null, $religious->religious_teacher_id];
    }
}
