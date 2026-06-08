# CLAUDE.md — ICH-Pendidikan

Sistem Informasi Manajemen Terintegrasi (IMS) untuk TK IQRA' Creative House, Medan.

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 12, Livewire 4.2
- **Frontend:** Blade + Tailwind CSS 3 + Alpine.js 3, build via Vite 7
- **Database:** MySQL
- **Auth:** Laravel Breeze (email/password)
- **PDF:** barryvdh/laravel-dompdf (raport PDF)
- **Testing:** PHPUnit 11

## Arsitektur

Controller-Service-Model. Controller hanya menerima request dan memanggil service — **seluruh business logic ada di service layer** (`app/Services/`).

```
app/
├── Http/Controllers/
│   ├── Admin/          # CRUD + approval (siswa, guru, user, kelas, keuangan, tabungan, absensi, raport, pendaftaran, pengaturan, laporan)
│   ├── Guru/           # Absensi siswa, absensi guru sendiri, tabungan ledger, raport siswa (wali kelas)
│   └── OrangTua/       # Portal mobile (beranda, pendaftaran, keuangan, tabungan, kehadiran, akademik, profil anak)
├── Services/
│   ├── Attendance/     # AttendanceService, StudentAttendanceService, GeofenceService
│   ├── Registration/   # RegistrationTransactionService, RegistrationFeeService
│   ├── ReportCard/     # ReportCardService, ReportCardPdfService, AcademicPeriodService, ChecklistAssessmentService, NarrativeAssessmentService, PhysicalMeasurementService, HealthConditionService, AttendanceSummaryService
│   ├── Saving/         # SavingTransactionService, SavingLedgerService, PassbookService
│   ├── Spp/            # SppPaymentService
│   └── User/           # UserService, AdminProfileService, TeacherProfileService, ReligiousTeacherProfileService, StudentProfileService, FoundationHeadProfileService
├── Models/             # Eloquent models, custom primary keys (user_id, student_id, dll — bukan id)
├── Console/Commands/   # CheckOverdueSppInvoices, GenerateSppInvoices
└── View/Components/    # AppLayout, GuestLayout, MainLayout, MobileLayout
```

## RBAC (Role-Based Access Control)

Implementasi via model `Role` (hasOne dari User) + middleware `RoleMiddleware` yang membaca `role_name`.

| Role | Middleware | Akses |
|------|-----------|-------|
| Admin | `role:Admin` | Full CRUD semua modul |
| Kepala Sekolah | `role:Admin,Kepala Sekolah,Kepala Yayasan` | Read-only admin area |
| Kepala Yayasan | `role:Admin,Kepala Sekolah,Kepala Yayasan` | Read-only admin area |
| Guru | `role:Guru` | Absensi siswa, tabungan, raport (kelas wali) |
| Guru Ngaji | `role:Guru,Guru Ngaji` | Hanya absensi guru sendiri (checkin/checkout) |
| Orang Tua | `role:Orang Tua` | Portal mobile semua fitur ortu |
| Guest | — | Akun terdaftar tanpa akses khusus |
| Student | — | Data siswa (relasi ke orang tua via User) |

User memiliki profile table terpisah per role: `admins`, `teachers`, `religious_teachers`, `foundation_heads`, `students`. Pembuatan/update/hapus profile di-handle oleh `UserService` → `*ProfileService`.

## Modul & Fitur

### PPDB (Pendaftaran Peserta Didik Baru)
- Orang tua mengisi formulir pendaftaran (data anak + orang tua)
- Admin approve/reject dengan alasan, kelola pembayaran biaya pendaftaran (cicilan/lunas)
- Toggle buka/tutup pendaftaran via `RegistrationSetting`

### Keuangan (SPP)
- Generate tagihan SPP bulanan otomatis (`GenerateSppInvoices` command)
- Cek overdue otomatis (`CheckOverdueSppInvoices` command)
- Orang tua upload bukti bayar, Admin approve/reject
- Model: `SppInvoice` → `SppPayment`

### Tabungan Siswa
- Guru membuat ledger → buka passbook per siswa → catat setoran/penarikan
- Admin bisa CRUD ledger juga
- Model: `SavingLedger` → `StudentPassbook` → `SavingTransaction`

### Absensi Siswa
- Guru input kehadiran per kelas per tanggal (bulk)
- Status: Hadir/Sakit/Izin/Alpha
- Orang tua lihat ringkasan bulanan + riwayat
- Model: `StudentAttendance` (teacher_id, student_id, status, created_at)

