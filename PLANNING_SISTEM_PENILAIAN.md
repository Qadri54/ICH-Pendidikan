# PLANNING: Sistem Penilaian / Raport TK

## 1. Context & Tujuan

Sistem ini adalah **Laporan Perkembangan Anak (Raport)** untuk jenjang TK di IQRA' Creative House. Raport TK bersifat **naratif + ceklis perkembangan** mengikuti Kurikulum Merdeka PAUD — bukan skala angka/huruf seperti jenjang SD ke atas.

**Masalah yang dipecahkan:** Saat ini belum ada modul raport di aplikasi. Guru membuat raport manual di luar sistem. Planning ini mendefinisikan database, service, controller, dan view untuk raport digital yang dapat di-generate per siswa per semester.

**Outcome yang diharapkan:**
- Guru dapat input penilaian naratif, ceklis perkembangan, pengukuran fisik, dan kondisi kesehatan.
- Admin/Kepala Sekolah dapat review dan approve raport.
- Orangtua dapat melihat/unduh raport anaknya dalam bentuk PDF.

**Catatan tentang tabel existing yang TIDAK dipakai:**
Tabel `students_grades`, `subjects`, dan `subject_teachers` **tidak relevan** untuk konteks TK (Kurikulum Merdeka PAUD tidak mengenal mata pelajaran terpisah & nilai angka). Modul raport ini berdiri sendiri dan tidak ber-relasi ke tabel-tabel tersebut.

---

## 2. Analisis Komponen Raport (Berdasarkan Gambar)

Raport terdiri dari **5 bagian utama**:

### Bagian A — INTRAKURIKULER (Naratif)
- **Nilai Agama dan Budi Pekerti** → teks naratif + foto kegiatan

### Bagian B — KOKURIKULER (Naratif)
Berisi **dua sub-penilaian** naratif + foto kegiatan:
1. **Jati Diri**
2. **Dasar-dasar Literasi, Matematika, Sains, Teknologi, Rekayasa, dan Seni**

### Bagian C — CEKLIS PERKEMBANGAN ANAK
Hirarki 2 level (kategori → sub-item), status **BM / MM / SM** per semester:
- `BM` = Belum Muncul
- `MM` = Mulai Muncul
- `SM` = Sudah Muncul

Kategori dari gambar (usia 5–6 tahun):
1. Mengenal Allah melalui Penciptanya
2. Rukun Iman (6 sub-item)
3. Rukun Islam (5 sub-item)
4. Menirukan dan Melafazkan Doa Harian (14 sub-item)
5. Menirukan dan Melafazkan Kalimat Toyibah (8 sub-item)
6. Menirukan dan Melafazkan Surah Pendek (6 sub-item)
7. Mengenal Aturan Wudhu (5 sub-item)
8. Mengenal Gerakan Shalat
9. Mengenal Gerakan dan Bacaan Shalat

### Bagian D — KETIDAKHADIRAN & PENGUKURAN FISIK
- **Pengukuran Fisik:** Tinggi Badan (cm), Berat Badan (kg), Lingkar Kepala (cm) per semester
- **Ketidakhadiran:** Sakit, Izin, Tanpa Keterangan (di-derive dari tabel `student_attendance` yang sudah ada)

### Bagian E — KONDISI KESEHATAN
- Pendengaran, Penglihatan (keterangan teks)

---

## 3. Desain Database

Mengikuti konvensi existing: primary key semantik (`*_id`), mix Bahasa Indonesia untuk field domain.

### 3.1. Table Baru

#### `academic_periods` (master semester/tahun ajaran)
```
- period_id (PK)
- tahun_ajaran   (string, mis. "2025/2026")
- semester       (enum: 1, 2)
- tanggal_mulai  (date)
- tanggal_selesai(date)
- is_active      (boolean)
```

#### `student_report_cards` (raport utama per siswa per semester)
```
- report_card_id     (PK)
- student_id         (FK → students)
- period_id          (FK → academic_periods)
- class_id           (FK → classes)
- homeroom_teacher_id(FK → teachers)  // wali kelas
- status             (enum: draft, submitted, approved)
- approved_by        (FK → users, nullable)
- approved_at        (timestamp, nullable)
- timestamps
- UNIQUE(student_id, period_id)
```

