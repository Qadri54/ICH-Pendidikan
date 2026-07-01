<?php

namespace App\Services\Attendance;

use App\Models\AttendanceRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{
    public function __construct(
        private GeofenceService $geofenceService,
    ) {}

    // Proses check-in guru dengan GPS dan foto selfie.
    // Alur:
    //   1. Pastikan guru belum check-in hari ini
    //   2. Validasi geofence via GeofenceService
    //   3. Simpan foto selfie ke storage
    //   4. Buat record baru dengan status Masuk
    // $data harus berisi: teacher_id, latitude, longitude, accuracy, selfie (UploadedFile)
    public function checkIn(array $data): AttendanceRecord
    {
        $teacherId = $data['teacher_id'];

        if ($this->getTodayRecord($teacherId)) {
            throw new \InvalidArgumentException('Anda sudah melakukan absensi hari ini.');
        }

        $isWithinGeofence = $this->geofenceService->isWithinZone(
            (float) $data['latitude'],
            (float) $data['longitude']
        );

        if (! $isWithinGeofence) {
            throw new \InvalidArgumentException('Anda berada di luar area sekolah. Check-in hanya dapat dilakukan di dalam radius geofence.');
        }

        $selfiePath = $data['selfie']->store('attendance/selfies', 'public');

        return AttendanceRecord::create([
            'teacher_id'          => $teacherId,
            'check_in_time'       => now(),
            'check_in_latitude'   => $data['latitude'],
            'check_in_longitude'  => $data['longitude'],
            'check_in_accuracy'   => $data['accuracy'],
            'selfie_path'         => $selfiePath,
            'is_within_geofence'  => 'ya',
            'attendance_status'   => 'Hadir',
        ]);
    }

    // Input absensi Izin atau Sakit — tanpa GPS dan tanpa selfie.
    // Validasi: guru belum punya record absensi hari ini.
    public function recordIzinSakit(array $data): AttendanceRecord
    {
        $teacherId = $data['teacher_id'];

        if ($this->getTodayRecord($teacherId)) {
            throw new \InvalidArgumentException('Anda sudah melakukan absensi hari ini.');
        }

        return AttendanceRecord::create([
            'teacher_id'        => $teacherId,
            'attendance_status' => $data['status'],
        ]);
    }

    // Ambil record absensi hari ini milik guru yang sedang login.
    // Mengembalikan null jika guru belum absen hari ini.
    // Dipakai untuk: cek status di halaman absensi guru + validasi duplikasi.
    public function getTodayRecord(int $teacherId): ?AttendanceRecord
    {
        return AttendanceRecord::whereDate('created_at', Carbon::today())
            ->where('teacher_id', $teacherId)
            ->first();
    }

    // Ambil semua record absensi dengan filter opsional.
    // Filter yang tersedia: tanggal, status, teacher_id.
    // Dipakai di halaman rekap admin.
    public function getAll(array $filters = []): Collection
    {
        $query = AttendanceRecord::with(['teacher.user'])
            ->latest();

        if (!empty($filters['tanggal'])) {
            $query->whereDate('created_at', $filters['tanggal']);
        }
        if (!empty($filters['status'])) {
            $query->where('attendance_status', $filters['status']);
        }
        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        return $query->get();
    }

    // Rekap absensi per guru untuk bulan dan tahun tertentu.
    // Menghitung jumlah Masuk, Izin, dan Sakit masing-masing guru.
    // Return array dengan struktur:
    // [
    //   ['nama' => '...', 'masuk' => 20, 'izin' => 1, 'sakit' => 0],
    //   ...
    // ]
    public function getMonthlyRecap(int $year, int $month): Collection
    {
        return AttendanceRecord::with(['teacher.user'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get()
            ->groupBy('teacher_id')
            ->map(function ($records) {
                $first = $records->first();
                $nama  = $first->teacher?->user?->name ?? 'Tidak diketahui';

                return [
                    'nama'              => $nama,
                    'hadir'             => $records->where('attendance_status', 'Hadir')->count(),
                    'izin'              => $records->where('attendance_status', 'Izin')->count(),
                    'sakit'             => $records->where('attendance_status', 'Sakit')->count(),
                    'tanpa_keterangan'  => $records->where('attendance_status', 'Tanpa Keterangan')->count(),
                ];
            })
            ->values();
    }
}
