<?php

namespace App\Services\Saving;

use App\Models\SavingTransaction;
use App\Models\StudentPassbook;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SavingTransactionService
{
    // Ambil semua transaksi dalam satu passbook, urut terbaru.
    // Dipakai di halaman detail buku tabungan siswa.
    public function getByPassbook(int $passbookId): Collection
    {
        return SavingTransaction::where('passbook_id', $passbookId)
            ->latest('transaction_date')
            ->get();
    }

    // Ambil semua transaksi dalam satu ledger dengan filter opsional.
    // Filter: student_id, transaction_type, tanggal_mulai, tanggal_selesai.
    // Dipakai di halaman rekap transaksi per ledger.
    public function getByLedger(int $ledgerId, array $filters = []): Collection
    {
        $query = SavingTransaction::with(['student', 'passbook'])
            ->where('ledger_id', $ledgerId)
            ->latest('transaction_date');

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }
        if (!empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }
        if (!empty($filters['tanggal_mulai'])) {
            $query->whereDate('transaction_date', '>=', $filters['tanggal_mulai']);
        }
        if (!empty($filters['tanggal_selesai'])) {
            $query->whereDate('transaction_date', '<=', $filters['tanggal_selesai']);
        }

        return $query->get();
    }

    // Catat transaksi setor (deposit) untuk satu buku tabungan siswa.
    // Menggunakan DB::transaction() karena ada 3 tabel yang berubah:
    //   1. saving_transactions → insert record baru
    //   2. student_passbooks   → current_balance += amount
    //   3. saving_ledgers      → total_balance += amount
    // $data: amount, description, transaction_date
    public function deposit(int $passbookId, array $data): SavingTransaction
    {
        $passbook = StudentPassbook::with('ledger')->findOrFail($passbookId);

        if ($passbook->ledger->status === 'Closed') {
            throw new \InvalidArgumentException('Tidak bisa melakukan transaksi pada ledger yang sudah ditutup.');
        }

        return DB::transaction(function () use ($passbook, $data) {
            $transaction = SavingTransaction::create([
                'student_id'         => $passbook->student_id,
                'ledger_id'          => $passbook->ledger_id,
                'passbook_id'        => $passbook->passbook_id,
                'transaction_date'   => $data['transaction_date'] ?? now(),
                'transaction_type'   => 'deposit',
                'amount'             => $data['amount'],
                'description'        => $data['description'] ?? null,
                'transaction_number' => $this->generateTransactionNumber(),
                'last_update'        => now(),
            ]);

            // Update saldo passbook siswa
            $passbook->increment('current_balance', $data['amount']);
            $passbook->update(['last_update' => now()]);

            // Update total saldo di ledger
            $passbook->ledger->increment('total_balance', $data['amount']);

            return $transaction;
        });
    }

    // Catat transaksi tarik (withdrawal) untuk satu buku tabungan siswa.
    // Validasi: jumlah tarik tidak boleh melebihi saldo saat ini.
    // Menggunakan DB::transaction() karena ada 3 tabel yang berubah:
    //   1. saving_transactions → insert record baru
    //   2. student_passbooks   → current_balance -= amount
    //   3. saving_ledgers      → total_balance -= amount
    // $data: amount, description, transaction_date
    public function withdraw(int $passbookId, array $data): SavingTransaction
    {
        $passbook = StudentPassbook::with('ledger')->findOrFail($passbookId);

        if ($passbook->ledger->status === 'Closed') {
            throw new \InvalidArgumentException('Tidak bisa melakukan transaksi pada ledger yang sudah ditutup.');
        }

        if ($data['amount'] > $passbook->current_balance) {
            throw new \InvalidArgumentException(
                "Jumlah penarikan ({$data['amount']}) melebihi saldo saat ini ({$passbook->current_balance})."
            );
        }

        return DB::transaction(function () use ($passbook, $data) {
            $transaction = SavingTransaction::create([
                'student_id'         => $passbook->student_id,
                'ledger_id'          => $passbook->ledger_id,
                'passbook_id'        => $passbook->passbook_id,
                'transaction_date'   => $data['transaction_date'] ?? now(),
                'transaction_type'   => 'withdrawal',
                'amount'             => $data['amount'],
                'description'        => $data['description'] ?? null,
                'transaction_number' => $this->generateTransactionNumber(),
                'last_update'        => now(),
            ]);

            // Kurangi saldo passbook siswa
            $passbook->decrement('current_balance', $data['amount']);
            $passbook->update(['last_update' => now()]);

            // Kurangi total saldo di ledger
            $passbook->ledger->decrement('total_balance', $data['amount']);

            return $transaction;
        });
    }

    // Generate nomor transaksi unik dengan format: TRX-YYYYMMDD-XXXXXX
    // Contoh: TRX-20251001-483920
    // Cek keunikan sebelum digunakan untuk menghindari duplikasi.
    private function generateTransactionNumber(): string
    {
        do {
            $number = 'TRX-' . Carbon::now()->format('Ymd') . '-' . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (SavingTransaction::where('transaction_number', $number)->exists());

        return $number;
    }
}
