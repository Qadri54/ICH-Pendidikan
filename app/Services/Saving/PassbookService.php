<?php

namespace App\Services\Saving;

use App\Models\SavingLedger;
use App\Models\StudentPassbook;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PassbookService
{
    // Ambil semua passbook dalam satu ledger beserta data siswa.
    // Dipakai di halaman detail ledger untuk melihat daftar siswa peserta tabungan.
    public function getByLedger(int $ledgerId): Collection
    {
        return StudentPassbook::with('student.classRoom')
            ->where('ledger_id', $ledgerId)
            ->get();
    }

    // Ambil semua passbook milik satu siswa di semua ledger.
    // Dipakai di halaman profil siswa untuk melihat riwayat tabungan.
    public function getByStudent(int $studentId): Collection
    {
        return StudentPassbook::with('ledger.teacher.user')
            ->where('student_id', $studentId)
            ->latest()
            ->get();
    }

    // Ambil satu passbook lengkap beserta riwayat transaksinya.
    // Dipakai di halaman detail buku tabungan siswa.
    public function getById(int $passbookId): StudentPassbook
    {
        return StudentPassbook::with([
            'student',
            'ledger.teacher.user',
            'savingTransactions',
        ])->findOrFail($passbookId);
    }

    // Buka buku tabungan baru untuk satu siswa dalam satu ledger.
    // Validasi: satu siswa hanya boleh punya satu passbook per ledger.
    // Throw jika ledger sudah berstatus Closed.
    // $data: student_id, opening_date, opening_balance
    public function open(int $ledgerId, array $data): StudentPassbook
    {
        $ledger = SavingLedger::findOrFail($ledgerId);

        if ($ledger->status === 'Closed') {
            throw new \InvalidArgumentException('Tidak bisa membuka buku tabungan pada ledger yang sudah ditutup.');
        }

        $exists = StudentPassbook::where('ledger_id', $ledgerId)
            ->where('student_id', $data['student_id'])
            ->exists();

        if ($exists) {
            throw new \InvalidArgumentException('Siswa ini sudah memiliki buku tabungan dalam ledger ini.');
        }

        return StudentPassbook::create([
            'student_id'      => $data['student_id'],
            'ledger_id'       => $ledgerId,
            'opening_date'    => $data['opening_date'],
            'opening_balance' => $data['opening_balance'] ?? 0,
            'current_balance' => $data['opening_balance'] ?? 0,
            'last_update'     => now(),
        ]);
    }

    public function bulkOpen(int $ledgerId, array $studentIds, string $openingDate, int $openingBalance = 0): int
    {
        $ledger = SavingLedger::findOrFail($ledgerId);

        if ($ledger->status === 'Closed') {
            throw new \InvalidArgumentException('Tidak bisa membuka buku tabungan pada ledger yang sudah ditutup.');
        }

        $existingIds = StudentPassbook::where('ledger_id', $ledgerId)
            ->whereIn('student_id', $studentIds)
            ->pluck('student_id')
            ->toArray();

        $newIds = array_diff($studentIds, $existingIds);

        if (empty($newIds)) {
            throw new \InvalidArgumentException('Semua siswa yang dipilih sudah memiliki buku tabungan.');
        }

        $rows = array_map(fn($sid) => [
            'student_id'      => $sid,
            'ledger_id'       => $ledgerId,
            'opening_date'    => $openingDate,
            'opening_balance' => $openingBalance,
            'current_balance' => $openingBalance,
            'last_update'     => now(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ], $newIds);

        StudentPassbook::insert($rows);

        return count($newIds);
    }

    public function delete(int $passbookId): bool
    {
        $passbook = StudentPassbook::withCount('savingTransactions')->findOrFail($passbookId);

        if ($passbook->saving_transactions_count > 0) {
            throw new \InvalidArgumentException('Buku tabungan tidak bisa dihapus karena sudah ada transaksi.');
        }

        return (bool) $passbook->delete();
    }
}
