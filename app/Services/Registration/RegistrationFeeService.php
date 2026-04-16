<?php

namespace App\Services\Registration;

use App\Models\RegistrationFee;
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

    public function updateStatus(int $registrationFeeId, string $status): bool {
        $validStatuses = ['unpaid', 'installments', 'paid'];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Status tidak valid: {$status}");
        }

        return RegistrationFee::where('registration_fee_id', $registrationFeeId)
            ->update(['status' => $status]);
    }
}
