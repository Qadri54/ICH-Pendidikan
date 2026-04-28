# Planning Service Layer — Sistem Raport

## Konteks

Migrasi (8 tabel) dan model (8 model baru + 3 update) untuk sistem penilaian sudah selesai.
Service layer dibangun sebagai jembatan antara Controller dan Model,
mengikuti pola yang sudah ada di `app/Services/`.

## Lokasi

Semua service ditempatkan di: `app/Services/ReportCard/`
Namespace: `App\Services\ReportCard`

---

## Daftar Service (8 Service)

| # | Service | File | Dependensi |
|---|---------|------|------------|
| 1 | AcademicPeriodService | AcademicPeriodService.php | — |
| 2 | ReportCardService | ReportCardService.php | — |
| 3 | NarrativeAssessmentService | NarrativeAssessmentService.php | — |
| 4 | ChecklistAssessmentService | ChecklistAssessmentService.php | — |
| 5 | PhysicalMeasurementService | PhysicalMeasurementService.php | — |
| 6 | HealthConditionService | HealthConditionService.php | — |
| 7 | AttendanceSummaryService | AttendanceSummaryService.php | — |
| 8 | ReportCardPdfService | ReportCardPdfService.php | barryvdh/laravel-dompdf |

---

## Detail Setiap Service

### 1. AcademicPeriodService

Model: `AcademicPeriod` (PK: `period_id`)

```php
public function getAll(): Collection
// Ambil semua periode, urut tahun_ajaran desc

public function getActive(): ?AcademicPeriod
// Ambil periode dengan is_active = true (hanya 1)

public function getById(int $id): AcademicPeriod
// findOrFail() by period_id

public function create(array $data): AcademicPeriod
// $data: tahun_ajaran, semester, tanggal_mulai, tanggal_selesai

public function update(int $id, array $data): bool
// Update periode by id

public function setActive(int $id): bool
// DB::transaction(): nonaktifkan semua → aktifkan yg dipilih

public function delete(int $id): bool
// Hapus periode (gagal jika sudah ada raport terkait)
```

---

### 2. ReportCardService

Model: `StudentReportCard` (PK: `report_card_id`)
Status workflow: `draft` → `submitted` → `approved`

```php
public function getAll(array $filters = []): Collection
// Filter opsional: period_id, class_id, status, homeroom_teacher_id
// Eager load: student, academicPeriod, classRoom

public function getByStudent(int $studentId, ?int $periodId = null): Collection
// Ambil semua raport milik student, opsional filter per periode

public function getById(int $id): StudentReportCard
// findOrFail() dengan eager load semua relasi

public function create(array $data): StudentReportCard
// $data: student_id, period_id, class_id, homeroom_teacher_id
// Status default: draft
// Throw jika raport periode ini sudah ada (unique student_id+period_id)

public function submit(int $id): bool
// Ubah status draft → submitted
// Throw InvalidArgumentException jika status bukan draft

public function approve(int $id, int $approvedBy): bool
// Ubah status submitted → approved, isi approved_by dan approved_at
// Throw InvalidArgumentException jika status bukan submitted

public function delete(int $id): bool
// Throw InvalidArgumentException jika status bukan draft
```

---

### 3. NarrativeAssessmentService

Model: `NarrativeAssessment` (PK: `narrative_id`), `NarrativePhoto` (PK: `photo_id`)

```php
public function getByReportCard(int $reportCardId): Collection
// Ambil semua narasi + foto, urut by kategori

public function upsert(int $reportCardId, array $narratives): void
// $narratives = [
//   ['kategori' => 'intrakurikuler', 'judul' => '...', 'isi_naratif' => '...'],
//   ['kategori' => 'kokurikuler',    'judul' => '...', 'isi_naratif' => '...'],
// ]
// Gunakan updateOrCreate(['report_card_id', 'judul'], [...fields])

public function addPhoto(
    int $narrativeId,
    UploadedFile $file,
    ?string $caption,
    int $urutan
): NarrativePhoto
// Simpan foto ke storage/app/public/raport/photos/
// Buat record NarrativePhoto

public function deletePhoto(int $photoId): bool
// Hapus file dari storage + hapus record
```

---

### 4. ChecklistAssessmentService

Model: `DevelopmentCategory` (PK: `category_id`), `StudentChecklistAssessment` (PK: `checklist_id`)

