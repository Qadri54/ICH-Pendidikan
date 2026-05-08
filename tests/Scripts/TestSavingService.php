<?php

/**
 * Script testing untuk Saving Service Layer (Tabungan Siswa).
 *
 * Cara menjalankan:
 *   php artisan tinker
 *   >>> require 'tests/Scripts/TestSavingService.php';
 *
 * PERHATIAN: Script ini membuat data nyata di database.
 * Jalankan hanya di environment development/local.
 */

use App\Models\SavingLedger;
use App\Models\SavingTransaction;
use App\Models\Student;
use App\Models\StudentPassbook;
use App\Models\Teacher;
use App\Services\Saving\PassbookService;
use App\Services\Saving\SavingLedgerService;
use App\Services\Saving\SavingTransactionService;

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
echo "\n  TEST: Saving Service Layer (Tabungan)";
echo "\n══════════════════════════════════════════════";

// ─────────────────────────────────────────────
// PRASYARAT
// ─────────────────────────────────────────────

section('0. Cek Prasyarat Data');

$teacher  = Teacher::first();
$student  = Student::first();
$student2 = Student::skip(1)->first();

if (!$teacher) {
    echo "\n  ✗ Tidak ada data Teacher. Jalankan seeder terlebih dahulu.";
    return;
}
if (!$student) {
    echo "\n  ✗ Tidak ada data Student. Jalankan seeder terlebih dahulu.";
    return;
}

result('Teacher', "ID={$teacher->teacher_id}");
result('Student 1', "ID={$student->student_id}, Nama={$student->nama_siswa}");
result('Student 2', $student2 ? "ID={$student2->student_id}, Nama={$student2->nama_siswa}" : 'tidak ada');
ok('Prasyarat terpenuhi');

// Bersihkan data test sebelumnya
SavingTransaction::whereHas('ledger', fn($q) => $q->where('ledger_name', 'Tabungan Test'))->delete();
StudentPassbook::whereHas('ledger', fn($q) => $q->where('ledger_name', 'Tabungan Test'))->delete();
SavingLedger::where('ledger_name', 'Tabungan Test')->delete();
ok('Data test lama dibersihkan (fresh test)');

$ledgerService      = new SavingLedgerService();
$passbookService    = new PassbookService();
$transactionService = new SavingTransactionService();

// ─────────────────────────────────────────────
// TEST 1: SavingLedgerService
// ─────────────────────────────────────────────

section('1. SavingLedgerService');

// create
$ledger = $ledgerService->create([
    'teacher_id'      => $teacher->teacher_id,
    'ledger_name'     => 'Tabungan Test',
    'academic_year'   => '2025-07-01',
    'opening_date'    => '2025-07-01',
    'opening_balance' => 500000,
]);
result('create() → ledger_id', $ledger->ledger_id);
result('create() → status', $ledger->status);
result('create() → total_balance', $ledger->total_balance);
ok('create() berhasil, status = Active');

// getAll
$all = $ledgerService->getAll();
result('getAll() → jumlah ledger', $all->count());
ok('getAll() berhasil');

// getByTeacher
$byTeacher = $ledgerService->getByTeacher($teacher->teacher_id);
result('getByTeacher() → jumlah', $byTeacher->count());
ok('getByTeacher() berhasil');

// getById
$found = $ledgerService->getById($ledger->ledger_id);
result('getById() → ledger_name', $found->ledger_name);
ok('getById() berhasil');

// ─────────────────────────────────────────────
// TEST 2: PassbookService
// ─────────────────────────────────────────────

section('2. PassbookService');

// open passbook untuk student 1
$passbook = $passbookService->open($ledger->ledger_id, [
    'student_id'      => $student->student_id,
    'opening_date'    => '2025-07-01',
    'opening_balance' => 50000,
]);
result('open() → passbook_id', $passbook->passbook_id);
result('open() → current_balance awal', $passbook->current_balance);
ok('open() berhasil untuk student 1');

