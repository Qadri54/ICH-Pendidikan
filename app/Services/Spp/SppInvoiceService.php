<?php

namespace App\Services\Spp;

use App\Models\SppInvoice;
use App\Models\Student;
use App\Notifications\SppOverdueNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SppInvoiceService
{
    /**
     * Nominal SPP bulanan (IDR).
     * Sesuaikan dengan tarif yang berlaku di IQRA' Creative House.
     */
    public const MONTHLY_FEE = 300000;

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

        $students = Student::aktif()
            ->whereNotIn('student_id', $existingIds)
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

        return \count($rows);
    }

    /**
     * Ambil semua invoice beserta relasi student.
     */
    public function getAll()
    {
        return SppInvoice::with(['student'])->latest()->get();
    }

    /**
     * Ambil invoice dengan filter search/status + pagination (untuk halaman admin).
     */
    public function getPaginated(?string $search, ?string $status, int $perPage = 15)
    {
        return SppInvoice::with('student.classRoom')
            ->when($search, fn($q) =>
                $q->whereHas('student', fn($s) => $s->where('nama_siswa', 'like', "%{$search}%"))
            )
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest('jatuh_tempo')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Ringkasan keuangan: total tagihan belum lunas + jumlah yang sudah lunas.
     */
    public function getSummary(): array
    {
        return [
            'total_tagihan'    => SppInvoice::where('status', '!=', 'paid')->sum('jumlah'),
            'tagihan_berjalan' => SppInvoice::where('status', 'unpaid')->count(),
            'total_lunas'      => SppInvoice::where('status', 'paid')->count(),
        ];
    }

    public function createSingle(array $data): SppInvoice
    {
        return SppInvoice::create([
            'student_id'    => $data['student_id'],
            'tanggal_tahun' => $data['tanggal_tahun'],
            'jumlah'        => $data['jumlah'],
            'jatuh_tempo'   => $data['jatuh_tempo'],
            'status'        => $data['status'] ?? 'unpaid',
        ]);
    }

    /**
     * Hapus invoice beserta data payment-nya.
     */
    public function delete(int $invoiceId): void
    {
        SppInvoice::findOrFail($invoiceId)->delete();
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
     * Tandai semua invoice yang melewati jatuh_tempo sebagai overdue
     * dan kirim notifikasi ke Orang Tua siswa yang bersangkutan.
     */
    public function checkOverdue(): int
    {
        $invoices = SppInvoice::with('student.user')
            ->where('status', 'unpaid')
            ->where('jatuh_tempo', '<', Carbon::today()->toDateString())
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->update(['status' => 'overdue']);

            if ($invoice->student?->user) {
                $invoice->student->user->notify(new SppOverdueNotification($invoice));
            }
        }

        return $invoices->count();
    }
}
