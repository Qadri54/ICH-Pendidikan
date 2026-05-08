<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationTransaction;
use App\Services\Registration\RegistrationTransactionService;
use Illuminate\Http\Request;

class PembayaranPendaftaranController extends Controller
{
    public function __construct(
        private RegistrationTransactionService $transactionService,
    ) {}

    public function index(Request $request)
    {
        $transaksi = $this->transactionService->getPaginated(
            $request->search,
            $request->status,
        );

        return view('admin.pembayaran-pendaftaran.index', compact('transaksi'));
    }

    public function approve(RegistrationTransaction $transaksi)
    {
        $this->transactionService->approve(
            $transaksi->registration_transaction_id,
            auth()->id(),
        );

        return redirect()->route('admin.pembayaran-pendaftaran.index')
            ->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function reject(Request $request, RegistrationTransaction $transaksi)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $this->transactionService->reject(
            $transaksi->registration_transaction_id,
            $request->rejection_reason,
        );

        return redirect()->route('admin.pembayaran-pendaftaran.index')
            ->with('success', 'Pembayaran berhasil ditolak.');
    }
}
