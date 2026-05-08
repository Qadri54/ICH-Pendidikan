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
        return StudentPassbook::with('student')
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
}
