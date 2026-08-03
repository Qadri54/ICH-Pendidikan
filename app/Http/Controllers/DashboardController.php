<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use App\Models\ClassRoom;
use App\Models\Registration;
use App\Models\RegistrationTransaction;
use App\Models\SavingLedger;
use App\Models\SppInvoice;
use App\Models\Student;
use App\Models\StudentReportCard;
use App\Models\Teacher;
use App\Services\Registration\RegistrationFeeService;
use App\Services\Spp\SppInvoiceService;
use Carbon\Carbon;

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

        if ($role === 'Orang Tua') {
            return redirect()->route('beranda');
        }

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
            'total_siswa'       => Student::aktif()->count(),
            'total_guru'        => Teacher::count(),
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

        $currentYear = now()->year;
        $monthlyIncome = collect(range(1, now()->month))->map(function ($m) use ($currentYear) {
            $spp = SppInvoice::where('status', 'paid')
                ->whereYear('tanggal_tahun', $currentYear)
                ->whereMonth('tanggal_tahun', $m)
                ->sum('jumlah');

            $pendaftaran = RegistrationTransaction::where('status', 'approved')
                ->whereYear('payment_date', $currentYear)
                ->whereMonth('payment_date', $m)
                ->sum('jumlah_bayar');

            return [
                'label'       => Carbon::create($currentYear, $m)->translatedFormat('M'),
                'spp'         => (int) $spp,
                'pendaftaran' => (int) $pendaftaran,
            ];
        });

        $executive = $this->getExecutiveMetrics();
        $classes = ClassRoom::orderBy('nama_kelas')->get();
        $periods = AcademicPeriod::orderByDesc('tanggal_mulai')->get();

        return view('admin.dashboard', compact('user', 'role', 'stats', 'recentPayments', 'monthlyIncome', 'currentYear', 'executive', 'classes', 'periods'));
    }

    private function getExecutiveMetrics(): ?array
    {
        $period = AcademicPeriod::where('is_active', true)->first()
            ?? AcademicPeriod::latest('tanggal_mulai')->first();

        if (!$period) {
            return null;
        }

        $start = $period->tanggal_mulai;
        $end   = $period->tanggal_selesai;

        // Collection Rate SPP
        $sppInvoiced  = (int) SppInvoice::whereBetween('tanggal_tahun', [$start, $end])->sum('jumlah');
        $sppCollected = (int) SppInvoice::where('status', 'paid')->whereBetween('tanggal_tahun', [$start, $end])->sum('jumlah');
        $collectionRate = $sppInvoiced > 0 ? round(($sppCollected / $sppInvoiced) * 100, 1) : 0;

        // Revenue semester + comparison
        $regCollected = (int) RegistrationTransaction::where('status', 'approved')
            ->whereBetween('payment_date', [$start, $end])->sum('jumlah_bayar');
        $revenue = $sppCollected + $regCollected;

        $prevPeriod = AcademicPeriod::where('tanggal_mulai', '<', $start)
            ->orderByDesc('tanggal_mulai')->first();
        $prevRevenue = 0;
        if ($prevPeriod) {
            $prevRevenue = (int) SppInvoice::where('status', 'paid')
                    ->whereBetween('tanggal_tahun', [$prevPeriod->tanggal_mulai, $prevPeriod->tanggal_selesai])
                    ->sum('jumlah')
                + (int) RegistrationTransaction::where('status', 'approved')
                    ->whereBetween('payment_date', [$prevPeriod->tanggal_mulai, $prevPeriod->tanggal_selesai])
                    ->sum('jumlah_bayar');
        }
        $revenueDelta = $prevRevenue > 0
            ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : null;

        // Churn Rate
        $siswaKeluar = Student::where('status', 'keluar')
            ->whereBetween('updated_at', [$start, $end])->count();
        $totalAtStart = Student::whereIn('status', ['aktif', 'keluar', 'alumni'])
            ->where('created_at', '<', $start)->count();
        $churnRate = $totalAtStart > 0 ? round(($siswaKeluar / $totalAtStart) * 100, 1) : 0;

        // PPDB Conversion
        $totalRegistrations = Registration::whereBetween('created_at', [$start, $end])->count();
        $totalAccepted = Registration::where('status', 'accepted')
            ->whereBetween('created_at', [$start, $end])->count();
        $conversionRate = $totalRegistrations > 0
            ? round(($totalAccepted / $totalRegistrations) * 100, 1)
            : 0;

        // Rasio Guru:Siswa
        $totalGuruAktif  = Teacher::whereHas('user', fn ($q) => $q->where('status', 'active'))->count();
        $totalSiswaAktif = Student::where('status', 'aktif')->count();
        $rasio = $totalGuruAktif > 0 ? round($totalSiswaAktif / $totalGuruAktif) : 0;

        // Aging Analysis (compact)
        $unpaidInvoices = SppInvoice::whereIn('status', ['unpaid', 'overdue'])->get();
        $aging = [
            ['label' => '≤ 1 bln', 'count' => 0, 'amount' => 0],
            ['label' => '2-3 bln', 'count' => 0, 'amount' => 0],
            ['label' => '4-6 bln', 'count' => 0, 'amount' => 0],
            ['label' => '> 6 bln', 'count' => 0, 'amount' => 0],
        ];
        foreach ($unpaidInvoices as $inv) {
            $months = $inv->jatuh_tempo ? max(0, (int) now()->diffInMonths($inv->jatuh_tempo, false)) : 0;
            $idx = match (true) {
                $months <= 1 => 0,
                $months <= 3 => 1,
                $months <= 6 => 2,
                default      => 3,
            };
            $aging[$idx]['count']++;
            $aging[$idx]['amount'] += $inv->jumlah;
        }
        $totalOutstanding = (int) $unpaidInvoices->sum('jumlah');

        return [
            'period_label'    => $period->tahun_ajaran . ' - Semester ' . $period->semester,
            'collection_rate' => $collectionRate,
            'revenue'         => $revenue,
            'prev_revenue'    => $prevRevenue,
            'revenue_delta'   => $revenueDelta,
            'churn_rate'      => $churnRate,
            'siswa_keluar'    => $siswaKeluar,
            'conversion_rate' => $conversionRate,
            'total_registrations' => $totalRegistrations,
            'total_accepted'  => $totalAccepted,
            'rasio'           => $rasio,
            'guru_aktif'      => $totalGuruAktif,
            'siswa_aktif'     => $totalSiswaAktif,
            'aging'           => $aging,
            'total_outstanding' => $totalOutstanding,
        ];
    }
}
