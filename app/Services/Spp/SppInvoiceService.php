<?php

namespace App\Services\Spp;

use App\Models\SppInvoice;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SppInvoiceService
{
    /**
     * Nominal SPP bulanan (IDR).
     * Sesuaikan dengan tarif yang berlaku di IQRA' Creative House.
     */
    const MONTHLY_FEE = 300000;

    /**
     * Generate invoice SPP bulan berjalan untuk seluruh siswa aktif.
     * Menggunakan bulk insert() — tidak ada loop individual.
     */
    public function generateMonthlyInvoices(): int
    {
        $now          = Carbon::now();
        $period       = $now->copy()->startOfMonth();        // tanggal_tahun : awal bulan
        $dueDate      = $now->copy()->endOfMonth();          // jatuh_tempo   : akhir bulan
        $createdAt    = $now->toDateTimeString();

        // Siswa yang belum punya invoice bulan ini (idempotent)
        $existingIds = SppInvoice::whereYear('tanggal_tahun', $now->year)
            ->whereMonth('tanggal_tahun', $now->month)
            ->pluck('student_id')
            ->all();

        $students = Student::whereNotIn('student_id', $existingIds)
            ->pluck('student_id')
            ->all();

        if (empty($students)) {
            return 0;
        }

        $rows = array_map(fn ($id) => [
            'student_id'    => $id,
            'tanggal_tahun' => $period->toDateString(),
            'jumlah'        => self::MONTHLY_FEE,
            'jatuh_tempo'   => $dueDate->toDateString(),
            'status'        => 'unpaid',
            'created_at'    => $createdAt,
            'updated_at'    => $createdAt,
        ], $students);

        DB::table('spp_invoices')->insert($rows);

        return count($rows);
    }

    /**
     * Ambil semua invoice beserta relasi student.
     */
    public function getAll()
    {
        return SppInvoice::with(['student'])->latest()->get();
    }

    /**
     * Ambil semua invoice milik siswa tertentu.
     */
    public function getByStudentId(int $studentId)
    {
        return SppInvoice::with(['student'])
            ->where('student_id', $studentId)
            ->latest()
            ->get();
    }

    /**
     * Admin update status invoice secara manual (dipakai untuk pembayaran cash).
     */
    public function updateStatus(int $invoiceId, string $status): SppInvoice
    {
        $invoice = SppInvoice::findOrFail($invoiceId);
        $invoice->update(['status' => $status]);

        return $invoice;
    }

    /**
     * Tandai semua invoice yang melewati jatuh_tempo sebagai overdue.
     * Menggunakan single bulk query — tidak ada loop.
     */
    public function checkOverdue(): int
    {
        return SppInvoice::where('status', 'unpaid')
            ->where('jatuh_tempo', '<', Carbon::today()->toDateString())
            ->update(['status' => 'overdue']);
    }
}
