<?php

namespace App\Services\Registration;

use App\Models\Admin;
use App\Models\RegistrationTransaction;
use App\Models\User;
use App\Notifications\RegistrationPaymentUploadedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class RegistrationTransactionService
{
    public function __construct(
        private RegistrationFeeService $registrationFeeService,
    ) {}

    public function upload(array $data): RegistrationTransaction
    {
        $transaction = RegistrationTransaction::create([
            'registration_fee_id'     => $data['registration_fee_id'],
            'payment_date'            => $data['payment_date'],
            'jumlah_bayar'            => $data['jumlah_bayar'],
            'nama_bank'               => $data['nama_bank'] ?? null,
            'gambar_bukti_pembayaran' => $data['gambar_bukti_pembayaran'],
            'payment_category'        => $data['payment_category'],
            'status'                  => 'pending',
        ]);

        $transaction->load('registrationFee.student');
        $admins = User::whereHas('role', fn ($q) => $q->where('role_name', 'Admin'))->get();
        Notification::send($admins, new RegistrationPaymentUploadedNotification($transaction));

        return $transaction;
    }

    public function approve(int $transactionId, int $approvedByUserId): bool
    {
        return DB::transaction(function () use ($transactionId, $approvedByUserId) {
            $transaction = RegistrationTransaction::with('registrationFee')
                ->where('registration_transaction_id', $transactionId)
                ->where('status', 'pending')
                ->firstOrFail();

            $adminId = Admin::where('user_id', $approvedByUserId)->value('admin_id');

            $transaction->update([
                'status'      => 'approved',
                'approved_by' => $adminId,
            ]);

            // Hitung total yang sudah dibayar (approved) untuk fee ini
            $fee        = $transaction->registrationFee;
            $totalPaid  = RegistrationTransaction::where('registration_fee_id', $fee->registration_fee_id)
                ->where('status', 'approved')
                ->sum('jumlah_bayar');

            $feeStatus = $totalPaid >= $fee->total_jumlah ? 'paid' : 'installments';
            $this->registrationFeeService->updateStatus($fee->registration_fee_id, $feeStatus);

            return true;
        });
    }

    public function reject(int $transactionId, ?string $reason = null): bool
    {
        return RegistrationTransaction::where('registration_transaction_id', $transactionId)
            ->where('status', 'pending')
            ->update([
                'status'           => 'rejected',
                'rejection_reason' => $reason,
            ]);
    }

    public function getPaginated(?string $search, ?string $status, int $perPage = 15)
    {
        return RegistrationTransaction::with(['registrationFee.student', 'registrationFee.transactions'])
            ->when($search, fn($q) =>
                $q->whereHas('registrationFee.student', fn($s) =>
                    $s->where('nama_siswa', 'like', "%{$search}%")
                )
            )
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByRegistrationFeeId(int $registrationFeeId)
    {
        return RegistrationTransaction::where('registration_fee_id', $registrationFeeId)->get();
    }
}
