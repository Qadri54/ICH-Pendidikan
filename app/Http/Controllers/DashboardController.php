<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\ReligiousTeacher;
use App\Models\SavingLedger;
use App\Models\SppInvoice;
use App\Models\Student;
use App\Models\StudentReportCard;
use App\Models\Teacher;
use App\Services\Registration\RegistrationFeeService;
use App\Services\Spp\SppInvoiceService;

class DashboardController extends Controller
{
    public function __construct(
        private SppInvoiceService $invoiceService,
        private RegistrationFeeService $registrationFeeService,
    ) {}

    public function index()
    {
        $user = auth()->user();
        $role = $user->role?->role_name ?? '';

        if (in_array($role, ['Guru', 'Guru Ngaji'])) {
            return view('guru.dashboard', compact('user'));
        }

        if (in_array($role, ['Admin', 'Kepala Sekolah', 'Kepala Yayasan'])) {
            return $this->adminDashboard($user, $role);
        }

        return view('dashboard', compact('user', 'role'));
    }

    private function adminDashboard($user, string $role)
    {
        $sppSummary       = $this->invoiceService->getSummary();
        $totalSpp         = SppInvoice::where('status', 'paid')->sum('jumlah');
        $totalPendaftaran = $this->registrationFeeService->getTotalCollected();

        $stats = [
            'total_siswa'       => Student::count(),
            'total_guru'        => Teacher::count() + ReligiousTeacher::count(),
            'total_pendapatan'  => $totalSpp + $totalPendaftaran,
            'tagihan_berjalan'  => $sppSummary['tagihan_berjalan'],
            'tagihan_lunas'     => $sppSummary['total_lunas'],
            'total_tabungan'    => SavingLedger::sum('total_balance'),
            'pending_daftar'    => Registration::where('status', 'pending')->count(),
            'pending_raport'    => StudentReportCard::where('status', 'submitted')->count(),
        ];

        $recentPayments = SppInvoice::with(['student.classRoom'])
            ->where('status', 'paid')
            ->latest('updated_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('user', 'role', 'stats', 'recentPayments'));
    }
}
