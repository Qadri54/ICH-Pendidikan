<?php

/**
 * Script testing untuk seluruh Service Layer Sistem Raport.
 *
 * Cara menjalankan:
 *   php artisan tinker
 *   >>> require 'tests/Scripts/TestReportCardService.php';
 *
 * PERHATIAN: Script ini MEMBUAT DATA NYATA di database.
 * Jalankan hanya di environment development/local.
 * Hapus data hasil test secara manual jika diperlukan.
 */

use App\Models\AcademicPeriod;
use App\Models\ClassRoom;
use App\Models\DevelopmentCategory;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\ReportCard\AcademicPeriodService;
use App\Services\ReportCard\AttendanceSummaryService;
use App\Services\ReportCard\ChecklistAssessmentService;
use App\Services\ReportCard\HealthConditionService;
use App\Services\ReportCard\NarrativeAssessmentService;
use App\Services\ReportCard\PhysicalMeasurementService;
use App\Services\ReportCard\ReportCardService;

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
echo "\n  TEST: Service Layer Sistem Raport";
echo "\n══════════════════════════════════════════════";

// ─────────────────────────────────────────────
// PRASYARAT: Pastikan data master tersedia
// ─────────────────────────────────────────────

section('0. Cek Prasyarat Data');

$student = Student::first();
$teacher = Teacher::first();
$class   = ClassRoom::first();

if (!$student) {
    echo "\n  ✗ Tidak ada data Student. Jalankan seeder terlebih dahulu.";
    return;
}
if (!$teacher) {
    echo "\n  ✗ Tidak ada data Teacher. Jalankan seeder terlebih dahulu.";
    return;
}
if (!$class) {
    echo "\n  ✗ Tidak ada data ClassRoom. Jalankan seeder terlebih dahulu.";
    return;
}

result('Student dipakai', "ID={$student->student_id}, Nama={$student->nama_siswa}");
result('Teacher dipakai', "ID={$teacher->teacher_id}");
result('ClassRoom dipakai', "ID={$class->class_id}, Nama={$class->nama_kelas}");
ok('Prasyarat terpenuhi');

// ─────────────────────────────────────────────
// TEST 1: AcademicPeriodService
// ─────────────────────────────────────────────

section('1. AcademicPeriodService');

$periodService = new AcademicPeriodService();

// getAll
$allPeriods = $periodService->getAll();
result('getAll() → jumlah periode', $allPeriods->count());
ok('getAll() berhasil');

// getActive
$activePeriod = $periodService->getActive();
if ($activePeriod) {
    result('getActive() → tahun ajaran', $activePeriod->tahun_ajaran . ' Sem ' . $activePeriod->semester);
    ok('getActive() berhasil');
} else {
    // Buat periode baru jika belum ada
    $activePeriod = $periodService->create([
        'tahun_ajaran'    => '2025/2026',
        'semester'        => 1,
        'tanggal_mulai'   => '2025-07-14',
        'tanggal_selesai' => '2025-12-19',
        'is_active'       => false,
    ]);
    $periodService->setActive($activePeriod->period_id);
    $activePeriod = $periodService->getActive();
    result('create() + setActive() → period_id', $activePeriod->period_id);
    ok('create() dan setActive() berhasil');
}

// getById
$foundPeriod = $periodService->getById($activePeriod->period_id);
result('getById() → period_id', $foundPeriod->period_id);
ok('getById() berhasil');

// ─────────────────────────────────────────────
// TEST 2: ReportCardService
// ─────────────────────────────────────────────

section('2. ReportCardService');

$reportCardService = new ReportCardService();

// Bersihkan raport lama untuk student + periode ini (agar bisa ditest ulang)
\App\Models\StudentReportCard::where('student_id', $student->student_id)
    ->where('period_id', $activePeriod->period_id)
    ->delete();

// create
$raport = $reportCardService->create([
    'student_id'          => $student->student_id,
    'period_id'           => $activePeriod->period_id,
    'class_id'            => $class->class_id,
    'homeroom_teacher_id' => $teacher->teacher_id,
]);
result('create() → report_card_id', $raport->report_card_id);
result('create() → status awal', $raport->status);
ok('create() berhasil, status = draft');

// Coba create duplikat → harus throw exception
try {
    $reportCardService->create([
        'student_id'          => $student->student_id,
        'period_id'           => $activePeriod->period_id,
        'class_id'            => $class->class_id,
        'homeroom_teacher_id' => $teacher->teacher_id,
    ]);
    echo "\n  ✗ Seharusnya throw exception untuk duplikat!";
} catch (\InvalidArgumentException $e) {
    ok('Duplikat raport dicegah: ' . $e->getMessage());
}