### Absensi Guru
- GPS + Geofencing: guru check-in/check-out dengan koordinat GPS
- Validasi lokasi via Haversine formula di `GeofenceService`
- Selfie saat check-in (`selfie_path`)
- Model: `AttendanceRecord` (lat/lng, is_within_geofence, selfie_path)
- Konfigurasi geofence via `AttendanceSetting` (key-value)

### Raport Digital (Sistem Penilaian PAUD)
- Penilaian naratif: cerita perkembangan anak + foto kegiatan (`NarrativeAssessment`, `NarrativePhoto`)
- Penilaian checklist: capaian perkembangan per kategori (`DevelopmentCategory` tree → `StudentChecklistAssessment`)
- Pengukuran fisik: berat/tinggi badan (`PhysicalMeasurement`)
- Kondisi kesehatan: pendengaran, penglihatan, dll (`HealthCondition`)
- Alur: Guru create → isi → submit → Admin approve → PDF (DomPDF)
- Model: `StudentReportCard` (status: draft/submitted/approved) → assessments
- `DevelopmentCategory` bersifat hierarkis (parent_id, self-referencing)

### Manajemen Kelas
- CRUD kelas dengan wali kelas (homeroom_teacher_id)
- Model: `ClassRoom` (table: `classes`)

### Notifikasi
- Laravel notification system (database driver)
- View berbeda: desktop (`notifications.index`) vs mobile (`notifications.mobile`)

### Laporan & Pengaturan
- Laporan: total pendapatan, statistik siswa/guru
- Pengaturan: toggle pendaftaran, kelola semester (`AcademicPeriod`)

## Database Conventions

- **Custom primary keys**: semua model pakai nama PK deskriptif (`user_id`, `student_id`, `class_id`, `report_card_id`, dll) — bukan default `id`
- **Foreign key references**: selalu eksplisit di relasi (`'user_id', 'user_id'`)
- **Table naming**: kebanyakan default plural, kecuali `ClassRoom` → table `classes`, `StudentAttendance` → table `student_attendance`
- **Timestamps**: sebagian besar model pakai default timestamps, `StudentAttendance` disable timestamps (`$timestamps = false`)

## Routes

- `/` — Welcome page
- `/dashboard` — Role-aware (Guru → `guru.dashboard`, lainnya → `dashboard`)
- `/admin/*` — Semua fitur admin (read: Admin+Kepsek+KepYay, write: Admin only)
- `/guru/*` — Fitur guru (absensi, tabungan, raport)
- `/beranda`, `/pendaftaran`, `/pembayaran`, `/tabungan`, `/kehadiran`, `/akademik`, `/profil-anak`, `/pengaturan` — Fitur orang tua (mobile layout)
- Auth routes via Laravel Breeze (login, register, forgot-password, dll)

## Views & Layout

3 layout utama:
- `layouts.app` — Admin desktop (sidebar navigation)
- `layouts.main` — Desktop umum
- `layouts.mobile` — Orang tua (topbar + tab-bar bawah)

Komponen Blade custom: `card`, `pill`, `stat-card`, `ich-button`, `ich-icon`, `section-header`, `sidebar-nav`, `mobile/drawer`, `mobile/topbar`, `mobile/tab-bar`

## Seeders

Urutan: `ClassRoomSeeder` → `UserSeeder` → `AcademicPeriodSeeder` → `DevelopmentCategorySeeder`

Default users (password: `password123`):
- admin@iqra.com (Admin)
- guru@iqra.com (Guru)
- guruNgaji@iqra.com (Guru Ngaji)
- kepsek@iqra.com (Kepala Sekolah)
- yayasan@iqra.com (Kepala Yayasan)
- guest@iqra.com (Guest)
- siswa@iqra.com (Student)

## Development

```bash
# Start dev server (artisan + queue + vite)
composer dev

# Run tests
composer test

# Fresh migrate + seed
php artisan migrate:fresh --seed
```

## Git Conventions

- Jangan commit file laporan TA (folder `docs/` dan `referensi/` sudah di `.gitignore`)
- Jangan tambahkan baris `Co-Authored-By` di pesan commit
- Commit setiap perubahan — jangan menumpuk banyak perubahan dalam satu commit besar
- Pesan commit dalam bahasa Indonesia, singkat dan deskriptif
