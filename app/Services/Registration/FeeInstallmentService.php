<?php

namespace App\Services\Registration;

use App\Models\FeeInstallment;
use Illuminate\Support\Carbon;

class FeeInstallmentService {
    public function createInstallments(
        int $registrationFeeId,
        float $totalAmount,
        Carbon $approvalDate
    ): void {
        $installmentAmount = $totalAmount / 3;

        $installments = [];

        for ($i = 1; $i <= 3; $i++) {
            $installments[] = [
                'registration_fee_id' => $registrationFeeId,
                'jumlah' => $installmentAmount,
                'tanggal_jatuh_tempo' => $approvalDate->copy()->addMonths($i),
                'status' => 'unpaid',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        FeeInstallment::insert($installments);
    }

    public function getByRegistrationFeeId(int $registrationFeeId) {
        return FeeInstallment::where('registration_fee_id', $registrationFeeId)->get();
    }

    public function payInstallment(int $installmentId): bool {
        return FeeInstallment::where('installment_id', $installmentId)
            ->where('status', 'unpaid')
            ->update(['status' => 'paid']);
    }

    public function payAllInstallments(int $registrationFeeId): bool {
        return FeeInstallment::where('registration_fee_id', $registrationFeeId)
            ->where('status', '!=', 'paid')
            ->update(['status' => 'paid']);
    }

    public function updateDueDate(int $installmentId, Carbon $newDueDate): bool {
        return FeeInstallment::where('installment_id', $installmentId)
            ->update(['tanggal_jatuh_tempo' => $newDueDate]);
    }

    public function checkOverdue(): void {
        FeeInstallment::where('status', 'unpaid')
            ->where('tanggal_jatuh_tempo', '<', now())
            ->update(['status' => 'overdue']);
    }
} 
