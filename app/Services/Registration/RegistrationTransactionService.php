<?php

namespace App\Services\Registration;

use App\Models\RegistrationTransaction;

class RegistrationTransactionService
{
    public function upload(array $data): RegistrationTransaction
    {
        return RegistrationTransaction::create([
            'registration_fee_id'     => $data['registration_fee_id'],
            'approved_by'             => $data['approved_by'],
            'payment_date'            => $data['payment_date'],
            'jumlah_bayar'            => $data['jumlah_bayar'],
            'payment_method'          => $data['payment_method'],
            'nama_bank'               => $data['nama_bank'] ?? null,
            'gambar_bukti_pembayaran' => $data['gambar_bukti_pembayaran'],
            'payment_category'        => $data['payment_category'],
            'status'                  => 'pending',
        ]);
    }

    public function approve(int $transactionId): bool
    {
        return RegistrationTransaction::where('registration_transaction_id', $transactionId)
            ->where('status', 'pending')
            ->update(['status' => 'approved']);
    }

    public function reject(int $transactionId): bool
    {
        return RegistrationTransaction::where('registration_transaction_id', $transactionId)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);
    }

    public function getByRegistrationFeeId(int $registrationFeeId)
    {
        return RegistrationTransaction::where('registration_fee_id', $registrationFeeId)
            ->get();
    }
}
