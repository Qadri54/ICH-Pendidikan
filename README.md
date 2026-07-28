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

# 5. Seed data demo
php artisan migrate:fresh --seed

# 6. Build frontend
npm run build

# 7. Jalankan server
php artisan serve
```

---

## Demo & Testing

### Seed Data Demo

Jalankan perintah berikut untuk mengisi database dengan data demo yang siap presentasi:

```bash
php artisan migrate:fresh --seed
```

Data yang di-generate:
- 22 siswa aktif + 17 alumni di 3 kelas
- 3 guru (2 Guru TK + 1 Guru Ngaji) sebagai wali kelas
- 6 bulan tagihan SPP per siswa (paid/pending/unpaid)
- Data absensi siswa & guru selama 1 semester
- 17 raport digital lengkap (naratif + checklist + pengukuran fisik)
- 2 ledger tabungan dengan passbook & transaksi per siswa
- 3 pendaftaran PPDB (2 pending + 1 diterima dengan cicilan)
- Geofence zone sekolah untuk absensi GPS guru

### Akun Demo

Semua akun menggunakan password: **`password123`**

| Role | Email | Keterangan |
|------|-------|------------|
| Admin | `admin@iqra.com` | Full CRUD semua modul |
| Guru (Wali Kelas A) | `lisma.pane@iqra.com` | Absensi, tabungan, raport Kelas A |
| Guru (Wali Kelas B) | `guru@iqra.com` | Absensi, tabungan, raport Kelas B |
| Kepala Sekolah | `kepsek@iqra.com` | Read-only admin area |
| Kepala Yayasan | `yayasan@iqra.com` | Read-only admin area |
| Orang Tua | `aswan.lubis@iqra.com` | Portal lengkap (SPP, kehadiran, raport, tabungan) |
| Orang Tua (2 anak) | `binsar.sitompul@iqra.com` | 1 anak alumni + 1 anak aktif |
| Calon Ortu 1 | `demo.ortu1@iqra.com` | Pendaftaran **pending** (untuk approve) |
| Calon Ortu 2 | `demo.ortu2@iqra.com` | Pendaftaran **pending** (untuk reject) |
| Calon Ortu 3 | `demo.ortu3@iqra.com` | Diterima, cicilan **pending** (untuk approve) |

### Panduan Testing End-to-End

#### 1. Flow PPDB (Pendaftaran Peserta Didik Baru)

**Sebagai Orang Tua** — login `demo.ortu1@iqra.com`:
1. Buka halaman Pendaftaran — lihat status pendaftaran "Menunggu"

**Sebagai Admin** — login `admin@iqra.com`:
1. Buka `/admin/pendaftaran` — terlihat 2 pendaftaran pending
2. Klik salah satu pendaftaran, lihat detail data anak & orang tua
3. **Approve** pendaftaran `demo.ortu1` — notifikasi WA terkirim ke orang tua
4. **Reject** pendaftaran `demo.ortu2` dengan alasan — notifikasi WA terkirim

**Verifikasi:** Login kembali sebagai `demo.ortu1@iqra.com`, status berubah menjadi "Diterima"

#### 2. Flow Pembayaran Pendaftaran (Cicilan)

**Sebagai Admin** — login `admin@iqra.com`:
1. Buka `/admin/pembayaran-pendaftaran` — terlihat 1 transaksi pending (Khadijah Azzahra, Rp 1.000.000)
2. **Approve** pembayaran — notifikasi WA terkirim ke `demo.ortu3`

**Sebagai Orang Tua** — login `demo.ortu3@iqra.com`:
1. Buka halaman Pembayaran — lihat riwayat cicilan (1 approved + 1 baru di-approve)
2. Sisa tagihan terupdate otomatis

#### 3. Flow Keuangan SPP

**Sebagai Orang Tua** — login `aswan.lubis@iqra.com`:
1. Buka halaman Pembayaran — lihat tagihan SPP bulan 1-6
2. Bulan 1-4: Lunas | Bulan 5: Menunggu konfirmasi | Bulan 6: Belum bayar
3. Upload bukti pembayaran untuk bulan 6 — notifikasi masuk ke admin

**Sebagai Admin** — login `admin@iqra.com`:
1. Klik notifikasi yang masuk — langsung diarahkan ke halaman bukti bayar dengan highlight
2. Buka `/admin/keuangan/bukti-pembayaran` — filter "Pending"
3. **Approve** bukti bayar — notifikasi WA terkirim ke orang tua
4. Buka `/admin/keuangan` — lihat ringkasan & total pendapatan SPP

#### 4. Flow Tabungan Siswa

**Sebagai Guru** — login `lisma.pane@iqra.com`:
1. Buka `/guru/tabungan` — terlihat ledger "Tabungan Kelas A Sem 1 2025/2026"
2. Klik ledger — lihat daftar passbook siswa dengan saldo masing-masing
3. Klik salah satu passbook — lihat riwayat transaksi (5 setoran, beberapa ada penarikan)
4. Coba catat setoran/penarikan baru

**Sebagai Orang Tua** — login `aswan.lubis@iqra.com`:
1. Buka halaman Tabungan — lihat saldo terkini dan riwayat transaksi anak

**Sebagai Admin** — login `admin@iqra.com`:
1. Buka `/admin/tabungan` — lihat semua ledger lintas kelas

#### 5. Flow Absensi Siswa

**Sebagai Guru** — login `lisma.pane@iqra.com`:
1. Buka `/guru/absensi` — pilih tanggal hari ini
2. Input kehadiran siswa Kelas A (Hadir/Sakit/Izin/Alpha)
3. Simpan — data langsung tersimpan

**Sebagai Orang Tua** — login `aswan.lubis@iqra.com`:
1. Buka halaman Kehadiran — lihat ringkasan bulanan (% hadir, sakit, izin, alpha)
2. Lihat riwayat ketidakhadiran detail

**Sebagai Admin** — login `admin@iqra.com`:
1. Buka `/admin/absensi` — lihat rekap absensi semua kelas

#### 6. Flow Absensi Guru (GPS)

**Sebagai Guru** — login `lisma.pane@iqra.com`:
1. Buka `/guru/absensi-guru` — klik Check-in
2. Izinkan akses lokasi GPS — sistem validasi posisi terhadap geofence sekolah
3. Ambil selfie (opsional) — data check-in tersimpan

**Sebagai Admin** — login `admin@iqra.com`:
1. Buka `/admin/absensi-guru` — lihat rekap kehadiran guru + status geofence

#### 7. Flow Raport Digital

**Sebagai Admin** — login `admin@iqra.com`:
1. Buka `/admin/raport` — lihat 17 raport berstatus "Approved"
2. Klik salah satu raport — lihat detail penilaian naratif, checklist, pengukuran fisik
3. Download PDF raport

**Sebagai Orang Tua** — login `aswan.lubis@iqra.com`:
1. Buka halaman Akademik — lihat raport anak
2. Download PDF raport

#### 8. Flow Notifikasi WhatsApp

Notifikasi WhatsApp otomatis terkirim saat:
- Admin approve/reject pendaftaran
- Admin approve/reject bukti bayar SPP
- Admin approve/reject pembayaran pendaftaran
- Admin approve raport (tersedia untuk diunduh)
- Orang tua upload bukti bayar SPP (notifikasi ke admin)
- Orang tua upload bukti bayar pendaftaran (notifikasi ke admin)

**Prasyarat:** Pastikan `FONNTE_TOKEN` di `.env` sudah diisi dan device WhatsApp terhubung di [fonnte.com](https://fonnte.com)

#### 9. Laporan & Dashboard

**Sebagai Admin** — login `admin@iqra.com`:
1. Buka `/admin` (dashboard) — lihat statistik: jumlah siswa, guru, pendaftaran pending, SPP pending
2. Buka `/admin/laporan` — lihat total pendapatan SPP & pendaftaran, statistik kehadiran

**Sebagai Kepala Sekolah** — login `kepsek@iqra.com`:
1. Akses halaman admin yang sama (read-only) — tidak bisa edit/hapus data

---

## Status Proyek

**Versi:** 1.0.0-dev &nbsp;|&nbsp; **Status:** In Development
