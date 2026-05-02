<?php

namespace App\Services\Saving;

use App\Models\SavingLedger;
use Illuminate\Support\Collection;

class SavingLedgerService
{
    // Ambil semua ledger beserta guru yang mengelolanya.
    // Dipakai di halaman admin untuk melihat seluruh program tabungan.
    public function getAll(): Collection
    {
        return SavingLedger::with('teacher.user')->latest()->get();
    }

    public function getPaginated(?string $search, ?string $status, int $perPage = 15)
    {
        return SavingLedger::with('teacher.user')
            ->when($search, fn($q) => $q->where('ledger_name', 'like', "%{$search}%"))
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    // Ambil semua ledger milik guru yang sedang login.
    public function getByTeacher(int $teacherId): Collection
    {
        return SavingLedger::with('teacher.user')
            ->where('teacher_id', $teacherId)
            ->latest()
            ->get();
    }

    // Ambil satu ledger beserta semua passbook siswa di dalamnya.
    // Dipakai di halaman detail ledger.
    public function getById(int $ledgerId): SavingLedger
    {
        return SavingLedger::with(['teacher.user', 'passbooks.student'])
            ->findOrFail($ledgerId);
    }

    // Buat ledger baru untuk program tabungan satu tahun ajaran.
    // $data: teacher_id, ledger_name, academic_year, opening_date, opening_balance
    public function create(array $data): SavingLedger
    {
        return SavingLedger::create([
            'teacher_id'      => $data['teacher_id'],
            'ledger_name'     => $data['ledger_name'],
            'academic_year'   => $data['academic_year'],
            'opening_date'    => $data['opening_date'],
            'opening_balance' => $data['opening_balance'] ?? 0,
            'total_balance'   => $data['opening_balance'] ?? 0,
            'status'          => 'Active',
        ]);
    }

    // Tutup ledger — status Active → Closed.
    // Ledger yang sudah Closed tidak bisa menerima transaksi baru.
    // Throw jika ledger sudah berstatus Closed.
    public function close(int $ledgerId): bool
    {
        $ledger = SavingLedger::findOrFail($ledgerId);

        if ($ledger->status === 'Closed') {
            throw new \InvalidArgumentException('Ledger ini sudah ditutup.');
        }

        return (bool) $ledger->update(['status' => 'Closed']);
    }

    // Hapus ledger.
    // Throw jika masih ada passbook terkait agar data siswa tidak hilang.
    public function delete(int $ledgerId): bool
    {
        $ledger = SavingLedger::withCount('passbooks')->findOrFail($ledgerId);

        if ($ledger->passbooks_count > 0) {
            throw new \InvalidArgumentException('Ledger tidak bisa dihapus karena masih ada buku tabungan siswa.');
        }

        return (bool) $ledger->delete();
    }
}
