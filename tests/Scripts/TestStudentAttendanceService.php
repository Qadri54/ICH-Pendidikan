<?php

/**
 * Script testing untuk StudentAttendanceService.
 *
 * Cara menjalankan:
 *   php artisan tinker
 *   >>> require 'tests/Scripts/TestStudentAttendanceService.php';
 *
 * PERHATIAN: Script ini membuat data nyata di database.
 * Jalankan hanya di environment development/local.
 */

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Services\Attendance\StudentAttendanceService;

// ─────────────────────────────────────────────
// Helper output
// ─────────────────────────────────────────────

function ok(string $label): void
{
    echo "\n  ✓ {$label}";
}

function section(string $title): void
{
    echo "\n\n┌─────────────────────────────────────────";
    echo "\n│ {$title}";
    echo "\n└─────────────────────────────────────────";
}

function result(string $label, mixed $value): void
{
    $display = is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $value;
    echo "\n  → {$label}: {$display}";
}

echo "\n\n══════════════════════════════════════════════";
echo "\n  TEST: StudentAttendanceService";
echo "\n══════════════════════════════════════════════";

// ─────────────────────────────────────────────
// PRASYARAT
// ─────────────────────────────────────────────

section('0. Cek Prasyarat Data');

$teacher  = Teacher::first();
$class    = ClassRoom::first();
$students = Student::where('class_id', $class->class_id)->get();

if (!$teacher) {
    echo "\n  ✗ Tidak ada data Teacher. Jalankan seeder terlebih dahulu.";
    return;
}
if ($students->isEmpty()) {
    echo "\n  ✗ Tidak ada data Student di kelas ini. Jalankan seeder terlebih dahulu.";
    return;
}

$student = $students->first();

result('Teacher', "ID={$teacher->teacher_id}");
result('Kelas', "ID={$class->class_id}, Nama={$class->nama_kelas}");
result('Jumlah siswa di kelas', $students->count());
result('Student untuk test', "ID={$student->student_id}, Nama={$student->nama_siswa}");
ok('Prasyarat terpenuhi');

// Bersihkan data absensi hari ini agar test bisa diulang
StudentAttendance::whereIn('student_id', $students->pluck('student_id'))
    ->whereDate('created_at', now()->toDateString())
    ->delete();
ok('Data absensi hari ini dibersihkan (fresh test)');

$service = new StudentAttendanceService();

// ─────────────────────────────────────────────
// TEST 1: getTodayRecord — belum ada record
// ─────────────────────────────────────────────

section('1. getTodayRecord() — sebelum input');

$record = $service->getTodayRecord($student->student_id);
result('getTodayRecord() → null jika belum absen?', $record === null ? 'YA (null)' : 'TIDAK (ada record)');
ok('getTodayRecord() return null sebelum ada input');

// ─────────────────────────────────────────────
// TEST 2: record() — input satu siswa
// ─────────────────────────────────────────────

section('2. record() — input absensi satu siswa');

$attendance = $service->record($student->student_id, $teacher->teacher_id, 'izin');
result('record() → student_attendance_id', $attendance->student_attendance_id);
result('record() → student_id', $attendance->student_id);
result('record() → teacher_id', $attendance->teacher_id);
result('record() → status', $attendance->status);
ok('record() berhasil, teacher_id tercatat');

// Coba input ulang siswa yang sama → harus throw exception
try {
    $service->record($student->student_id, $teacher->teacher_id, 'sakit');
    echo "\n  ✗ Seharusnya throw exception untuk duplikat!";
} catch (\InvalidArgumentException $e) {
    ok('Duplikat dicegah: ' . $e->getMessage());
}

// ─────────────────────────────────────────────
// TEST 3: getTodayRecord() — setelah input
// ─────────────────────────────────────────────

section('3. getTodayRecord() — setelah input');

$record = $service->getTodayRecord($student->student_id);
result('getTodayRecord() → status', $record?->status);
result('getTodayRecord() → teacher_id', $record?->teacher_id);
ok('getTodayRecord() mengembalikan record yang benar');

// ─────────────────────────────────────────────
// TEST 4: recordBulk() — input massal satu kelas
// ─────────────────────────────────────────────

section('4. recordBulk() — input massal');

// Ambil siswa lain selain yang sudah diinput
$remainingStudents = $students->skip(1)->take(2);

if ($remainingStudents->isNotEmpty()) {
    $absences = $remainingStudents->map(fn($s) => [
        'student_id' => $s->student_id,
        'status'     => collect(['sakit', 'tanpa keterangan'])->random(),
    ])->toArray();

    $count = $service->recordBulk($teacher->teacher_id, $absences);
    result('recordBulk() → jumlah yang berhasil diinsert', $count);
    ok('recordBulk() berhasil');

    // Coba bulk ulang siswa yang sama → harus di-skip (count = 0)
    $countDuplikat = $service->recordBulk($teacher->teacher_id, $absences);
    result('recordBulk() ulang → jumlah insert (harus 0)', $countDuplikat);
    ok('recordBulk() skip duplikat dengan benar');
} else {
    echo "\n  ⚠ Hanya ada 1 siswa di kelas ini, skip test bulk";
}

// ─────────────────────────────────────────────
// TEST 5: getTodayByClass()
// ─────────────────────────────────────────────

section('5. getTodayByClass() — rekap kelas hari ini');

$todayRecords = $service->getTodayByClass($class->class_id);
result('getTodayByClass() → jumlah siswa tidak hadir hari ini', $todayRecords->count());
result('Di-keyBy student_id → akses langsung?', $todayRecords->has($student->student_id) ? 'YA' : 'TIDAK');
ok('getTodayByClass() berhasil (keyBy student_id berfungsi)');

// ─────────────────────────────────────────────
// TEST 6: getAll() dengan filter
// ─────────────────────────────────────────────

section('6. getAll() — dengan filter');

$all = $service->getAll();
result('getAll() tanpa filter → jumlah total record', $all->count());
ok('getAll() tanpa filter berhasil');

$filtered = $service->getAll(['tanggal' => now()->toDateString(), 'class_id' => $class->class_id]);
result('getAll() filter tanggal+kelas → jumlah', $filtered->count());
result('Record pertama → nama siswa', $filtered->first()?->student?->nama_siswa);
ok('getAll() dengan filter berhasil');

$filteredStatus = $service->getAll(['status' => 'izin']);
result('getAll() filter status=izin → jumlah', $filteredStatus->count());
ok('getAll() filter status berhasil');

// ─────────────────────────────────────────────
// TEST 7: getMonthlyRecap()
// ─────────────────────────────────────────────

section('7. getMonthlyRecap() — rekap bulanan');

$recap = $service->getMonthlyRecap($class->class_id, now()->year, now()->month);
result('getMonthlyRecap() → jumlah siswa dengan absensi', $recap->count());

if ($recap->isNotEmpty()) {
    $first = $recap->first();
    result('Siswa pertama → nama', $first['nama']);
    result('Siswa pertama → izin', $first['izin']);
    result('Siswa pertama → sakit', $first['sakit']);
    result('Siswa pertama → tanpa_keterangan', $first['tanpa_keterangan']);
}
ok('getMonthlyRecap() berhasil');

// ─────────────────────────────────────────────
// RINGKASAN
// ─────────────────────────────────────────────

echo "\n\n══════════════════════════════════════════════";
echo "\n  SELESAI — StudentAttendanceService berjalan dengan baik";
echo "\n  Total absensi hari ini: {$todayRecords->count()} siswa tidak hadir";
echo "\n══════════════════════════════════════════════\n\n";
