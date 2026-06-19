<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SppInvoice;
use App\Models\SppPayment;
use App\Models\Student;
use App\Services\Spp\SppInvoiceService;
use App\Services\Spp\SppPaymentService;
use Illuminate\Http\RedirectResponse;
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
        $siswa   = Student::aktif()->with('classRoom')->orderBy('nama_siswa')->get();

        return view('admin.keuangan.index', [
            'invoices'      => $invoices,
            'totalTagihan'  => $summary['total_tagihan'],
            'totalLunas'    => $summary['total_lunas'],
            'siswa'         => $siswa,
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

    public function create()
    {
        $siswa = Student::aktif()->with('classRoom')->orderBy('nama_siswa')->get();

        return view('admin.keuangan.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id'    => 'required|exists:students,student_id',
            'tanggal_tahun' => 'required|date',
            'jumlah'        => 'required|integer|min:0',
            'jatuh_tempo'   => 'required|date|after_or_equal:tanggal_tahun',
            'status'        => 'required|in:unpaid,paid',
        ]);

        $this->invoiceService->createSingle($data);

        return redirect()->route('admin.keuangan.index')
            ->with('success', 'Tagihan SPP berhasil dibuat.');
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

    public function buktiPembayaran(Request $request)
    {
        auth()->user()->notifications()
            ->where('type', \App\Notifications\SppPaymentUploadedNotification::class)
            ->delete();

        $payments = $this->paymentService->getPaginated(
            $request->search,
            $request->status,
        );

        $pendingCount = SppPayment::where('status', 'pending')->count();

        return view('admin.keuangan.bukti-pembayaran', compact('payments', 'pendingCount'));
    }

    public function approvePayment(SppPayment $payment): RedirectResponse
    {
        $this->paymentService->approve($payment->payment_id, auth()->id());

        return redirect()->route('admin.keuangan.bukti-pembayaran')
            ->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function rejectPayment(SppPayment $payment): RedirectResponse
    {
        $this->paymentService->cancel($payment->payment_id);

        return redirect()->route('admin.keuangan.bukti-pembayaran')
            ->with('success', 'Pembayaran berhasil ditolak.');
    }
}
