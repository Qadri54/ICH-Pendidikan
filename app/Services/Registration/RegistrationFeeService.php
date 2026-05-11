<?php

namespace App\Services\Registration;

use App\Models\RegistrationFee;
use App\Models\RegistrationTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RegistrationFeeService {
    const TOTAL_FEE = 3000000;

    public function __construct(
        private FeeInstallmentService $feeInstallmentService,
    ) {
    }

    public function createFee(int $studentId): RegistrationFee {
        return DB::transaction(function () use ($studentId) {
            $fee = RegistrationFee::create([
                'student_id' => $studentId,
                'total_jumlah' => self::TOTAL_FEE,
                'status' => 'unpaid',
            ]);

            $this->feeInstallmentService->createInstallments(
                $fee->registration_fee_id,
                self::TOTAL_FEE,
                Carbon::now(),
            );

            return $fee;
        });
    }

    public function getByStudentId(int $studentId): RegistrationFee {
        return RegistrationFee::with('installments')
            ->where('student_id', $studentId)
            ->firstOrFail();
    }

    public function findByStudentId(int $studentId): ?RegistrationFee {
        return RegistrationFee::with('transactions')->where('student_id', $studentId)->first();
    }

    public function getRemainingAmount(int $feeId): int {
        $fee  = RegistrationFee::findOrFail($feeId);
        $paid = RegistrationTransaction::where('registration_fee_id', $feeId)
            ->where('status', 'approved')
            ->sum('jumlah_bayar');

        return max(0, $fee->total_jumlah - $paid);
    }

    public function getTotalCollected(): int {
        return RegistrationTransaction::where('status', 'approved')->sum('jumlah_bayar');
    }

    public function getPaginated(?string $search, ?string $status, int $perPage = 15)
    {
        return \App\Models\RegistrationFee::with([
                'student',
                'transactions' => fn($q) => $q->latest(),
            ])
            ->when($search, fn($q) =>
                $q->whereHas('student', fn($s) =>
                    $s->where('nama_siswa', 'like', "%{$search}%")
                )
            )
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getPaidFees() {
        return RegistrationFee::with('student.classRoom')
            ->where('status', 'paid')
            ->latest()
            ->get();
    }

    public function updateStatus(int $registrationFeeId, string $status): bool {
        $validStatuses = ['unpaid', 'installments', 'paid'];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Status tidak valid: {$status}");
        }

        return RegistrationFee::where('registration_fee_id', $registrationFeeId)
            ->update(['status' => $status]);
    }
}
