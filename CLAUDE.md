# CLAUDE.md — ICH-Pendidikan

Sistem Informasi Manajemen Terintegrasi (IMS) untuk TK IQRA' Creative House, Medan.

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 12, Livewire 4.2
- **Frontend:** Blade + Tailwind CSS 3 + Alpine.js 3, build via Vite 7
- **Database:** MySQL
- **Auth:** Laravel Breeze (email/password)
- **PDF:** barryvdh/laravel-dompdf (raport PDF) — lihat batasan DomPDF di bawah
- **Testing:** PHPUnit 11, Laravel Dusk 8 (browser testing)

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
│   ├── Spp/            # SppInvoiceService, SppPaymentService
│   └── User/           # UserService, AdminProfileService, TeacherProfileService, ReligiousTeacherProfileService, StudentProfileService, FoundationHeadProfileService
├── Models/             # Eloquent models, custom primary keys (user_id, student_id, dll — bukan id)
├── Console/Commands/   # CheckOverdueSppInvoices, GenerateSppInvoices
└── View/Components/    # AppLayout, GuestLayout, MainLayout, MobileLayout
```

## RBAC (Role-Based Access Control)

Implementasi via model `Role` (hasOne dari User) + middleware `RoleMiddleware` yang membaca `role_name`.

| Role           | Middleware                                 | Akses                                         |
| -------------- | ------------------------------------------ | --------------------------------------------- |
| Admin          | `role:Admin`                               | Full CRUD semua modul                         |
| Kepala Sekolah | `role:Admin,Kepala Sekolah,Kepala Yayasan` | Read-only admin area                          |
| Kepala Yayasan | `role:Admin,Kepala Sekolah,Kepala Yayasan` | Read-only admin area                          |
| Guru           | `role:Guru`                                | Absensi siswa, tabungan, raport (kelas wali)  |
| Guru Ngaji     | `role:Guru,Guru Ngaji`                     | Hanya absensi guru sendiri (checkin/checkout) |
| Orang Tua      | `role:Orang Tua`                           | Portal mobile semua fitur ortu                |
| Guest          | —                                          | Akun terdaftar tanpa akses khusus             |
| Student        | —                                          | Data siswa (relasi ke orang tua via User)     |

User memiliki profile table terpisah per role: `admins`, `teachers`, `religious_teachers`, `foundation_heads`, `students`. Pembuatan/update/hapus profile di-handle oleh `UserService` → `*ProfileService`.

**Relasi Student ↔ Orang Tua:** `Student.user_id` → User (role: Orang Tua). Satu orang tua bisa punya banyak anak. Akun orang tua dibuat berdasarkan nama ayah (jika tidak ada, ibu/wali). Portal orang tua (`ProfilAnakController`) mengambil anak via `Student::where('user_id', auth()->id())`.

## Modul & Fitur

### PPDB (Pendaftaran Peserta Didik Baru)

- Orang tua mengisi formulir pendaftaran (data anak + orang tua)
- Admin approve/reject dengan alasan, kelola pembayaran biaya pendaftaran (cicilan/lunas)
- Toggle buka/tutup pendaftaran via `RegistrationSetting`

### Keuangan (SPP)

- Generate tagihan SPP bulanan otomatis (`GenerateSppInvoices` command) — **hanya siswa aktif**
- Cek overdue otomatis (`CheckOverdueSppInvoices` command)
- Orang tua upload bukti bayar, Admin approve/reject
- Nominal SPP bulanan: Rp 300.000 (`SppInvoiceService::MONTHLY_FEE`)
- Generate invoice via bulk `DB::table()->insert()` (bukan loop Eloquent)
- Dropdown siswa di admin keuangan (index + create) hanya tampilkan `Student::aktif()`
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

- Laravel notification system (database + WhatsApp channel)
- View berbeda: desktop (`notifications.index`) vs mobile (`notifications.mobile`)
- WhatsApp: `WhatsAppService` + `WhatsAppChannel`, konfigurasi via `whatsapp_settings` table, toggle on/off di admin pengaturan

### Laporan & Pengaturan

- Laporan: total pendapatan, statistik siswa/guru
- Pengaturan: toggle pendaftaran, kelola semester (`AcademicPeriod`)

## Database Conventions

- **Custom primary keys**: semua model pakai nama PK deskriptif (`user_id`, `student_id`, `class_id`, `report_card_id`, dll) — bukan default `id`
- **Foreign key references**: selalu eksplisit di relasi (`'user_id', 'user_id'`)
- **Table naming**: kebanyakan default plural, kecuali `ClassRoom` → table `classes`, `StudentAttendance` → table `student_attendance`
- **Timestamps**: sebagian besar model pakai default timestamps, `StudentAttendance` disable timestamps (`$timestamps = false`)
- **Student status lifecycle**: kolom `status` enum (`aktif`, `alumni`, `keluar`) default `aktif`. Scope `Student::aktif()` untuk filter siswa terdaftar. Dashboard dan SPP hanya menghitung/menagih siswa aktif

## Routes

- `/` — Welcome page
- `/dashboard` — Role-aware (Guru → `guru.dashboard`, lainnya → `dashboard`)
- `/admin/*` — Semua fitur admin (read: Admin+Kepsek+KepYay, write: Admin only)
- `/guru/*` — Fitur guru (absensi, tabungan, raport)
- `/beranda`, `/pendaftaran`, `/pembayaran`, `/tabungan`, `/kehadiran`, `/akademik`, `/profil-anak`, `/pengaturan` — Fitur orang tua (mobile layout)
- Auth routes via Laravel Breeze (login, register, forgot-password, reset-password)
- Error pages otomatis via `resources/views/errors/{code}.blade.php`

## Views & Layout

3 layout utama:

- `layouts.app` — Admin desktop (sidebar navigation)
- `layouts.main` — Desktop umum
- `layouts.mobile` — Orang tua (topbar + tab-bar bawah)

Komponen Blade custom: `card`, `pill` (prop: `tone`, bukan `variant`), `stat-card`, `ich-button`, `ich-icon`, `section-header`, `sidebar-nav`, `mobile/drawer`, `mobile/topbar`, `mobile/tab-bar`

Error pages: `resources/views/errors/` — shared layout (`layout.blade.php`) + per-code views (403, 404, 419, 500, 502, 503). Laravel otomatis resolve ke `errors/{code}.blade.php` saat `abort()` dipanggil.

Mobile layout spinner: `layouts.mobile` punya page-loader yang intercept semua `<a>` click. Link download (atribut `download`, `target="_blank"`, atau URL match `/download|export|cetak/`) di-exclude agar spinner tidak stuck.

## UI Design Conventions

### Warna & Token

- **Font families:** `font-ui` (label/tombol), `font-display` (heading), `font-sans` (body text)
- **Ink palette:** `text-ich-ink-900` (primary), `text-ich-ink-600` (secondary), `text-ich-ink-400` (muted), `text-ich-ink-300` (disabled/empty)
- **Card shadow:** `shadow-ich-card` untuk semua card, `hover:shadow-md` untuk interaktif

### Tabel

- Header: `bg-ich-surface` dengan `text-ich-ink-600 font-ui font-bold` — **bukan** `bg-ich-green text-white`
- Row hover: `hover:bg-ich-surface transition-colors`
- Divider: `divide-y divide-ich-line`
- Empty state: `text-ich-ink-300 font-sans` centered

### Halaman Admin

- Setiap halaman admin punya **ikon header**: SVG 24x24 di dalam `div` rounded-xl dengan warna latar per modul:
    - Guru: `bg-ich-green-surface` hijau | Siswa: `bg-ich-blue-soft` biru muda | Kelas: `bg-ich-purple-soft` ungu
    - User: `bg-ich-blue-soft` biru | Keuangan: `bg-ich-green-surface` hijau | Absensi Siswa: `bg-ich-warning-soft` kuning
    - Absensi Guru: `bg-ich-pink-soft` pink | Pendaftaran: `bg-ich-blue-soft` biru | Pembayaran: `bg-ich-purple-soft` ungu
    - Tabungan: `bg-ich-warning-soft` kuning | Raport: `bg-ich-purple-soft` ungu | Pengaturan: `bg-gray-100` abu

### Greeting Banner (Dashboard)

- Gradient: `bg-gradient-to-br from-ich-green to-ich-gradient-end`
- Avatar initials: `w-12 h-12 rounded-full bg-white/15` dengan huruf pertama nama
- Decorative circles: `bg-white/5 rounded-full` positioned absolutely

### Stat Cards

- Icon badge: `w-12 h-12 rounded-xl` dengan warna latar per metrik (gunakan token: `bg-ich-green-surface`, `bg-ich-info-soft`, `bg-ich-warning-soft`, `bg-ich-purple-soft`)
- Hover interaktif: `hover:-translate-y-0.5 transition-all`

### Status Badges (gunakan komponen `<x-pill>`)

- Pending/Menunggu: `bg-ich-warning-soft text-ich-warning`
- Sukses/Disetujui/Lunas: `bg-ich-success-soft text-ich-success`
- Error/Ditolak: `bg-ich-error-soft text-ich-error`
- Info/Izin: `bg-ich-purple-soft text-ich-purple`

## DomPDF CSS Limitations

Template PDF (`resources/views/raport/pdf.blade.php`) harus menghindari CSS yang tidak didukung DomPDF:

| Tidak Didukung                   | Alternatif                                |
| -------------------------------- | ----------------------------------------- |
| `position: fixed`                | `position: absolute` (per `.page`)        |
| `display: inline-block`          | `width` + `margin: auto`                  |
| `box-sizing: border-box`         | Hapus, hitung padding manual              |
| `min-height`                     | Padding bottom sebagai pengganti          |
| `letter-spacing`                 | Hapus (render tidak konsisten)            |
| Nested `<table>` di dalam `<td>` | Flatten jadi kolom terpisah               |
| `<div>` centering via margin     | Gunakan `<table>` dengan `margin: 0 auto` |

Background image di setiap halaman: gunakan `position: absolute` + `z-index: -1` pada `<img>` pertama di setiap `.page`.

## Seeders

Urutan: `ClassRoomSeeder` → `UserSeeder` → `ParentStudentSeeder` → `AcademicPeriodSeeder` → `DevelopmentCategorySeeder` → `ReportCardSeeder`

`DatabaseSeeder` membungkus semua seeder call dengan `Schema::disableForeignKeyConstraints()` / `enableForeignKeyConstraints()` agar `db:seed` bisa jalan tanpa `migrate:fresh` (menghindari FK constraint violation saat truncate).

`ReportCardSeeder`: seeds data raport lengkap untuk 3 T.A — penilaian naratif (6 aspek per siswa), checklist perkembangan, pengukuran fisik, dan kondisi kesehatan. Data diambil dari dokumen raport asli sekolah.

`ParentStudentSeeder`: seeds 34 akun orang tua + 37 siswa dari data raport 3 T.A (2023/2024, 2024/2025, 2025/2026). Email di-generate dari nama ayah (lowercase, spasi → titik, @iqra.com). Siswa T.A 2025/2026 → `status: aktif` (21 siswa, 3 kelas), siswa T.A lama → `status: alumni` (17 siswa). Orang tua dengan banyak anak: Binsar Sitompul, Robby Pratama, Muhammad Juanda Harahap.

Default users (password: `password123`):

- admin@iqra.com (Admin)
- guru@iqra.com — Sofia Aurora Susanto (Guru, wali kelas Kelas B)
- lisma.pane@iqra.com — Lisma Farida Pane (Guru, wali kelas Kelas A)
- guruNgaji@iqra.com (Guru Ngaji)
- kepsek@iqra.com — Adli Qarin (Kepala Sekolah)
- yayasan@iqra.com (Kepala Yayasan)
- guest@iqra.com (Guest)
- siswa@iqra.com (Student)
- 34 akun orang tua (via `ParentStudentSeeder`)

## Browser Testing (Laravel Dusk)

Laravel Dusk v8 untuk automated browser testing. ChromeDriver headless, window 1920x1080.

### Setup & Konfigurasi

- `DuskServiceProvider` didaftarkan manual di `AppServiceProvider::register()` (Laravel 12 tidak auto-discover)
- Environment file: `.env.dusk.local` (copy dari `.env`, `APP_URL=http://localhost:8000`)
- Dev server harus jalan (`composer dev`) sebelum run Dusk
- Database harus sudah di-seed (`php artisan migrate:fresh --seed`)

### Menjalankan Test

```bash
# Run semua browser test
php artisan dusk

# Run satu test class
php artisan dusk --filter=AuthenticationTest

# Stop saat pertama kali gagal
php artisan dusk --stop-on-failure

# Run satu method
php artisan dusk --filter=AuthenticationTest::test_user_can_login
```

### Struktur Test (`tests/Browser/`)

| File                     | Test | Cakupan                                                          |
| ------------------------ | ---- | ---------------------------------------------------------------- |
| `AuthenticationTest`     | 7    | Login valid/invalid, register, logout, redirect, forgot password |
| `RbacTest`               | 8    | Akses per role (admin, kepsek, guru, ortu, guest)                |
| `DashboardTest`          | 4    | Dashboard admin, guru, orangtua + greeting banner                |
| `AdminSiswaTest`         | 3    | List, detail, create siswa                                       |
| `AdminKeuanganTest`      | 3    | SPP invoices, create, bukti bayar                                |
| `AdminRaportTest`        | 4    | List, detail, narasi, create                                     |
| `AdminPendaftaranTest`   | 2    | Pendaftaran + pembayaran                                         |
| `AdminPengaturanTest`    | 2    | Pengaturan + laporan                                             |
| `AdminTabunganTest`      | 2    | Ledger list + create                                             |
| `AdminAbsensiTest`       | 4    | Absensi siswa & guru + recap                                     |
| `AdminGuruKelasUserTest` | 6    | Guru, kelas, user list & create                                  |
| `GuruModuleTest`         | 5    | Absensi, tabungan, raport guru + guru ngaji                      |
| `OrangTuaPortalTest`     | 10   | Semua fitur portal ortu + tab bar + multi-child                  |
| `ErrorPageTest`          | 2    | 404, 403                                                         |
| `ProfileTest`            | 2    | Profil admin + pengaturan ortu                                   |

### Gotchas & Konvensi Dusk

- **Custom PK:** Gunakan `loginAs($user->user_id, 'web')` (scalar), bukan `loginAs($user, 'web')` (model object) — karena custom PK bisa return null dari `getKey()` jika user tidak ada di DB
- **Session persist:** Browser session bertahan antar test method — panggil `$browser->logout()` eksplisit di awal test login
- **Null guard:** Semua test non-admin user harus cek `if (!$user) { $this->markTestSkipped(...); }`
- **UI elements:**
    - Login button: `click('button[type=submit]')` (teks: "Masuk")
    - Detail siswa: `press('Detail')` (Alpine.js button, bukan `<a>` link)
    - Tab bar mobile: `assertPresent('.sticky.bottom-0')` (bukan `<nav>`)
- **Screenshot:** Otomatis disimpan per test di `tests/Browser/screenshots/` (subfolder per modul). Failure screenshot: `failure-*.png` di root screenshots/

## Development

```bash
# Start dev server (artisan + queue + vite)
composer dev

# Run tests
composer test

# Run browser tests (dev server harus jalan)
php artisan dusk

# Fresh migrate + seed
php artisan migrate:fresh --seed
```

## ERD (Entity Relationship Diagram)

ERD database tersedia dalam format Mermaid.js, dipecah per modul di `docs/erd/` sebagai file `.mmd`:

| File                    | Modul           | Tabel                                                                                                                                                                            |
| ----------------------- | --------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `1_user_management.mmd` | User Management | users, roles, admins, teachers, religious_teachers, foundation_heads                                                                                                             |
| `2_siswa_kelas.mmd`     | Siswa & Kelas   | classes, students, student_attendance                                                                                                                                            |
| `3_mata_pelajaran.mmd`  | Mata Pelajaran  | subjects, subject_teachers, students_grades                                                                                                                                      |
| `4_ppdb.mmd`            | PPDB            | registrations, registration_fees, fee_installments, registration_transactions, registration_settings                                                                             |
| `5_spp.mmd`             | SPP             | spp_invoices, spp_payments                                                                                                                                                       |
| `6_tabungan.mmd`        | Tabungan Siswa  | saving_ledgers, student_passbooks, saving_transactions                                                                                                                           |
| `7_absensi_guru.mmd`    | Absensi Guru    | attendance_records, geofence_zones, attendance_settings                                                                                                                          |
| `8_sistem_raport.mmd`   | Sistem Raport   | academic_periods, student_report_cards, narrative_assessments, narrative_photos, development_categories, student_checklist_assessments, physical_measurements, health_conditions |

- File `.mmd` berisi pure Mermaid code (dimulai langsung dengan `erDiagram`) — tanpa markdown wrapper
- File referensi lengkap (markdown): `docs/ERD_ICH_PENDIDIKAN.md`
- Gunakan tanda hubung (`-`) bukan koma di comment string Mermaid (contoh: `"5-2"` bukan `"5,2"`)
- Label relasi harus deskriptif dalam bahasa Indonesia (contoh: `memiliki`, `profil`, `mencatat`)

## Git Conventions

- Jangan commit file laporan TA (folder `docs/` dan `referensi/` sudah di `.gitignore`)
- Jangan tambahkan baris `Co-Authored-By` di pesan commit
- Commit setiap perubahan — jangan menumpuk banyak perubahan dalam satu commit besar
- Pesan commit dalam bahasa Indonesia, singkat dan deskriptif
