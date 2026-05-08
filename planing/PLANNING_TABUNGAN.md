# Planning Fitur Tabungan Siswa

## Konteks

Fitur tabungan memungkinkan guru mengelola program tabungan siswa per tahun ajaran.
Guru membuat buku besar (ledger), lalu setiap siswa memiliki buku tabungan (passbook)
di dalamnya. Guru mencatat transaksi setor/tarik yang secara otomatis memperbarui saldo.

Migration dan model sudah tersedia. Yang perlu dibangun: Service, Controller, Request, Routes, Views.

---

## Struktur Database yang Sudah Ada

### Alur Relasi

```
Teacher (1)
  └── SavingLedger (N) — buku besar per tahun ajaran
        └── StudentPassbook (N) — satu per siswa per ledger
              └── SavingTransaction (N) — riwayat setor/tarik
```

### `saving_ledgers`
```
ledger_id       PK
teacher_id      FK → teachers
ledger_name     VARCHAR
academic_year   DATE
opening_date    DATE
opening_balance INT (default 0)
total_balance   INT (default 0)  ← diupdate otomatis setiap transaksi
status          ENUM(Active, Closed)
timestamps
```

### `student_passbooks`
```
passbook_id      PK
student_id       FK → students
ledger_id        FK → saving_ledgers
opening_date     DATE
opening_balance  INT (default 0)
current_balance  INT (default 0)  ← diupdate otomatis setiap transaksi
passbook_file    VARCHAR (nullable) — file laporan PDF
last_update      DATETIME (nullable)
timestamps
```

### `saving_transactions`
```
transaction_id     PK
student_id         FK → students
ledger_id          FK → saving_ledgers
passbook_id        FK → student_passbooks
transaction_date   DATETIME
transaction_type   VARCHAR — 'deposit' atau 'withdrawal'
amount             INT
description        VARCHAR (nullable)
transaction_number VARCHAR UNIQUE — digenerate otomatis
last_update        DATETIME (nullable)
timestamps
```

---

## Business Rules

1. Satu siswa hanya boleh punya **satu passbook per ledger** (unique student_id + ledger_id)
2. **Withdrawal tidak boleh melebihi** `current_balance` passbook
3. `transaction_number` digenerate otomatis — format: `TRX-{YYYYMMDD}-{random 6 digit}`
4. Setiap transaksi wajib update:
   - `current_balance` di `student_passbooks`
   - `total_balance` di `saving_ledgers`
5. Gunakan `DB::transaction()` karena ada 3 tabel yang berubah sekaligus
6. Ledger berstatus `Closed` tidak bisa menerima transaksi baru

---

## Service Layer → `app/Services/Saving/`

### SavingLedgerService

```php
public function getAll(): Collection
// Ambil semua ledger dengan eager load teacher.user

public function getByTeacher(int $teacherId): Collection
// Ambil semua ledger milik guru yang login

public function getById(int $ledgerId): SavingLedger
// findOrFail() dengan eager load passbooks.student

public function create(array $data): SavingLedger
// $data: teacher_id, ledger_name, academic_year, opening_date, opening_balance

public function close(int $ledgerId): bool
// Ubah status Active → Closed
// Throw jika sudah Closed

public function delete(int $ledgerId): bool
// Throw jika masih ada passbook/transaksi terkait
```

### PassbookService

```php
public function getByLedger(int $ledgerId): Collection
// Ambil semua passbook dalam satu ledger, eager load student

public function getByStudent(int $studentId): Collection
// Ambil semua passbook milik satu siswa

public function getById(int $passbookId): StudentPassbook
// findOrFail() dengan eager load transaksi

public function open(int $ledgerId, int $studentId, array $data): StudentPassbook
// Buka passbook baru untuk siswa dalam ledger
// Validasi: satu siswa hanya satu passbook per ledger
// $data: opening_date, opening_balance

public function generateReport(int $passbookId): string
// Generate PDF laporan tabungan siswa
// Simpan ke storage, return path
```

### SavingTransactionService

```php
public function getByPassbook(int $passbookId): Collection
// Ambil semua transaksi satu passbook, urut terbaru

public function getByLedger(int $ledgerId, array $filters = []): Collection
// Ambil semua transaksi dalam satu ledger
// Filter: student_id, transaction_type, tanggal

public function deposit(int $passbookId, array $data): SavingTransaction
// Catat transaksi setor
// $data: amount, description, transaction_date
// DB::transaction():
//   1. Buat record di saving_transactions
//   2. Update current_balance passbook += amount
//   3. Update total_balance ledger += amount

public function withdraw(int $passbookId, array $data): SavingTransaction
// Catat transaksi tarik
// Validasi: amount <= current_balance passbook
// Validasi: ledger tidak berstatus Closed
// DB::transaction():
//   1. Buat record di saving_transactions
//   2. Update current_balance passbook -= amount
//   3. Update total_balance ledger -= amount

private function generateTransactionNumber(): string
// Generate nomor unik: TRX-{YYYYMMDD}-{random 6 digit}
// Cek keunikan sebelum dipakai
```