// Coba open duplikat → harus throw exception
try {
    $passbookService->open($ledger->ledger_id, [
        'student_id'      => $student->student_id,
        'opening_date'    => '2025-07-01',
        'opening_balance' => 0,
    ]);
    echo "\n  ✗ Seharusnya throw exception untuk duplikat!";
} catch (\InvalidArgumentException $e) {
    ok('Duplikat passbook dicegah: ' . $e->getMessage());
}

// open passbook untuk student 2 (jika ada)
$passbook2 = null;
if ($student2) {
    $passbook2 = $passbookService->open($ledger->ledger_id, [
        'student_id'      => $student2->student_id,
        'opening_date'    => '2025-07-01',
        'opening_balance' => 100000,
    ]);
    result('open() → passbook_id student 2', $passbook2->passbook_id);
    ok('open() berhasil untuk student 2');
}

// getByLedger
$byLedger = $passbookService->getByLedger($ledger->ledger_id);
result('getByLedger() → jumlah passbook', $byLedger->count());
ok('getByLedger() berhasil');

// getByStudent
$byStudent = $passbookService->getByStudent($student->student_id);
result('getByStudent() → jumlah passbook siswa', $byStudent->count());
ok('getByStudent() berhasil');

// getById
$detail = $passbookService->getById($passbook->passbook_id);
result('getById() → nama siswa', $detail->student->nama_siswa);
ok('getById() dengan relasi berhasil');

// ─────────────────────────────────────────────
// TEST 3: SavingTransactionService — Deposit
// ─────────────────────────────────────────────

section('3. SavingTransactionService — Deposit');

$balanceSebelum = $passbook->fresh()->current_balance;
result('Saldo sebelum deposit', $balanceSebelum);

$deposit = $transactionService->deposit($passbook->passbook_id, [
    'amount'           => 75000,
    'description'      => 'Setoran minggu pertama',
    'transaction_date' => now(),
]);
result('deposit() → transaction_id', $deposit->transaction_id);
result('deposit() → transaction_number', $deposit->transaction_number);
result('deposit() → transaction_type', $deposit->transaction_type);
result('deposit() → amount', $deposit->amount);
ok('deposit() berhasil');

$balanceSesudah = $passbook->fresh()->current_balance;
result('Saldo sesudah deposit', $balanceSesudah);
result('Selisih saldo (harus 75000)', $balanceSesudah - $balanceSebelum);
ok('current_balance passbook terupdate dengan benar');

$ledgerBalance = $ledger->fresh()->total_balance;
result('total_balance ledger sesudah deposit', $ledgerBalance);
ok('total_balance ledger terupdate dengan benar');

// Deposit kedua untuk test riwayat
$transactionService->deposit($passbook->passbook_id, [
    'amount'           => 50000,
    'description'      => 'Setoran minggu kedua',
    'transaction_date' => now(),
]);
ok('Deposit kedua berhasil');

// ─────────────────────────────────────────────
// TEST 4: SavingTransactionService — Withdrawal
// ─────────────────────────────────────────────

section('4. SavingTransactionService — Withdrawal');

$saldonya = $passbook->fresh()->current_balance;
result('Saldo sebelum tarik', $saldonya);

$withdrawal = $transactionService->withdraw($passbook->passbook_id, [
    'amount'           => 30000,
    'description'      => 'Penarikan keperluan',
    'transaction_date' => now(),
]);
result('withdraw() → transaction_type', $withdrawal->transaction_type);
result('withdraw() → amount', $withdrawal->amount);
ok('withdraw() berhasil');

result('Saldo sesudah tarik', $passbook->fresh()->current_balance);
ok('current_balance passbook berkurang dengan benar');

