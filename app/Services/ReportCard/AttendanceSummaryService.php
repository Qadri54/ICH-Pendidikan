<?php

namespace App\Services\ReportCard;

use App\Models\AcademicPeriod;
use App\Models\StudentAttendance;
use Illuminate\Support\Carbon;

class AttendanceSummaryService
{
    // Hitung rekap absensi satu siswa dalam rentang waktu satu periode akademik.
    // Service ini hanya MEMBACA data — tidak menyimpan apapun ke database.
    // Data absensi sudah ada di tabel student_attendance yang dikelola modul absensi.
    //
    // Cara kerja query:
    //   1. Filter by student_id → hanya absensi milik siswa ini.
    //   2. whereBetween created_at → hanya absensi dalam rentang tanggal periode.
    //      startOfDay() dan endOfDay() memastikan hari pertama dan terakhir ikut terhitung.
    //   3. selectRaw + groupBy → hitung jumlah per status langsung di MySQL (lebih efisien
    //      daripada menarik semua baris ke PHP lalu menghitung manual).
    //   4. pluck('total', 'status') → ubah hasil menjadi ['hadir' => 45, 'izin' => 2, ...].
    //
    // Catatan: nilai status 'tanpa keterangan' di database menggunakan spasi (bukan underscore),
    //   sehingga key di array return diberi nama 'tanpa_keterangan' untuk konsistensi PHP.
    // ?? 0 → fallback ke 0 jika status tersebut tidak ada sama sekali di periode ini.
    public function getSummary(int $studentId, int $periodId): array
    {
        $period = AcademicPeriod::findOrFail($periodId);

        $counts = StudentAttendance::where('student_id', $studentId)
            ->whereBetween('created_at', [
                Carbon::parse($period->tanggal_mulai)->startOfDay(),
                Carbon::parse($period->tanggal_selesai)->endOfDay(),
            ])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'hadir'            => (int) ($counts['hadir'] ?? 0),
            'izin'             => (int) ($counts['izin'] ?? 0),
            'sakit'            => (int) ($counts['sakit'] ?? 0),
            'tanpa_keterangan' => (int) ($counts['tanpa keterangan'] ?? 0),
            'total'            => (int) $counts->sum(),
        ];
    }
}