#### `narrative_assessments` (Bagian A/B)
Satu raport punya **3 baris naratif**: 1 intrakurikuler + 2 kokurikuler.
```
- narrative_id       (PK)
- report_card_id     (FK → student_report_cards)
- kategori           (enum: intrakurikuler, kokurikuler)
- judul              (string)
    // Nilai:
    //   "Nilai Agama dan Budi Pekerti"            → kategori=intrakurikuler
    //   "Jati Diri"                               → kategori=kokurikuler
    //   "Dasar-dasar Literasi, Matematika, ..."   → kategori=kokurikuler
- isi_naratif        (text)
- timestamps
- UNIQUE(report_card_id, judul)
```

#### `narrative_photos` (foto kegiatan — 1 naratif bisa banyak foto)
```
- photo_id       (PK)
- narrative_id   (FK → narrative_assessments)
- photo_path     (string) // storage path
- caption        (string, nullable)
- urutan         (int)
```

#### `development_categories` (master kategori ceklis — hirarkis)
```
- category_id    (PK)
- parent_id      (FK self, nullable)
- nama           (string)
- urutan         (int)
- usia_min       (int, default 5)
- usia_max       (int, default 6)
- is_active      (boolean)
```
Seeder akan isi 9 kategori + sub-item dari gambar.

#### `student_checklist_assessments` (hasil ceklis per siswa)
```
- checklist_id   (PK)
- report_card_id (FK → student_report_cards)
- category_id    (FK → development_categories)  // hanya sub-item (leaf)
- status         (enum: BM, MM, SM)
- catatan        (text, nullable)
- UNIQUE(report_card_id, category_id)
```

#### `physical_measurements` (pengukuran fisik)
```
- measurement_id (PK)
- report_card_id (FK → student_report_cards)
- tinggi_badan   (decimal 5,2) // cm
- berat_badan    (decimal 5,2) // kg
- lingkar_kepala (decimal 5,2) // cm
- tanggal_ukur   (date)
```

#### `health_conditions` (kondisi kesehatan)
```
- health_id       (PK)
- report_card_id  (FK → student_report_cards)
- pendengaran     (string) // "Baik", "Kurang", dll.
- penglihatan     (string)
- catatan_tambahan(text, nullable)
```

### 3.2. Tabel Existing yang Dipakai (Read-only FK)

| Tabel | Dipakai untuk | Relasi |
|---|---|---|
| `students` | Identitas siswa yang diraport | FK `student_id` |
| `classes` | Kelas/rombel siswa | FK `class_id` |
| `teachers` | Wali kelas pembuat raport | FK `homeroom_teacher_id` |
| `users` | User yang approve raport | FK `approved_by` |
| `roles` | RBAC (dicek via middleware) | — |
| `student_attendance` | Agregasi Bagian D (Ketidakhadiran) | query by tanggal |

**`student_attendance` — sudah siap dipakai:**
Kolom `status` di [2026_04_18_001507_create_student_attendance_table.php](database/migrations/2026_04_18_001507_create_student_attendance_table.php) sudah berisi `ENUM(izin, sakit, tanpa keterangan)` — tepat sesuai yang dibutuhkan. Tabel ini hanya mencatat ketidakhadiran (tidak ada entri untuk `hadir`). `AttendanceSummaryService` cukup `COUNT` per status dalam rentang tanggal periode. **Tidak perlu migration tambahan.**

### 3.3. Tabel Existing yang TIDAK Dipakai

Tabel berikut **sengaja tidak disentuh** karena bukan bagian dari konteks raport TK:
- `students_grades` — sistem nilai angka per mata pelajaran (tidak relevan untuk TK)
- `subjects` & `subject_teachers` — mata pelajaran & penugasan guru mapel (TK tidak pakai)
- `spp_*`, `saving_*`, `registration_*` — domain keuangan/pendaftaran, terpisah dari raport

---

## 4. Arsitektur Kode (Services + Controllers)

Mengikuti pola existing (`app/Services/Registration/*`):