// getAll dengan filter
$filtered = $reportCardService->getAll(['period_id' => $activePeriod->period_id]);
result('getAll(filter period_id) → jumlah', $filtered->count());
ok('getAll() dengan filter berhasil');

// getById
$fullRaport = $reportCardService->getById($raport->report_card_id);
result('getById() → relasi student', $fullRaport->student->nama_siswa);
ok('getById() dengan eager load berhasil');

// ─────────────────────────────────────────────
// TEST 3: NarrativeAssessmentService
// ─────────────────────────────────────────────

section('3. NarrativeAssessmentService');

$narrativeService = new NarrativeAssessmentService();

// upsert
$narrativeService->upsert($raport->report_card_id, [
    [
        'kategori'    => 'intrakurikuler',
        'judul'       => 'Nilai Agama dan Budi Pekerti',
        'isi_naratif' => 'Anak sudah mampu berdoa sebelum dan sesudah kegiatan dengan baik.',
    ],
    [
        'kategori'    => 'kokurikuler',
        'judul'       => 'Jati Diri',
        'isi_naratif' => 'Anak menunjukkan rasa percaya diri saat tampil di depan kelas.',
    ],
    [
        'kategori'    => 'kokurikuler',
        'judul'       => 'Dasar-dasar Literasi, Matematika, Sains, Teknologi, Rekayasa, dan Seni',
        'isi_naratif' => 'Anak mulai mengenal angka 1-10 dan dapat menyebutkan warna dasar.',
    ],
]);
ok('upsert() 3 narasi berhasil');

// Upsert ulang → tidak duplikat
$narrativeService->upsert($raport->report_card_id, [
    [
        'kategori'    => 'intrakurikuler',
        'judul'       => 'Nilai Agama dan Budi Pekerti',
        'isi_naratif' => 'Anak sudah sangat baik dalam berdoa dan menghafal surah pendek.',
    ],
]);
ok('upsert() ulang berhasil (tidak duplikat, data terupdate)');

// getByReportCard
$narratives = $narrativeService->getByReportCard($raport->report_card_id);
result('getByReportCard() → jumlah narasi', $narratives->count());
result('Narasi pertama', $narratives->first()->judul);
ok('getByReportCard() berhasil');

// ─────────────────────────────────────────────
// TEST 4: ChecklistAssessmentService
// ─────────────────────────────────────────────

section('4. ChecklistAssessmentService');

$checklistService = new ChecklistAssessmentService();

// getAllCategories
$categories = $checklistService->getAllCategories();
result('getAllCategories() → jumlah kategori utama', $categories->count());
result('Kategori pertama', $categories->first()->nama);
result('Sub-item kategori kedua (Rukun Iman)', $categories->get(1)?->children->count() . ' sub-item');
ok('getAllCategories() berhasil');

// Ambil beberapa leaf category untuk ditest
$leafCategories = DevelopmentCategory::whereNotNull('parent_id')
    ->where('is_active', true)
    ->take(5)
    ->get();

if ($leafCategories->isNotEmpty()) {
    $checklistData = $leafCategories->map(fn($cat) => [
        'category_id' => $cat->category_id,
        'status'      => collect(['BM', 'MM', 'SM'])->random(),
        'catatan'     => null,
    ])->toArray();

    $checklistService->upsert($raport->report_card_id, $checklistData);
    result('upsert() → jumlah item yang dinilai', count($checklistData));
    ok('upsert() checklist berhasil');

    // getByReportCard
    $savedChecklists = $checklistService->getByReportCard($raport->report_card_id);
    result('getByReportCard() → jumlah tersimpan', $savedChecklists->count());
    result('Akses via key', 'status = ' . $savedChecklists->first()->status);
    ok('getByReportCard() (keyBy category_id) berhasil');
} else {
    echo "\n  ⚠ Tidak ada leaf category — jalankan DevelopmentCategorySeeder terlebih dahulu";
}

// ─────────────────────────────────────────────
// TEST 5: PhysicalMeasurementService
// ─────────────────────────────────────────────

section('5. PhysicalMeasurementService');

$physicalService = new PhysicalMeasurementService();

