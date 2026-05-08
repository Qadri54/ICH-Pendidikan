<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReligiousTeacher;
use App\Models\SavingLedger;
use App\Models\SppInvoice;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\Registration\RegistrationFeeService;
use App\Services\Spp\SppInvoiceService;

class LaporanController extends Controller
{
    public function __construct(
        private SppInvoiceService $invoiceService,
        private RegistrationFeeService $registrationFeeService,
    ) {}

    public function index()
    {
        $summary = $this->invoiceService->getSummary();

        $totalSpp          = SppInvoice::where('status', 'paid')->sum('jumlah');
        $totalPendaftaran  = $this->registrationFeeService->getTotalCollected();

        $stats = [
            'total_siswa'            => Student::count(),
            'total_guru'             => Teacher::count() + ReligiousTeacher::count(),
            'tagihan_berjalan'       => $summary['tagihan_berjalan'],
            'tagihan_lunas'          => $summary['total_lunas'],
            'total_tagihan'          => $summary['total_tagihan'],
            'total_spp_terkumpul'    => $totalSpp,
            'total_pendaftaran'      => $totalPendaftaran,
            'total_pendapatan'       => $totalSpp + $totalPendaftaran,
            'total_tabungan'         => SavingLedger::sum('total_balance'),
        ];

        $pembayaranSpp = SppInvoice::with(['student.classRoom'])
            ->where('status', 'paid')
            ->latest('updated_at')
            ->limit(50)
            ->get();

        $lunasPendaftaran = $this->registrationFeeService->getPaidFees();

        return view('admin.laporan.index', compact('stats', 'pembayaranSpp', 'lunasPendaftaran'));
    }
}