### 4.1. Folder Services Baru: `app/Services/ReportCard/`
- `ReportCardService.php` — CRUD raport, transisi status (draft → submitted → approved)
- `NarrativeAssessmentService.php` — kelola 3 naratif (1 intrakurikuler + 2 kokurikuler) + upload foto
- `ChecklistAssessmentService.php` — bulk upsert ceklis perkembangan
- `PhysicalMeasurementService.php` — input pengukuran fisik
- `HealthConditionService.php` — input kondisi kesehatan
- `AttendanceSummaryService.php` — agregasi Sakit/Izin/Alpha dari `student_attendance`
- `ReportCardPdfService.php` — generate PDF raport (gunakan `barryvdh/laravel-dompdf` atau `spatie/laravel-pdf`)

### 4.2. Controllers
- `app/Http/Controllers/ReportCard/ReportCardController.php` — list, show, create, form multi-step
- `app/Http/Controllers/ReportCard/NarrativeController.php` — form naratif + upload foto
- `app/Http/Controllers/ReportCard/ChecklistController.php` — form ceklis
- `app/Http/Controllers/ReportCard/ReportCardPdfController.php` — download PDF

### 4.3. Form Request Validation
- `StoreNarrativeRequest`, `StoreChecklistRequest`, `StorePhysicalRequest`, `StoreHealthRequest`

### 4.4. Models Baru (di `app/Models/`)
Mengikuti konvensi existing (primary key semantik, relationships explicit):
- `AcademicPeriod.php`
- `StudentReportCard.php`
- `NarrativeAssessment.php`
- `NarrativePhoto.php`
- `DevelopmentCategory.php`
- `StudentChecklistAssessment.php`
- `PhysicalMeasurement.php`
- `HealthCondition.php`

---

## 5. Routing (`routes/web.php`)

```php
Route::middleware(['auth', 'role:admin,guru,guru_ngaji,kepala_sekolah'])
    ->prefix('raport')->name('raport.')->group(function () {
        Route::get('/', [ReportCardController::class, 'index'])->name('index');
        Route::get('/create/{student}', [ReportCardController::class, 'create'])->name('create');
        Route::post('/', [ReportCardController::class, 'store'])->name('store');
        Route::get('/{reportCard}/edit', [ReportCardController::class, 'edit'])->name('edit');
        Route::get('/{reportCard}', [ReportCardController::class, 'show'])->name('show');

        // Sub-form per bagian
        Route::post('/{reportCard}/narrative', [NarrativeController::class, 'store'])->name('narrative.store');
        Route::post('/{reportCard}/checklist', [ChecklistController::class, 'storeBulk'])->name('checklist.store');
        Route::post('/{reportCard}/physical', [PhysicalController::class, 'store'])->name('physical.store');
        Route::post('/{reportCard}/health', [HealthController::class, 'store'])->name('health.store');

        // Workflow
        Route::post('/{reportCard}/submit', [ReportCardController::class, 'submit'])->name('submit');
        Route::post('/{reportCard}/approve', [ReportCardController::class, 'approve'])
            ->middleware('role:admin,kepala_sekolah')->name('approve');

        // PDF
        Route::get('/{reportCard}/pdf', [ReportCardPdfController::class, 'download'])->name('pdf');
    });
```

---

## 6. UI / Views (Blade + Alpine + Tailwind)

Folder: `resources/views/raport/`

- `index.blade.php` — list raport (filter by kelas, semester); guru lihat siswa di kelasnya, admin lihat semua
- `create.blade.php` — pilih siswa + periode untuk raport baru
- `edit.blade.php` — form multi-tab (Alpine):
  - Tab 1: Naratif (3 kategori) + upload foto
  - Tab 2: Ceklis Perkembangan (radio BM/MM/SM per item)
  - Tab 3: Pengukuran Fisik
  - Tab 4: Kondisi Kesehatan
  - Tab 5: Preview & Submit
- `show.blade.php` — tampilan raport final
- `pdf.blade.php` — template khusus PDF (layout mirip gambar asli)

---

## 7. Seeder yang Diperlukan

- `DevelopmentCategorySeeder.php` — isi 9 kategori + ~50 sub-item usia 5–6 tahun dari gambar
- `AcademicPeriodSeeder.php` — isi periode aktif default (Semester 1 & 2 2025/2026)

---

## 8. Roles & Permissions

