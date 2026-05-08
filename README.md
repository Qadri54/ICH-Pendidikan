# ICH-Pendidikan — Sistem Informasi Manajemen TK IQRA' Creative House

Sistem manajemen sekolah terintegrasi untuk TK **IQRA' Creative House**, dibangun dengan Laravel 12. Mengotomatisasi proses PPDB, keuangan, tabungan, absensi siswa, dan komunikasi antara sekolah dengan orang tua.

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Blade, Tailwind CSS 3, Alpine.js 3 |
| Build Tool | Vite 7 |
| Database | MySQL |
| Auth | Laravel Breeze |

---

## Role & Akses

| Role | Akses |
|------|-------|
| **Admin / Kepala Sekolah / Kepala Yayasan** | Seluruh fitur admin — `/admin/*` |
| **Guru** | Absensi kelas & tabungan ledger — `/guru/*` |
| **Orang Tua** | Portal mobile — `/beranda`, `/pendaftaran`, dll. |

---

## Fitur yang Diimplementasikan

### Admin
- **PPDB** — Buka/tutup masa penerimaan, terima/tolak pendaftaran dengan alasan, kelola pembayaran biaya pendaftaran (cicilan & lunas), approve/reject bukti transfer
- **Siswa & Kelas** — CRUD siswa, CRUD kelas dengan penetapan wali kelas
- **Guru** — Manajemen guru kelas dan guru ngaji
- **User** — Manajemen akun user dan role
- **Keuangan (SPP)** — Generate tagihan bulanan otomatis, approve/reject pembayaran SPP
- **Tabungan** — CRUD ledger, buka buku tabungan per siswa, catat setoran & penarikan
- **Absensi Siswa** — Input & lihat kehadiran per kelas per tanggal
- **Laporan** — Total pendapatan (SPP + pendaftaran), statistik siswa/guru, daftar lunas pendaftaran
- **Pengaturan** — Toggle masa penerimaan siswa baru

### Guru
- **Absensi** — Input ketidakhadiran siswa untuk kelas yang diwalinya (bulk)
- **Tabungan** — Kelola ledger yang di-assign, catat setoran & penarikan

### Orang Tua (Mobile)
- **Pendaftaran** — Daftar anak (multi-anak), pantau status, lihat alasan penolakan
- **Keuangan** — Lihat tagihan pendaftaran & SPP, upload bukti transfer, lihat sisa tagihan
- **Tabungan** — Saldo terkini dan riwayat transaksi per anak
- **Kehadiran** — Ringkasan bulanan dan riwayat ketidakhadiran per anak
- **Profil Anak** — Data lengkap setiap anak yang terdaftar
- **Pengaturan** — Edit profil akun (nama, email, no HP, ganti password)

---

## Arsitektur

Seluruh business logic dikapsulasi dalam **service layer** — controller hanya menerima request, memanggil service, dan mengembalikan response.

```
app/
├── Http/Controllers/
│   ├── Admin/          # Controller admin
│   ├── Guru/           # Controller guru
│   └── OrangTua/       # Controller orang tua
├── Services/
│   ├── Attendance/     # StudentAttendanceService
│   ├── Registration/   # RegistrationService, RegistrationFeeService, RegistrationTransactionService
│   ├── Saving/         # SavingLedgerService, PassbookService, SavingTransactionService
│   ├── Spp/            # SppInvoiceService
│   └── User/           # UserService, StudentProfileService
└── Models/
```

---

## Instalasi

```bash
# 1. Clone & install dependencies
git clone <repo-url>
cd ICH-Pendidikan
composer install
npm install

# 2. Konfigurasi environment
cp .env.example .env
php artisan key:generate

# 3. Atur database di .env, lalu jalankan migration
php artisan migrate

# 4. Storage link untuk upload bukti pembayaran
php artisan storage:link

# 5. Build frontend
npm run build

# 6. Jalankan server
php artisan serve
```

---

## Status Proyek

**Versi:** 1.0.0-dev &nbsp;|&nbsp; **Status:** In Development