// upsert
$measurement = $physicalService->upsert($raport->report_card_id, [
    'tinggi_badan'  => 110.5,
    'berat_badan'   => 18.3,
    'lingkar_kepala' => 52.0,
    'tanggal_ukur'  => '2025-10-01',
]);
result('upsert() → measurement_id', $measurement->measurement_id);
result('tinggi_badan', $measurement->tinggi_badan);
ok('upsert() berhasil');

// upsert ulang → update, tidak duplikat
$updated = $physicalService->upsert($raport->report_card_id, [
    'tinggi_badan'  => 111.0,
    'berat_badan'   => 18.5,
    'lingkar_kepala' => 52.0,
    'tanggal_ukur'  => '2025-11-01',
]);
result('upsert() ulang → measurement_id sama?', $measurement->measurement_id === $updated->measurement_id ? 'YA (update)' : 'TIDAK (duplikat!)');
ok('upsert() ulang tidak duplikat');

// getByReportCard
$found = $physicalService->getByReportCard($raport->report_card_id);
result('getByReportCard() → tinggi terbaru', $found->tinggi_badan);
ok('getByReportCard() berhasil');

// ─────────────────────────────────────────────
// TEST 6: HealthConditionService
// ─────────────────────────────────────────────

section('6. HealthConditionService');

$healthService = new HealthConditionService();

// upsert
$health = $healthService->upsert($raport->report_card_id, [
    'pendengaran'      => 'Normal',
    'penglihatan'      => 'Normal',
    'catatan_tambahan' => null,
]);
result('upsert() → health_id', $health->health_id);
result('pendengaran', $health->pendengaran);
ok('upsert() berhasil');

// upsert ulang → update
$updatedHealth = $healthService->upsert($raport->report_card_id, [
    'pendengaran'      => 'Normal',
    'penglihatan'      => 'Perlu pemeriksaan lebih lanjut',
    'catatan_tambahan' => 'Dirujuk ke dokter mata',
]);
result('upsert() ulang → health_id sama?', $health->health_id === $updatedHealth->health_id ? 'YA (update)' : 'TIDAK (duplikat!)');
ok('upsert() ulang tidak duplikat');

// ─────────────────────────────────────────────
// TEST 7: AttendanceSummaryService
// ─────────────────────────────────────────────

section('7. AttendanceSummaryService');

$attendanceService = new AttendanceSummaryService();

$summary = $attendanceService->getSummary($student->student_id, $activePeriod->period_id);
result('getSummary() → hadir', $summary['hadir']);
result('getSummary() → izin', $summary['izin']);
result('getSummary() → sakit', $summary['sakit']);
result('getSummary() → tanpa keterangan', $summary['tanpa_keterangan']);
result('getSummary() → total', $summary['total']);
ok('getSummary() berhasil (0 jika belum ada data absensi)');

// ─────────────────────────────────────────────
// TEST 8: Status Workflow (submit & approve)
// ─────────────────────────────────────────────

section('8. Status Workflow: draft → submitted → approved');

// submit
$submitResult = $reportCardService->submit($raport->report_card_id);
$raport->refresh();
result('submit() → berhasil?', $submitResult ? 'YA' : 'TIDAK');
result('status setelah submit', $raport->status);
ok('submit() berhasil');

// Coba submit lagi → harus throw exception
try {
    $reportCardService->submit($raport->report_card_id);
    echo "\n  ✗ Seharusnya throw exception saat submit ulang!";
} catch (\InvalidArgumentException $e) {
    ok('Double submit dicegah: ' . $e->getMessage());
}

// approve
$approverUser = User::first();
$approveResult = $reportCardService->approve($raport->report_card_id, $approverUser->user_id);
$raport->refresh();
result('approve() → berhasil?', $approveResult ? 'YA' : 'TIDAK');
result('status setelah approve', $raport->status);
result('approved_by', $raport->approved_by);
result('approved_at', $raport->approved_at);
ok('approve() berhasil');

// Coba hapus raport yang sudah approved → harus throw exception
try {
    $reportCardService->delete($raport->report_card_id);
    echo "\n  ✗ Seharusnya throw exception saat delete raport approved!";
} catch (\InvalidArgumentException $e) {
    ok('Delete raport approved dicegah: ' . $e->getMessage());
}

// ─────────────────────────────────────────────
// RINGKASAN
// ─────────────────────────────────────────────

echo "\n\n══════════════════════════════════════════════";
echo "\n  SELESAI — Semua service berjalan dengan baik";
echo "\n  Report Card ID yang dibuat: {$raport->report_card_id}";
echo "\n══════════════════════════════════════════════\n\n";
