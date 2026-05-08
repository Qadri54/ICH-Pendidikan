<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SppInvoice;
use App\Services\Spp\SppInvoiceService;
use App\Services\Spp\SppPaymentService;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function __construct(
        private SppInvoiceService $invoiceService,
        private SppPaymentService $paymentService,
    ) {}

    public function index(Request $request)
    {
        $invoices = $this->invoiceService->getPaginated(
            $request->search,
            $request->status,
        );

        $summary = $this->invoiceService->getSummary();

        return view('admin.keuangan.index', [
            'invoices'      => $invoices,
            'totalTagihan'  => $summary['total_tagihan'],
            'totalLunas'    => $summary['total_lunas'],
        ]);
    }

    public function generate()
    {
        $count = $this->invoiceService->generateMonthlyInvoices();

        $message = $count > 0
            ? "{$count} tagihan SPP bulan ini berhasil digenerate."
            : "Semua siswa sudah punya tagihan untuk bulan ini.";

        return redirect()->route('admin.keuangan.index')->with('success', $message);
    }

    public function edit(SppInvoice $keuangan)
    {
        $keuangan->load('student', 'payments');
        return view('admin.keuangan.edit', compact('keuangan'));
    }

    public function update(Request $request, SppInvoice $keuangan)
    {
        $data = $request->validate([
            'status' => 'required|in:unpaid,paid,overdue',
        ]);

        $this->invoiceService->updateStatus($keuangan->invoice_id, $data['status']);

        return redirect()->route('admin.keuangan.index')
            ->with('success', "Status tagihan berhasil diperbarui.");
    }

    public function destroy(SppInvoice $keuangan)
    {
        $this->invoiceService->delete($keuangan->invoice_id);

        return redirect()->route('admin.keuangan.index')
            ->with('success', "Tagihan berhasil dihapus.");
    }
}
