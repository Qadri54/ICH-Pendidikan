<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\RegistrationFee;
use App\Models\SppInvoice;
use App\Services\Registration\RegistrationFeeService;
use App\Services\Registration\RegistrationTransactionService;
use App\Services\Spp\SppInvoiceService;
use App\Services\Spp\SppPaymentService;
use App\Services\User\StudentProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeuanganController extends Controller
{
    public function __construct(
        private StudentProfileService $studentProfileService,
        private RegistrationFeeService $registrationFeeService,
        private RegistrationTransactionService $registrationTransactionService,
        private SppInvoiceService $sppInvoiceService,
        private SppPaymentService $sppPaymentService,
    ) {}

    public function index(): View
    {
        $students = $this->studentProfileService->getAllByUserId(auth()->id());

        $summary = $students->map(function ($student) {
            $fee         = $this->registrationFeeService->findByStudentId($student->student_id);
            $sppCount    = $this->sppInvoiceService
                ->getByStudentId($student->student_id)
                ->filter(fn($inv) => in_array($inv->status, ['unpaid', 'overdue', 'pending']))
                ->count();

            return compact('student', 'fee', 'sppCount');
        });

        return view('orang-tua.keuangan.index', compact('summary'));
    }

    public function pendaftaran(): View
    {
        $students = $this->studentProfileService->getAllByUserId(auth()->id());

        $data = $students->map(function ($student) {
            $fee = $this->registrationFeeService->findByStudentId($student->student_id);

            $pendingTx = $fee
                ? $this->registrationTransactionService
                    ->getByRegistrationFeeId($fee->registration_fee_id)
                    ->firstWhere('status', 'pending')
                : null;

            return compact('student', 'fee', 'pendingTx');
        });

        return view('orang-tua.keuangan.pendaftaran', compact('data'));
    }

    public function spp(): View
    {
        $students = $this->studentProfileService->getAllByUserId(auth()->id());

        $data = $students->map(function ($student) {
            $invoices = $this->sppInvoiceService
                ->getByStudentId($student->student_id)
                ->filter(fn($inv) => in_array($inv->status, ['unpaid', 'overdue', 'pending']));

            return compact('student', 'invoices');
        });

        return view('orang-tua.keuangan.spp', compact('data'));
    }

    public function storeRegistrationPayment(Request $request, RegistrationFee $fee): RedirectResponse
    {
        abort_if($fee->student->user_id !== auth()->id(), 403);

        $remaining = $this->registrationFeeService->getRemainingAmount($fee->registration_fee_id);

        $data = $request->validate([
            'jumlah_bayar'            => "required|integer|min:1|max:{$remaining}",
            'nama_bank'               => 'required|string|max:100',
            'gambar_bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'payment_category'        => 'required|in:full,installment',
        ]);

        $path = $request->file('gambar_bukti_pembayaran')
            ->store('bukti-pembayaran', 'public');

        $this->registrationTransactionService->upload([
            'registration_fee_id'     => $fee->registration_fee_id,
            'payment_date'            => now(),
            'jumlah_bayar'            => $data['jumlah_bayar'],
            'nama_bank'               => $data['nama_bank'],
            'gambar_bukti_pembayaran' => $path,
            'payment_category'        => $data['payment_category'],
        ]);

        return redirect()->route('pembayaran.pendaftaran.index')
            ->with('success', 'Bukti pembayaran pendaftaran berhasil dikirim, menunggu konfirmasi admin.');
    }

    public function storeSppPayment(Request $request, SppInvoice $invoice): RedirectResponse
    {
        abort_if($invoice->student->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'jumlah_bayar'            => 'required|integer|min:1',
            'nama_bank'               => 'required|string|max:100',
            'gambar_bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('gambar_bukti_pembayaran')
            ->store('bukti-pembayaran', 'public');

        $this->sppPaymentService->upload([
            'student_id'              => $invoice->student_id,
            'invoice_id'              => $invoice->invoice_id,
            'payment_date'            => now(),
            'jumlah_bayar'            => $data['jumlah_bayar'],
            'nama_bank'               => $data['nama_bank'],
            'gambar_bukti_pembayaran' => $path,
        ]);

        return redirect()->route('pembayaran.spp.index')
            ->with('success', 'Bukti pembayaran SPP berhasil dikirim, menunggu konfirmasi admin.');
    }
}