```php
public function getAllCategories(): Collection
// Ambil semua kategori root (parent_id = null) dengan eager load children
// urut by urutan

public function getByReportCard(int $reportCardId): Collection
// Ambil semua checklist assessment untuk 1 raport
// Key by category_id untuk mudah di-lookup di view

public function upsert(int $reportCardId, array $checklists): void
// $checklists = [
//   ['category_id' => 1, 'status' => 'MM', 'catatan' => '...'],
//   ['category_id' => 2, 'status' => 'SM', 'catatan' => null],
// ]
// Gunakan updateOrCreate(['report_card_id', 'category_id'], ['status', 'catatan'])
// Hanya proses category yang isLeaf() = true
```

---

### 5. PhysicalMeasurementService

Model: `PhysicalMeasurement` (PK: `measurement_id`)
Relasi 1:1 dengan `StudentReportCard`

```php
public function getByReportCard(int $reportCardId): ?PhysicalMeasurement
// firstWhere('report_card_id', $reportCardId)

public function upsert(int $reportCardId, array $data): PhysicalMeasurement
// $data: tinggi_badan, berat_badan, lingkar_kepala, tanggal_ukur
// updateOrCreate(['report_card_id' => $reportCardId], $data)
```

---

### 6. HealthConditionService

Model: `HealthCondition` (PK: `health_id`)
Relasi 1:1 dengan `StudentReportCard`

```php
public function getByReportCard(int $reportCardId): ?HealthCondition
// firstWhere('report_card_id', $reportCardId)

public function upsert(int $reportCardId, array $data): HealthCondition
// $data: pendengaran, penglihatan, catatan_tambahan
// updateOrCreate(['report_card_id' => $reportCardId], $data)
```

---

### 7. AttendanceSummaryService

Model: `StudentAttendance` (PK: `student_attendance_id`, field: `status`, `created_at`)
Model: `AcademicPeriod` untuk ambil `tanggal_mulai` & `tanggal_selesai`

```php
public function getSummary(int $studentId, int $periodId): array
// Return: [
//   'hadir'            => int,
//   'izin'             => int,
//   'sakit'            => int,
//   'tanpa_keterangan' => int,
//   'total'            => int,
// ]
// Query:
// StudentAttendance::where('student_id', $studentId)
//   ->whereBetween('created_at', [$period->tanggal_mulai, $period->tanggal_selesai])
//   ->selectRaw('status, count(*) as total')
//   ->groupBy('status')
//   ->pluck('total', 'status')
```

---

### 8. ReportCardPdfService

Dependency: `barryvdh/laravel-dompdf` (install via composer)
View: `resources/views/raport/pdf.blade.php` (dibuat di fase views)

```php
public function generate(int $reportCardId): string
// Load raport dengan eager load semua relasi
// Render PDF dari view raport.pdf
// Simpan ke storage/app/public/raport/pdf/{report_card_id}.pdf
// Return path file

public function download(int $reportCardId): \Symfony\Component\HttpFoundation\Response
// Generate PDF dan return sebagai download response
// Nama file: raport_{student_NIS}_{tahun_ajaran}_sem{semester}.pdf
```

---

## Urutan Implementasi

1. `AcademicPeriodService` — paling sederhana, tidak ada dependensi
2. `PhysicalMeasurementService` — sederhana, relasi 1:1
3. `HealthConditionService` — sederhana, relasi 1:1
4. `ChecklistAssessmentService` — perlu DevelopmentCategory hierarchy
5. `NarrativeAssessmentService` — perlu file upload handling
6. `AttendanceSummaryService` — perlu query lintas tabel dengan date range
7. `ReportCardService` — integrasikan semua, ada status workflow
8. `ReportCardPdfService` — paling kompleks, install dompdf dulu

## Dependency Tambahan

```bash
composer require barryvdh/laravel-dompdf
```

## Status Implementasi

| Service | Status |
|---------|--------|
| AcademicPeriodService | ✅ Selesai |
| PhysicalMeasurementService | ✅ Selesai |
| HealthConditionService | ✅ Selesai |
| ChecklistAssessmentService | ✅ Selesai |
| NarrativeAssessmentService | ✅ Selesai |
| AttendanceSummaryService | ✅ Selesai |
| ReportCardService | ✅ Selesai |
| ReportCardPdfService | ✅ Selesai (butuh: composer require barryvdh/laravel-dompdf) |