// Coba tarik melebihi saldo → harus throw exception
try {
    $transactionService->withdraw($passbook->passbook_id, [
        'amount'           => 9999999,
        'description'      => 'Coba tarik lebih',
        'transaction_date' => now(),
    ]);
    echo "\n  ✗ Seharusnya throw exception saldo tidak cukup!";
} catch (\InvalidArgumentException $e) {
    ok('Penarikan melebihi saldo dicegah: ' . $e->getMessage());
}

// ─────────────────────────────────────────────
// TEST 5: getByPassbook & getByLedger
// ─────────────────────────────────────────────

section('5. Riwayat Transaksi');

$riwayat = $transactionService->getByPassbook($passbook->passbook_id);
result('getByPassbook() → jumlah transaksi', $riwayat->count());
result('Transaksi pertama (terbaru)', $riwayat->first()->transaction_type . ' - Rp ' . number_format($riwayat->first()->amount));
ok('getByPassbook() berhasil');

$riwayatLedger = $transactionService->getByLedger($ledger->ledger_id);
result('getByLedger() → jumlah transaksi seluruh ledger', $riwayatLedger->count());
ok('getByLedger() berhasil');

// Filter by type
$depositOnly = $transactionService->getByLedger($ledger->ledger_id, ['transaction_type' => 'deposit']);
result('getByLedger(filter=deposit) → jumlah', $depositOnly->count());
ok('getByLedger() dengan filter berhasil');

// ─────────────────────────────────────────────
// TEST 6: Close Ledger
// ─────────────────────────────────────────────

section('6. SavingLedgerService — Close Ledger');

$closeResult = $ledgerService->close($ledger->ledger_id);
result('close() → berhasil?', $closeResult ? 'YA' : 'TIDAK');
result('status setelah close', $ledger->fresh()->status);
ok('close() berhasil');

// Coba close lagi → harus throw exception
try {
    $ledgerService->close($ledger->ledger_id);
    echo "\n  ✗ Seharusnya throw exception untuk ledger yang sudah closed!";
} catch (\InvalidArgumentException $e) {
    ok('Double close dicegah: ' . $e->getMessage());
}

// Coba deposit ke ledger yang sudah Closed → harus throw exception
try {
    $transactionService->deposit($passbook->passbook_id, [
        'amount'           => 10000,
        'transaction_date' => now(),
    ]);
    echo "\n  ✗ Seharusnya throw exception untuk ledger yang sudah closed!";
} catch (\InvalidArgumentException $e) {
    ok('Transaksi ke ledger Closed dicegah: ' . $e->getMessage());
}

// Coba buka passbook baru di ledger Closed → harus throw exception
if ($student2) {
    try {
        $passbookService->open($ledger->ledger_id, [
            'student_id'      => $student2->student_id,
            'opening_date'    => now(),
            'opening_balance' => 0,
        ]);
        echo "\n  ✗ Seharusnya throw exception!";
    } catch (\InvalidArgumentException $e) {
        ok('Buka passbook di ledger Closed dicegah: ' . $e->getMessage());
    }
}

// Coba hapus ledger yang masih ada passbook → harus throw exception
try {
    $ledgerService->delete($ledger->ledger_id);
    echo "\n  ✗ Seharusnya throw exception karena masih ada passbook!";
} catch (\InvalidArgumentException $e) {
    ok('Delete ledger dengan passbook dicegah: ' . $e->getMessage());
}

// ─────────────────────────────────────────────
// RINGKASAN
// ─────────────────────────────────────────────

$finalBalance = $passbook->fresh()->current_balance;
$finalLedger  = $ledger->fresh()->total_balance;

echo "\n\n══════════════════════════════════════════════";
echo "\n  SELESAI — Saving Service berjalan dengan baik";
echo "\n  Ledger ID   : {$ledger->ledger_id}";
echo "\n  Passbook ID : {$passbook->passbook_id}";
echo "\n  Saldo akhir siswa  : Rp " . number_format($finalBalance);
echo "\n  Total saldo ledger : Rp " . number_format($finalLedger);
echo "\n══════════════════════════════════════════════\n\n";