---

## Controllers → `app/Http/Controllers/Saving/`

### SavingLedgerController (untuk guru)
```php
index()   → daftar semua ledger milik guru
show()    → detail satu ledger + daftar passbook siswa
create()  → form buat ledger baru
store()   → simpan ledger baru
close()   → tutup ledger (Active → Closed)
```

### PassbookController (untuk guru)
```php
show()    → detail passbook + riwayat transaksi
open()    → buka passbook untuk siswa baru
report()  → generate & download PDF laporan tabungan siswa
```

### SavingTransactionController (untuk guru)
```php
store()    → proses deposit atau withdrawal
```

### SavingAdminController (untuk admin/kepala sekolah)
```php
index()   → rekap tabungan semua ledger
show()    → detail satu ledger
```

---

## Form Requests → `app/Http/Requests/Saving/`

**StoreLedgerRequest**
```php
'ledger_name'    => ['required', 'string', 'max:255'],
'academic_year'  => ['required', 'date'],
'opening_date'   => ['required', 'date'],
'opening_balance'=> ['required', 'integer', 'min:0'],
```

**OpenPassbookRequest**
```php
'student_id'     => ['required', 'exists:students,student_id'],
'opening_date'   => ['required', 'date'],
'opening_balance'=> ['required', 'integer', 'min:0'],
```

**StoreTransactionRequest**
```php
'amount'           => ['required', 'integer', 'min:1'],
'transaction_type' => ['required', 'in:deposit,withdrawal'],
'transaction_date' => ['required', 'date'],
'description'      => ['nullable', 'string', 'max:255'],
```

---

## Routes (`routes/web.php`)

```php
// Guru: kelola tabungan
Route::middleware(['auth', 'role:Guru,Guru Ngaji'])
    ->prefix('tabungan')->name('saving.')
    ->group(function () {
        // Ledger
        Route::get('/',                [SavingLedgerController::class, 'index'])->name('index');
        Route::get('/create',          [SavingLedgerController::class, 'create'])->name('create');
        Route::post('/',               [SavingLedgerController::class, 'store'])->name('store');
        Route::get('/{ledger}',        [SavingLedgerController::class, 'show'])->name('show');
        Route::patch('/{ledger}/close',[SavingLedgerController::class, 'close'])->name('close');

        // Passbook
        Route::post('/{ledger}/passbook',        [PassbookController::class, 'open'])->name('passbook.open');
        Route::get('/passbook/{passbook}',        [PassbookController::class, 'show'])->name('passbook.show');
        Route::get('/passbook/{passbook}/report', [PassbookController::class, 'report'])->name('passbook.report');

        // Transaksi
        Route::post('/passbook/{passbook}/transaction', [SavingTransactionController::class, 'store'])->name('transaction.store');
    });

// Admin: lihat rekap
Route::middleware(['auth', 'role:Admin,Kepala Sekolah'])
    ->prefix('admin/tabungan')->name('saving.admin.')
    ->group(function () {
        Route::get('/',          [SavingAdminController::class, 'index'])->name('index');
        Route::get('/{ledger}',  [SavingAdminController::class, 'show'])->name('show');
    });
```

---

## Urutan Implementasi

1. `SavingLedgerService` — CRUD ledger, tidak ada dependensi service lain
2. `PassbookService` — butuh ledger
3. `SavingTransactionService` — paling kompleks, butuh DB::transaction()
4. Form Requests — validasi input
5. Controllers — sambungkan ke service
6. Routes — daftarkan dengan middleware
7. Views — UI untuk guru kelola tabungan

---

## File yang Sudah Ada (Tidak Perlu Dibuat Ulang)

```
app/Models/SavingLedger.php        ✅
app/Models/StudentPassbook.php     ✅
app/Models/SavingTransaction.php   ✅
database/migrations/..._create_saving_ledgers_table.php      ✅
database/migrations/..._create_student_passbooks_table.php   ✅
database/migrations/..._create_saving_transactions_table.php ✅
```

## Status Implementasi

| Komponen | Status |
|---|---|
| SavingLedgerService | ⏳ Belum |
| PassbookService | ⏳ Belum |
| SavingTransactionService | ⏳ Belum |
| StoreLedgerRequest | ⏳ Belum |
| OpenPassbookRequest | ⏳ Belum |
| StoreTransactionRequest | ⏳ Belum |
| SavingLedgerController | ⏳ Belum |
| PassbookController | ⏳ Belum |
| SavingTransactionController | ⏳ Belum |
| SavingAdminController | ⏳ Belum |
| Routes | ⏳ Belum |
| Views | ⏳ Belum |
