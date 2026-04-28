<?php

namespace App\Services\Attendance;

use App\Models\StudentAttendance;
use App\Models\ClassRoom;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class StudentAttendanceService
{
    // Input absensi untuk satu siswa.
    // Hanya mencatat siswa yang TIDAK hadir (izin/sakit/tanpa keterangan).
    // teacher_id diisi dari guru yang sedang login agar tercatat siapa yang input.
    // Validasi: satu siswa hanya boleh punya satu record per hari.
    public function record(int $studentId, int $teacherId, string $status): StudentAttendance
    {
        $existing = $this->getTodayRecord($studentId);

        if ($existing) {
            throw new \InvalidArgumentException('Absensi siswa ini untuk hari ini sudah diinput.');
        }

        return StudentAttendance::create([
            'student_id' => $studentId,
            'teacher_id' => $teacherId,
            'status'     => $status,
            'created_at' => now(),
        ]);
    }

    // Input absensi massal untuk satu kelas sekaligus.
    // Guru memilih seluruh siswa yang tidak hadir dalam satu form.
    // $absences = [
    //   ['student_id' => 1, 'status' => 'izin'],
    //   ['student_id' => 2, 'status' => 'sakit'],
    // ]
    // Siswa yang tidak ada di array dianggap hadir (tidak perlu diinput).
    // Menggunakan bulk insert untuk efisiensi — satu query untuk semua siswa.
    public function recordBulk(int $teacherId, array $absences): int
    {
        $now  = now()->toDateTimeString();
        $rows = [];

        foreach ($absences as $item) {
            // Skip jika hari ini sudah ada record untuk siswa ini
            if ($this->getTodayRecord($item['student_id'])) {
                continue;
            }

            $rows[] = [
                'student_id' => $item['student_id'],
                'teacher_id' => $teacherId,
                'status'     => $item['status'],
                'created_at' => $now,
            ];
        }

        if (empty($rows)) {
            return 0;
        }

        StudentAttendance::insert($rows);

        return count($rows);
    }

    // Ambil record absensi hari ini untuk satu siswa.
    // Return null jika siswa hadir (tidak ada record = hadir).
    public function getTodayRecord(int $studentId): ?StudentAttendance
    {
        return StudentAttendance::where('student_id', $studentId)
            ->whereDate('created_at', Carbon::today())
            ->first();
    }

    // Ambil rekap absensi seluruh siswa dalam satu kelas untuk hari ini.
    // Dipakai guru untuk melihat siapa saja yang sudah diinput absen hari ini.
    // Return collection yang di-keyBy student_id untuk mudah di-lookup di view.
    public function getTodayByClass(int $classId): Collection
    {
        return StudentAttendance::with('student')
            ->whereHas('student', fn($q) => $q->where('class_id', $classId))
            ->whereDate('created_at', Carbon::today())
            ->get()
            ->keyBy('student_id');
    }

    // Ambil semua record absensi dengan filter opsional.
    // Filter: class_id, student_id, tanggal, status.
    // Dipakai admin untuk melihat rekap absensi siswa.
    public function getAll(array $filters = []): Collection
    {
        $query = StudentAttendance::with(['student.classRoom', 'teacher.user'])
            ->latest('created_at');

        if (!empty($filters['tanggal'])) {
            $query->whereDate('created_at', $filters['tanggal']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }
        if (!empty($filters['class_id'])) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $filters['class_id']));
        }

        return $query->get();
    }

    // Rekap absensi siswa per bulan untuk satu kelas.
    // Menghitung jumlah izin, sakit, tanpa keterangan per siswa.
    // Siswa yang tidak ada di rekap berarti tidak pernah absen (hadir semua).
    public function getMonthlyRecap(int $classId, int $year, int $month): Collection
    {
        return StudentAttendance::with('student')
            ->whereHas('student', fn($q) => $q->where('class_id', $classId))
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get()
            ->groupBy('student_id')
            ->map(function ($records) {
                $student = $records->first()->student;

                return [
                    'nama'              => $student->nama_siswa,
                    'izin'              => $records->where('status', 'izin')->count(),
                    'sakit'             => $records->where('status', 'sakit')->count(),
                    'tanpa_keterangan'  => $records->where('status', 'tanpa keterangan')->count(),
                ];
            })
            ->values();
    }
}
