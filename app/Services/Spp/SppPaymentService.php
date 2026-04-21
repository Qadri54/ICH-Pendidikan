<?php

namespace App\Services\Spp;

use App\Models\SppInvoice;
use App\Models\SppPayment;
use Illuminate\Support\Facades\DB;

class SppPaymentService
{
    /**
     * Upload bukti transfer — buat record pembayaran baru dengan status pending.
     * Hanya digunakan untuk pembayaran transfer (bukan cash).
     */
    public function upload(array $data): SppPayment
    {
        return SppPayment::create([
            'student_id'               => $data['student_id'],
            'invoice_id'               => $data['invoice_id'],
            'payment_date'             => $data['payment_date'],
            'jumlah_bayar'             => $data['jumlah_bayar'],
            'nama_bank'                => $data['nama_bank'],
            'gambar_bukti_pembayaran'  => $data['gambar_bukti_pembayaran'],
            'approved_by'              => null,
            'status'                   => 'pending',
        ]);
    }

    /**
     * Admin approve pembayaran transfer.
     * Menggunakan DB transaction karena menyentuh 2 tabel sekaligus.
     */
    public function approve(int $paymentId, int $approvedBy): SppPayment
    {
        return DB::transaction(function () use ($paymentId, $approvedBy) {
            $payment = SppPayment::findOrFail($paymentId);

            // 1. Update spp_payments
            $payment->update([
                'status'      => 'paid',
                'approved_by' => $approvedBy,
            ]);

            // 2. Update spp_invoices
            SppInvoice::where('invoice_id', $payment->invoice_id)
                ->update(['status' => 'paid']);

            return $payment;
        });
    }

    /**
     * Admin tolak bukti pembayaran yang tidak valid.
     */
    public function cancel(int $paymentId): SppPayment
    {
        $payment = SppPayment::findOrFail($paymentId);
        $payment->update(['status' => 'cancelled']);

        return $payment;
    }

    /**
     * Ambil semua pembayaran berdasarkan invoice_id.
     */
    public function getByInvoiceId(int $invoiceId)
    {
        return SppPayment::with(['student', 'invoice'])
            ->where('invoice_id', $invoiceId)
            ->latest()
            ->get();
    }
}