| Role | Aksi |
|---|---|
| Guru (wali kelas) | Create/edit raport siswa di kelasnya, submit |
| Guru Ngaji | Input ceklis keagamaan |
| Admin | Lihat semua, approve |
| Kepala Sekolah | Lihat semua, approve |
| Siswa/Orangtua | Lihat & unduh raport miliknya (status = approved saja) |

Middleware `role:*` sudah ada di [RoleMiddleware.php](app/Http/Middleware/RoleMiddleware.php).

---

## 9. File Kritis yang Akan Dibuat/Dimodifikasi

**Akan dibuat:**
- 8 migration baru di `database/migrations/`
- 8 model baru di `app/Models/`
- 7 service di `app/Services/ReportCard/`
- 5 controller di `app/Http/Controllers/ReportCard/`
- 4 form request di `app/Http/Requests/ReportCard/`
- 2 seeder di `database/seeders/`
- 8–10 blade view di `resources/views/raport/`

**Akan dimodifikasi:**
- [routes/web.php](routes/web.php) — tambah group route raport
- [app/Models/Student.php](app/Models/Student.php) — tambah relationship `reportCards()` (hapus juga relationship `studentsGrades()` bila `students_grades` sudah tidak dipakai)
- [app/Models/Teacher.php](app/Models/Teacher.php) — tambah relationship `homeroomReportCards()`
- [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php) — panggil seeder baru
- [2026_04_18_001507_create_student_attendance_table.php](database/migrations/2026_04_18_001507_create_student_attendance_table.php) — ✅ tidak perlu diubah; kolom `status` sudah `ENUM(izin, sakit, tanpa keterangan)`

**Dependency tambahan (composer):**
- `barryvdh/laravel-dompdf` untuk generate PDF raport

---

## 10. Urutan Implementasi (Recommended)

1. **Migration + Model + Seeder** (foundation data)
   - Buat 8 migration, jalankan `php artisan migrate`
   - Buat seeder `DevelopmentCategorySeeder` (penting — data master)
2. **Service Layer** (business logic)
3. **Controllers + Form Requests**
4. **Routes**
5. **Views** (mulai dari `index` → `edit` → `show` → `pdf`)
6. **Install dompdf + template PDF**
7. **Testing manual + unit test service**

---

## 11. Verification / Cara Test

**Manual end-to-end:**
1. Login sebagai guru wali kelas
2. Buka `/raport` → klik "Buat Raport Baru" → pilih siswa & semester
3. Isi ke-4 tab (naratif, ceklis, fisik, kesehatan), upload ≥1 foto per naratif
4. Klik "Submit" → status berubah jadi `submitted`
5. Login sebagai admin/kepsek → approve
6. Klik "Download PDF" → verifikasi layout PDF mirip gambar referensi
7. Login sebagai siswa → pastikan hanya bisa lihat raport miliknya dengan status `approved`

**Automated tests:**
- `tests/Feature/ReportCardFlowTest.php` — full workflow create → submit → approve
- `tests/Unit/ChecklistAssessmentServiceTest.php` — bulk upsert logic
- `tests/Unit/AttendanceSummaryServiceTest.php` — agregasi kehadiran

---

## 12. Pertanyaan Terbuka (Perlu Konfirmasi)

Sebelum mulai implementasi, mohon konfirmasi:

1. **Foto kegiatan:** berapa foto maksimal per naratif? (default saya pakai unlimited, disimpan di `storage/app/public/raport-photos/`)
2. **Template PDF:** apakah harus persis seperti gambar referensi (hijau, ornamen), atau cukup layout rapi standar?
3. **Alur approval:** apakah perlu 2 level (guru → admin → kepsek), atau cukup 1 level (guru → admin/kepsek)?
4. **Ceklis items:** apakah daftar 9 kategori di gambar sudah final, atau ada rencana menambah kategori untuk kelompok usia lain (3–4 tahun, 4–5 tahun)?
5. **Komentar wali kelas & orangtua:** di gambar hanya terlihat naratif dari guru. Apakah perlu field khusus "Komentar Wali Kelas" & "Komentar Orangtua"?
6. **Library PDF:** OK pakai `barryvdh/laravel-dompdf` atau prefer `spatie/laravel-pdf` (butuh Node + Playwright)?
