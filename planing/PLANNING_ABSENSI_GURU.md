# Planning Fitur Absensi Guru

## Konteks

Fitur absensi guru berbasis geolocation + foto selfie. Tabel dan model dasar
(`attendance_records`, `attendance_settings`) sudah ada dari migration sebelumnya.
Yang perlu dibangun: migration tambahan (selfie), Service, Controller, Request, Routes, Views.

## Keputusan Desain

- **Geofencing: WAJIB** — sistem otomatis validasi apakah guru dalam radius sekolah.
- **Selfie: WAJIB** — guru upload foto selfie saat check-in sebagai bukti tambahan.
- **Self-service** — guru check-in/check-out sendiri dari perangkat masing-masing.
- **Admin** — hanya bisa melihat rekap, tidak menginput absensi guru.

---

## Struktur Tabel Existing (`attendance_records`)

```
attendance_record_id   PK
teacher_id             FK → teachers
check_in_time          datetime (nullable)
check_in_latitude      decimal(10,7) (nullable)
check_in_longitude     decimal(10,7) (nullable)
check_in_accuracy      string (nullable)
is_within_geofence     enum: ya | tidak (nullable)
attendance_status      enum: Masuk | Izin | Sakit
check_out_time         datetime (nullable)
check_out_latitude     decimal(10,7) (nullable)
check_out_longitude    decimal(10,7) (nullable)
timestamps
```

## Perubahan Database yang Diperlukan

### Migration Baru: Tambah Kolom Selfie

File: `database/migrations/2026_04_29_000001_add_selfie_to_attendance_records_table.php`

```php
// Tambah kolom selfie_path ke tabel attendance_records
$table->string('selfie_path')->nullable()->after('check_in_accuracy');
```

---

## Yang Perlu Dibangun

### 1. Service Layer → `app/Services/Attendance/`

**GeofenceService**
```php
public function isWithinZone(float $lat, float $lng): bool
// Ambil config dari attendance_settings (center lat, lng, radius)
// Hitung jarak dengan Haversine formula
// Return true jika jarak ≤ radius

public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
// Haversine formula, return jarak dalam meter
```

**AttendanceService**
```php
public function checkIn(array $data): AttendanceRecord
// $data: teacher_id, lat, lng, accuracy, selfie file
// Validasi: belum ada record hari ini
// Panggil GeofenceService untuk isi is_within_geofence
// Simpan selfie ke storage/app/public/attendance/selfies/
// Return AttendanceRecord

public function recordIzinSakit(array $data): AttendanceRecord
// $data: teacher_id, status (Izin/Sakit)
// Validasi: belum ada record hari ini
// Simpan tanpa GPS dan selfie

public function getTodayRecord(int $teacherId): ?AttendanceRecord
// Cari record hari ini milik guru yang login
// Return null jika belum ada

public function getAll(array $filters = []): Collection
// Filter: tanggal, status, nama guru
// Eager load: teacher.user

public function getMonthlyRecap(int $year, int $month): Collection
// Rekap per guru per bulan
// Group by teacher, hitung jumlah Masuk/Izin/Sakit
```

### 2. Controllers → `app/Http/Controllers/Attendance/`

**AttendanceController** (untuk guru)
```php
index()     → tampilkan halaman absensi + status hari ini
checkIn()   → proses check-in (GPS + selfie)
checkOut()  → proses check-out (GPS)
izinSakit() → proses izin/sakit (tanpa GPS)
```

**AttendanceAdminController** (untuk admin/kepala sekolah)
```php
index()  → daftar absensi hari ini semua guru
rekap()  → rekap bulanan dengan filter
```

### 3. Form Requests → `app/Http/Requests/Attendance/`

**CheckInRequest**
```php
'latitude'  => ['required', 'numeric', 'between:-90,90'],
'longitude' => ['required', 'numeric', 'between:-180,180'],
'accuracy'  => ['required', 'numeric'],
'selfie'    => ['required', 'image', 'max:5120'], // max 5MB
```

**CheckOutRequest**
```php
'latitude'  => ['required', 'numeric', 'between:-90,90'],
'longitude' => ['required', 'numeric', 'between:-180,180'],
```

**IzinSakitRequest**
```php
'status' => ['required', 'in:Izin,Sakit'],
```

### 4. Routes (`routes/web.php`)

```php
// Guru: self-service absensi
Route::middleware(['auth', 'role:Guru,Guru Ngaji'])
    ->prefix('absensi')->name('attendance.')
    ->group(function () {
        Route::get('/',           [AttendanceController::class, 'index'])->name('index');
        Route::post('/check-in',  [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
        Route::post('/izin-sakit',[AttendanceController::class, 'izinSakit'])->name('izin-sakit');
    });

// Admin & Kepala Sekolah: lihat rekap
Route::middleware(['auth', 'role:Admin,Kepala Sekolah'])
    ->prefix('admin/absensi')->name('attendance.admin.')
    ->group(function () {
        Route::get('/',      [AttendanceAdminController::class, 'index'])->name('index');
        Route::get('/rekap', [AttendanceAdminController::class, 'rekap'])->name('rekap');
    });
```

### 5. Views → `resources/views/attendance/`

**`index.blade.php`** (halaman guru)
- Tampilkan status absensi hari ini
- Tombol **Check In**: aktifkan kamera + GPS via browser API
- Tombol **Check Out**: aktifkan GPS via browser API
- Tombol **Izin / Sakit**: form dropdown tanpa GPS

**`admin/index.blade.php`**
- Tabel semua guru + status absensi hari ini
- Filter by tanggal

**`admin/rekap.blade.php`**
- Tabel rekap bulanan per guru
- Kolom: Nama, Masuk, Izin, Sakit, Total Hari Kerja

---

## Urutan Implementasi

1. Migration baru → tambah `selfie_path` ke `attendance_records`
2. Update Model `AttendanceRecord` → tambah `selfie_path` ke fillable
3. `GeofenceService` → Haversine formula + baca config dari `attendance_settings`
4. `AttendanceService` → logika check-in/check-out/izin + simpan selfie
5. Form Requests → validasi input
6. Controllers → sambungkan request ke service
7. Routes → daftarkan dengan middleware role
8. Views → UI dengan browser Geolocation API + kamera

---

## File yang Sudah Ada (Tidak Perlu Dibuat Ulang)

```
app/Models/AttendanceRecord.php      ✅ (perlu update fillable)
app/Models/AttendanceSetting.php     ✅
app/Http/Middleware/RoleMiddleware.php ✅
database/migrations/..._create_attendance_records_table.php ✅
database/migrations/..._create_attendance_settings_table.php ✅
```

## Status Implementasi

| Komponen | Status |
|---|---|
| Migration selfie_path | ⏳ Belum |
| GeofenceService | ⏳ Belum |
| AttendanceService | ⏳ Belum |
| CheckInRequest | ⏳ Belum |
| CheckOutRequest | ⏳ Belum |
| IzinSakitRequest | ⏳ Belum |
| AttendanceController | ⏳ Belum |
| AttendanceAdminController | ⏳ Belum |
| Routes | ⏳ Belum |
| Views | ⏳ Belum |
