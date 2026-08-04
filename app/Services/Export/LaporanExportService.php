<?php

namespace App\Services\Export;

use App\Models\AcademicPeriod;
use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Registration;
use App\Models\RegistrationFee;
use App\Models\RegistrationTransaction;
use App\Models\Role;
use App\Models\SavingLedger;
use App\Models\SppInvoice;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentPassbook;
use App\Models\Teacher;
use App\Models\User;
use App\Services\Attendance\AttendanceService;
use App\Services\Attendance\StudentAttendanceService;
use App\Services\Registration\RegistrationFeeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanExportService
{
    public function __construct(
        private StudentAttendanceService $studentAttendanceService,
        private AttendanceService $attendanceService,
        private RegistrationFeeService $registrationFeeService,
    ) {}

    public function getMonthlySummary(int $year): array
    {
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $spp = SppInvoice::where('status', 'paid')
                ->whereYear('tanggal_tahun', $year)
                ->whereMonth('tanggal_tahun', $m)
                ->sum('jumlah');

            $pendaftaran = RegistrationTransaction::where('status', 'approved')
                ->whereYear('payment_date', $year)
                ->whereMonth('payment_date', $m)
                ->sum('jumlah_bayar');

            $months[] = [
                'month'       => $m,
                'label'       => Carbon::create($year, $m)->translatedFormat('F'),
                'spp'         => (int) $spp,
                'pendaftaran' => (int) $pendaftaran,
            ];
        }
        return $months;
    }

    private function getKepalaSekolahName(): string
    {
        return User::whereHas('role', fn ($q) => $q->where('role_name', 'Kepala Sekolah'))
            ->value('name') ?? 'Kepala Sekolah';
    }

    // ─── PDF ─────────────────────────────────────────

    public function exportKeuanganPdf(?int $year = null): Response
    {
        $year             = $year ?? now()->year;
        $totalSpp         = (int) SppInvoice::where('status', 'paid')->sum('jumlah');
        $totalPendaftaran = (int) $this->registrationFeeService->getTotalCollected();
        $totalTabungan    = (int) SavingLedger::sum('total_balance');
        $monthlySummary   = $this->getMonthlySummary($year);

        $sppInvoiced = (int) SppInvoice::whereYear('tanggal_tahun', $year)->sum('jumlah');
        $sppCollected = (int) SppInvoice::where('status', 'paid')->whereYear('tanggal_tahun', $year)->sum('jumlah');
        $collectionRate = $sppInvoiced > 0 ? round(($sppCollected / $sppInvoiced) * 100, 1) : 0;

        $totalOutstanding = (int) SppInvoice::whereIn('status', ['unpaid', 'overdue'])->sum('jumlah');
        $totalPendapatan = $totalSpp + $totalPendaftaran;
        $maxMonthly = max(array_column($monthlySummary, 'spp') ?: [1]);

        $prevYear = $year - 1;
        $prevSpp = (int) SppInvoice::where('status', 'paid')->whereYear('tanggal_tahun', $prevYear)->sum('jumlah');
        $prevPendaftaran = (int) RegistrationTransaction::where('status', 'approved')
            ->whereYear('payment_date', $prevYear)->sum('jumlah_bayar');
        $prevTotal = $prevSpp + $prevPendaftaran;
        $yearDelta = $prevTotal > 0 ? round((($totalPendapatan - $prevTotal) / $prevTotal) * 100, 1) : null;

        $kepalaSekolah = $this->getKepalaSekolahName();

        $pdf = Pdf::loadView('exports.keuangan-pdf', compact(
            'year', 'totalSpp', 'totalPendaftaran', 'totalTabungan', 'totalPendapatan',
            'collectionRate', 'totalOutstanding', 'monthlySummary', 'maxMonthly',
            'prevYear', 'prevTotal', 'yearDelta', 'kepalaSekolah'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-keuangan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportAbsensiSiswaPdf(int $classId, int $year, int $month): Response
    {
        $classroom     = ClassRoom::with('homeroomTeacher.user')->findOrFail($classId);
        $recap         = $this->studentAttendanceService->getMonthlyRecap($classId, $year, $month);
        $waliKelas     = $classroom->homeroomTeacher?->user?->name;
        $kepalaSekolah = $this->getKepalaSekolahName();

        $hariEfektif = StudentAttendance::whereHas('student', fn ($q) => $q->where('class_id', $classId))
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DATE(created_at) as tgl')
            ->distinct()
            ->count('tgl');

        $pdf = Pdf::loadView('exports.absensi-siswa-pdf', compact(
            'classroom', 'recap', 'year', 'month', 'waliKelas', 'kepalaSekolah', 'hariEfektif'
        ));

        return $pdf->download("rekap-absensi-siswa-{$classroom->nama_kelas}-{$year}-{$month}.pdf");
    }

    public function exportAbsensiGuruPdf(int $year, int $month): Response
    {
        $recap         = $this->attendanceService->getMonthlyRecap($year, $month);
        $kepalaSekolah = $this->getKepalaSekolahName();

        $hariEfektif = AttendanceRecord::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DATE(created_at) as tgl')
            ->distinct()
            ->count('tgl');

        $pdf = Pdf::loadView('exports.absensi-guru-pdf', compact('recap', 'year', 'month', 'kepalaSekolah', 'hariEfektif'));

        return $pdf->download("rekap-absensi-guru-{$year}-{$month}.pdf");
    }

    public function exportDataSiswaPdf(): Response
    {
        $totalAktif  = Student::where('status', 'aktif')->count();
        $totalAlumni = Student::where('status', 'alumni')->count();
        $totalKeluar = Student::where('status', 'keluar')->count();

        $tanpaAkun = Student::where('status', 'aktif')
            ->whereNull('user_id')
            ->count();

        $totalL = Student::where('status', 'aktif')->where('jenis_kelamin', 'L')->count();
        $totalP = Student::where('status', 'aktif')->where('jenis_kelamin', 'P')->count();

        $totalAll = $totalAktif + $totalAlumni + $totalKeluar;
        $retentionRate = $totalAll > 0 ? round(($totalAktif / $totalAll) * 100, 1) : 0;

        $studentsGrouped = Student::with('classRoom')
            ->where('status', 'aktif')
            ->get()
            ->groupBy(fn ($s) => $s->classRoom?->nama_kelas ?? 'Belum Ada Kelas');

        $growthData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Student::where('status', 'aktif')
                ->where('created_at', '<=', $date->endOfMonth())
                ->count();
            $growthData[] = [
                'label'   => $date->translatedFormat('M Y'),
                'value'   => $count,
            ];
        }
        $maxGrowth = max(array_column($growthData, 'value') ?: [1]);

        $kepalaSekolah = $this->getKepalaSekolahName();

        $pdf = Pdf::loadView('exports.data-siswa-pdf', compact(
            'totalAktif', 'totalAlumni', 'totalKeluar', 'tanpaAkun',
            'totalL', 'totalP', 'retentionRate',
            'studentsGrouped', 'growthData', 'maxGrowth', 'kepalaSekolah'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-data-siswa-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportDataGuruPdf(): Response
    {
        $teachers = Teacher::with(['user', 'homeroomClass'])->get();

        $guruAktif    = $teachers->filter(fn ($t) => $t->user?->status === 'active');
        $guruNonaktif = $teachers->filter(fn ($t) => $t->user?->status !== 'active');
        $totalAktif   = $guruAktif->count();

        $totalSiswaAktif = Student::where('status', 'aktif')->count();
        $rasio = $totalAktif > 0 ? round($totalSiswaAktif / $totalAktif) : 0;

        $tipeBreakdown = $guruAktif->groupBy('tipe')->map->count()->sortDesc();

        $kepalaSekolah = $this->getKepalaSekolahName();

        $pdf = Pdf::loadView('exports.data-guru-pdf', compact(
            'guruAktif', 'guruNonaktif', 'totalAktif', 'totalSiswaAktif',
            'rasio', 'tipeBreakdown', 'kepalaSekolah'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-data-guru-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportDataOrangTuaPdf(): Response
    {
        $totalAkun = User::whereHas('role', fn ($q) => $q->where('role_name', 'Orang Tua'))->count();

        $childrenPerParent = Student::where('status', 'aktif')
            ->whereNotNull('user_id')
            ->get()
            ->groupBy('user_id');

        $activeParentIds = $childrenPerParent->keys();

        $activeParents = User::whereIn('user_id', $activeParentIds)
            ->get()
            ->each(fn ($p) => $p->anak_aktif = $childrenPerParent[$p->user_id]->count());
        $totalAktif = $activeParents->count();

        $studentsWithUnpaid = Student::where('status', 'aktif')
            ->whereNotNull('user_id')
            ->whereHas('sppInvoices', fn ($q) => $q->whereIn('status', ['unpaid', 'overdue']))
            ->with(['user', 'classRoom', 'sppInvoices' => fn ($q) => $q->whereIn('status', ['unpaid', 'overdue'])])
            ->get();

        $unpaidInvoiceParents = $studentsWithUnpaid->groupBy('user_id')
            ->map(function ($students) {
                $allInvoices = $students->flatMap->sppInvoices;
                $oldestOverdue = $allInvoices->filter(fn ($inv) => $inv->jatuh_tempo && now()->gt($inv->jatuh_tempo))
                    ->sortBy('jatuh_tempo')
                    ->first();
                $agingMonths = $oldestOverdue ? (int) $oldestOverdue->jatuh_tempo->diffInMonths(now()) : 0;

                return [
                    'parent'        => $students->first()->user,
                    'students'      => $students,
                    'total_tagihan' => $allInvoices->sum('jumlah'),
                    'jumlah_invoice' => $allInvoices->count(),
                    'aging_months'  => $agingMonths,
                ];
            })
            ->sortByDesc('total_tagihan');

        $totalTunggakan = $unpaidInvoiceParents->sum('total_tagihan');
        $kepalaSekolah = $this->getKepalaSekolahName();

        $pdf = Pdf::loadView('exports.data-orang-tua-pdf', compact(
            'totalAkun', 'totalAktif', 'activeParents', 'unpaidInvoiceParents',
            'totalTunggakan', 'kepalaSekolah'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-data-orang-tua-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportSppPdf(?int $periodId = null): Response
    {
        $period = $periodId
            ? AcademicPeriod::findOrFail($periodId)
            : AcademicPeriod::where('is_active', true)->first() ?? AcademicPeriod::latest('tanggal_mulai')->first();

        $start = $period->tanggal_mulai;
        $end   = $period->tanggal_selesai;

        $totalInvoiced = (int) SppInvoice::whereBetween('tanggal_tahun', [$start, $end])->sum('jumlah');
        $totalCollected = (int) SppInvoice::where('status', 'paid')
            ->whereBetween('tanggal_tahun', [$start, $end])
            ->sum('jumlah');
        $collectionRate = $totalInvoiced > 0 ? round(($totalCollected / $totalInvoiced) * 100, 1) : 0;

        $monthlyBreakdown = [];
        $date = $start->copy()->startOfMonth();
        while ($date->lte($end)) {
            $invoiced = (int) SppInvoice::whereYear('tanggal_tahun', $date->year)
                ->whereMonth('tanggal_tahun', $date->month)
                ->sum('jumlah');
            $collected = (int) SppInvoice::where('status', 'paid')
                ->whereYear('tanggal_tahun', $date->year)
                ->whereMonth('tanggal_tahun', $date->month)
                ->sum('jumlah');
            $monthlyBreakdown[] = [
                'label'     => $date->translatedFormat('F Y'),
                'value'     => $collected,
                'invoiced'  => $invoiced,
                'rate'      => $invoiced > 0 ? round(($collected / $invoiced) * 100, 1) : 0,
            ];
            $date->addMonth();
        }
        $maxMonthly = max(array_column($monthlyBreakdown, 'value') ?: [1]);

        $totalOutstanding = (int) SppInvoice::whereIn('status', ['unpaid', 'overdue'])
            ->whereBetween('tanggal_tahun', [$start, $end])
            ->sum('jumlah');

        $unpaidInvoices = SppInvoice::whereIn('status', ['unpaid', 'overdue'])
            ->whereBetween('tanggal_tahun', [$start, $end])
            ->get();

        $aging = [
            ['label' => '≤ 1 Bulan', 'count' => 0, 'amount' => 0],
            ['label' => '2–3 Bulan', 'count' => 0, 'amount' => 0],
            ['label' => '4–6 Bulan', 'count' => 0, 'amount' => 0],
            ['label' => '> 6 Bulan', 'count' => 0, 'amount' => 0],
        ];
        foreach ($unpaidInvoices as $inv) {
            $months = $inv->jatuh_tempo && now()->gt($inv->jatuh_tempo) ? (int) $inv->jatuh_tempo->diffInMonths(now()) : 0;
            $idx = match (true) {
                $months <= 1 => 0,
                $months <= 3 => 1,
                $months <= 6 => 2,
                default      => 3,
            };
            $aging[$idx]['count']++;
            $aging[$idx]['amount'] += $inv->jumlah;
        }

        $unpaidByClass = SppInvoice::with(['student.classRoom', 'student.user'])
            ->whereIn('status', ['unpaid', 'overdue'])
            ->whereBetween('tanggal_tahun', [$start, $end])
            ->get()
            ->groupBy(fn ($inv) => $inv->student?->classRoom?->nama_kelas ?? 'Tanpa Kelas');

        $totalSiswaAktif = Student::where('status', 'aktif')->count();
        $siswaMenunggak = SppInvoice::whereIn('status', ['unpaid', 'overdue'])
            ->whereBetween('tanggal_tahun', [$start, $end])
            ->distinct('student_id')
            ->count('student_id');
        $delinquencyRate = $totalSiswaAktif > 0 ? round(($siswaMenunggak / $totalSiswaAktif) * 100, 1) : 0;

        $periodLabel = $period->tahun_ajaran . ' - Semester ' . $period->semester;
        $kepalaSekolah = $this->getKepalaSekolahName();

        $pdf = Pdf::loadView('exports.spp-pdf', compact(
            'totalInvoiced', 'totalCollected', 'collectionRate',
            'monthlyBreakdown', 'maxMonthly', 'totalOutstanding',
            'aging', 'unpaidByClass', 'siswaMenunggak', 'delinquencyRate',
            'periodLabel', 'kepalaSekolah'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-spp-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportPendaftaranPdf(?int $periodId = null): Response
    {
        $period = $periodId
            ? AcademicPeriod::findOrFail($periodId)
            : AcademicPeriod::where('is_active', true)->first() ?? AcademicPeriod::latest('tanggal_mulai')->first();

        $start = $period->tanggal_mulai;
        $end   = $period->tanggal_selesai;

        $totalRegistrations = Registration::whereBetween('created_at', [$start, $end])->count();
        $totalAccepted = Registration::where('status', 'accepted')->whereBetween('created_at', [$start, $end])->count();
        $totalRejected = Registration::where('status', 'rejected')->whereBetween('created_at', [$start, $end])->count();
        $totalPending  = Registration::where('status', 'pending')->whereBetween('created_at', [$start, $end])->count();
        $conversionRate = $totalRegistrations > 0 ? round(($totalAccepted / $totalRegistrations) * 100, 1) : 0;

        $totalViaApp   = Registration::whereBetween('created_at', [$start, $end])->where('source', 'app')->count();
        $totalViaAdmin = Registration::whereBetween('created_at', [$start, $end])->where('source', 'admin')->count();

        $totalPendapatan = (int) RegistrationTransaction::where('status', 'approved')
            ->whereBetween('payment_date', [$start, $end])
            ->sum('jumlah_bayar');

        $unpaidFees = RegistrationFee::with('student.classRoom')
            ->where('status', '!=', 'paid')
            ->whereHas('student', fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->get();
        $totalUnpaid = $unpaidFees->count();

        $paidFees = RegistrationFee::where('status', 'paid')
            ->whereHas('student', fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->get();

        $totalCicilan = $paidFees->filter(fn ($f) => $f->transactions()->where('status', 'approved')->count() > 1)->count();
        $totalLunas   = $paidFees->count() - $totalCicilan;

        $periodLabel   = $period->tahun_ajaran . ' - Semester ' . $period->semester;
        $kepalaSekolah = $this->getKepalaSekolahName();

        $pdf = Pdf::loadView('exports.pendaftaran-pdf', compact(
            'totalRegistrations', 'totalAccepted', 'totalRejected', 'totalPending',
            'conversionRate', 'totalViaApp', 'totalViaAdmin', 'totalPendapatan',
            'unpaidFees', 'totalUnpaid', 'totalCicilan', 'totalLunas',
            'periodLabel', 'kepalaSekolah'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-pendaftaran-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportKelasPdf(): Response
    {
        $classes = ClassRoom::with(['homeroomTeacher.user', 'students' => fn ($q) => $q->where('status', 'aktif')])
            ->orderBy('nama_kelas')
            ->get();

        $totalClasses  = $classes->count();
        $totalSiswa    = $classes->sum(fn ($c) => $c->students->count());
        $totalGuruAktif = Teacher::whereHas('user', fn ($q) => $q->where('status', 'active'))->count();
        $rasio = $totalGuruAktif > 0 ? round($totalSiswa / $totalGuruAktif) : 0;
        $totalL = $classes->sum(fn ($c) => $c->students->where('jenis_kelamin', 'L')->count());
        $totalP = $classes->sum(fn ($c) => $c->students->where('jenis_kelamin', 'P')->count());

        $kepalaSekolah = $this->getKepalaSekolahName();

        $pdf = Pdf::loadView('exports.kelas-pdf', compact(
            'classes', 'totalClasses', 'totalSiswa', 'totalGuruAktif', 'rasio',
            'totalL', 'totalP', 'kepalaSekolah'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-kelas-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportTabunganPdf(): Response
    {
        $totalSavings = (int) StudentPassbook::sum('current_balance');
        $totalPassbooks = StudentPassbook::count();
        $totalSiswaAktif = Student::where('status', 'aktif')->count();
        $participationRate = $totalSiswaAktif > 0 ? round(($totalPassbooks / $totalSiswaAktif) * 100, 1) : 0;
        $avgBalance = $totalPassbooks > 0 ? round($totalSavings / $totalPassbooks) : 0;

        $savingsPerClass = SavingLedger::with('classRoom')
            ->where('status', 'Active')
            ->get()
            ->groupBy(fn ($l) => $l->classRoom?->nama_kelas ?? 'Tanpa Kelas')
            ->map(fn ($ledgers) => (int) $ledgers->sum('total_balance'));

        $topStudents = StudentPassbook::with('student.classRoom')
            ->where('current_balance', '>', 0)
            ->orderByDesc('current_balance')
            ->limit(10)
            ->get();

        $kepalaSekolah = $this->getKepalaSekolahName();

        $pdf = Pdf::loadView('exports.tabungan-pdf', compact(
            'totalSavings', 'totalPassbooks', 'totalSiswaAktif', 'participationRate',
            'avgBalance', 'savingsPerClass', 'topStudents', 'kepalaSekolah'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-tabungan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportRingkasanEksekutifPdf(?int $periodId = null): Response
    {
        $period = $periodId
            ? AcademicPeriod::findOrFail($periodId)
            : AcademicPeriod::where('is_active', true)->first() ?? AcademicPeriod::latest('tanggal_mulai')->first();

        $start = $period->tanggal_mulai;
        $end   = $period->tanggal_selesai;
        $periodLabel = $period->tahun_ajaran . ' - Semester ' . $period->semester;

        $prevPeriod = AcademicPeriod::where('tanggal_mulai', '<', $start)
            ->orderByDesc('tanggal_mulai')
            ->first();
        $prevPeriodLabel = $prevPeriod
            ? $prevPeriod->tahun_ajaran . ' - Semester ' . $prevPeriod->semester
            : null;

        // ── Siswa ──
        $totalSiswaAktif = Student::where('status', 'aktif')->count();
        $siswaBaru = Student::whereBetween('created_at', [$start, $end])->count();
        $siswaKeluar = Student::where('status', 'keluar')
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        $prevSiswaBaru = 0;
        if ($prevPeriod) {
            $prevSiswaBaru = Student::whereBetween('created_at', [$prevPeriod->tanggal_mulai, $prevPeriod->tanggal_selesai])->count();
        }

        // ── Guru & Rasio ──
        $totalGuruAktif = Teacher::whereHas('user', fn ($q) => $q->where('status', 'active'))->count();
        $rasioGuruSiswa = $totalGuruAktif > 0 ? round($totalSiswaAktif / $totalGuruAktif) : 0;

        // ── Keuangan SPP ──
        $sppInvoicedAmount = (int) SppInvoice::whereBetween('tanggal_tahun', [$start, $end])->sum('jumlah');
        $sppCollectedAmount = (int) SppInvoice::where('status', 'paid')
            ->whereBetween('tanggal_tahun', [$start, $end])
            ->sum('jumlah');
        $sppCollectionRate = $sppInvoicedAmount > 0
            ? round(($sppCollectedAmount / $sppInvoicedAmount) * 100, 1)
            : 0;

        $prevSppCollected = 0;
        if ($prevPeriod) {
            $prevSppCollected = (int) SppInvoice::where('status', 'paid')
                ->whereBetween('tanggal_tahun', [$prevPeriod->tanggal_mulai, $prevPeriod->tanggal_selesai])
                ->sum('jumlah');
        }

        // ── Keuangan Pendaftaran ──
        $regCollected = (int) RegistrationTransaction::where('status', 'approved')
            ->whereBetween('payment_date', [$start, $end])
            ->sum('jumlah_bayar');

        $prevRegCollected = 0;
        if ($prevPeriod) {
            $prevRegCollected = (int) RegistrationTransaction::where('status', 'approved')
                ->whereBetween('payment_date', [$prevPeriod->tanggal_mulai, $prevPeriod->tanggal_selesai])
                ->sum('jumlah_bayar');
        }

        $totalRevenue     = $sppCollectedAmount + $regCollected;
        $prevTotalRevenue = $prevSppCollected + $prevRegCollected;
        $revenueDelta = $prevTotalRevenue > 0
            ? round((($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100, 1)
            : null;

        $revenuePerSiswa = $totalSiswaAktif > 0 ? round($totalRevenue / $totalSiswaAktif) : 0;

        // ── Aging Analysis Tunggakan ──
        $unpaidInvoices = SppInvoice::whereIn('status', ['unpaid', 'overdue'])->get();

        $aging = [
            ['label' => '≤ 1 Bulan', 'count' => 0, 'amount' => 0],
            ['label' => '2–3 Bulan', 'count' => 0, 'amount' => 0],
            ['label' => '4–6 Bulan', 'count' => 0, 'amount' => 0],
            ['label' => '> 6 Bulan', 'count' => 0, 'amount' => 0],
        ];

        foreach ($unpaidInvoices as $inv) {
            $months = $inv->jatuh_tempo && now()->gt($inv->jatuh_tempo) ? (int) $inv->jatuh_tempo->diffInMonths(now()) : 0;
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

        // ── Pendapatan Bulanan ──
        $monthlyRevenue = [];
        $date = $start->copy()->startOfMonth();
        while ($date->lte($end)) {
            $spp = (int) SppInvoice::where('status', 'paid')
                ->whereYear('tanggal_tahun', $date->year)
                ->whereMonth('tanggal_tahun', $date->month)
                ->sum('jumlah');
            $reg = (int) RegistrationTransaction::where('status', 'approved')
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('jumlah_bayar');
            $monthlyRevenue[] = [
                'label' => $date->translatedFormat('M Y'),
                'spp'   => $spp,
                'reg'   => $reg,
                'total' => $spp + $reg,
            ];
            $date->addMonth();
        }
        $maxMonthlyRevenue = max(array_column($monthlyRevenue, 'total') ?: [1]);

        // ── PPDB ──
        $totalRegistrations = Registration::whereBetween('created_at', [$start, $end])->count();
        $totalAccepted = Registration::where('status', 'accepted')->whereBetween('created_at', [$start, $end])->count();
        $totalRejected = Registration::where('status', 'rejected')->whereBetween('created_at', [$start, $end])->count();
        $totalPending  = Registration::where('status', 'pending')->whereBetween('created_at', [$start, $end])->count();
        $conversionRate = $totalRegistrations > 0
            ? round(($totalAccepted / $totalRegistrations) * 100, 1)
            : 0;

        $regViaApp   = Registration::whereBetween('created_at', [$start, $end])->where('source', 'app')->count();
        $regViaAdmin = Registration::whereBetween('created_at', [$start, $end])->where('source', 'admin')->count();

        // ── Churn Rate ──
        $totalAtPeriodStart = Student::whereIn('status', ['aktif', 'keluar', 'alumni'])
            ->where('created_at', '<', $start)
            ->count();
        $churnRate = $totalAtPeriodStart > 0
            ? round(($siswaKeluar / $totalAtPeriodStart) * 100, 1)
            : 0;

        // ── Kelas Overview ──
        $classOverview = ClassRoom::withCount(['students' => fn ($q) => $q->where('status', 'aktif')])
            ->with('homeroomTeacher.user')
            ->orderBy('nama_kelas')
            ->get();

        $kepalaSekolah = $this->getKepalaSekolahName();

        $pdf = Pdf::loadView('exports.ringkasan-eksekutif-pdf', compact(
            'periodLabel', 'prevPeriodLabel',
            'totalSiswaAktif', 'siswaBaru', 'siswaKeluar', 'prevSiswaBaru',
            'totalGuruAktif', 'rasioGuruSiswa',
            'sppInvoicedAmount', 'sppCollectedAmount', 'sppCollectionRate',
            'regCollected', 'totalRevenue', 'prevTotalRevenue', 'revenueDelta',
            'revenuePerSiswa',
            'aging', 'totalOutstanding',
            'totalRegistrations', 'totalAccepted', 'totalRejected', 'totalPending',
            'conversionRate', 'regViaApp', 'regViaAdmin',
            'monthlyRevenue', 'maxMonthlyRevenue',
            'churnRate', 'classOverview',
            'kepalaSekolah'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('ringkasan-eksekutif-' . now()->format('Y-m-d') . '.pdf');
    }

    // ─── EXCEL ───────────────────────────────────────

    public function exportKeuanganExcel(?int $year = null): StreamedResponse
    {
        $year           = $year ?? now()->year;
        $invoices       = SppInvoice::with('student.classRoom')->latest('jatuh_tempo')->get();
        $monthlySummary = $this->getMonthlySummary($year);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('IMS IQRA Creative House')
            ->setTitle("Laporan Keuangan {$year}");

        // ── Sheet 1: Ringkasan Bulanan ──
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan Bulanan');

        $this->applyExcelHeader($sheet, "Laporan Keuangan - TK IQRA' Creative House", "Tahun {$year}");

        $sheet->setCellValue('A5', 'Bulan');
        $sheet->setCellValue('B5', 'SPP (Rp)');
        $sheet->setCellValue('C5', 'Pendaftaran (Rp)');
        $sheet->setCellValue('D5', 'Total (Rp)');
        $this->styleHeaderRow($sheet, 'A5:D5');

        $row = 6;
        foreach ($monthlySummary as $ms) {
            $sheet->setCellValue("A{$row}", $ms['label']);
            $sheet->setCellValue("B{$row}", $ms['spp']);
            $sheet->setCellValue("C{$row}", $ms['pendaftaran']);
            $sheet->setCellValue("D{$row}", $ms['spp'] + $ms['pendaftaran']);
            $sheet->getStyle("B{$row}:D{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        $totalRow = $row;
        $sheet->setCellValue("A{$totalRow}", 'TOTAL');
        $sheet->setCellValue("B{$totalRow}", "=SUM(B6:B" . ($row - 1) . ")");
        $sheet->setCellValue("C{$totalRow}", "=SUM(C6:C" . ($row - 1) . ")");
        $sheet->setCellValue("D{$totalRow}", "=SUM(D6:D" . ($row - 1) . ")");
        $sheet->getStyle("A{$totalRow}:D{$totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("B{$totalRow}:D{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("A{$totalRow}:D{$totalRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]],
        ]);

        $sheet->getStyle("A5:D{$totalRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        foreach (['A', 'B', 'C', 'D'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ── Chart: Bar chart pendapatan bulanan ──
        $categories = [new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$A\$6:\$A\$17", null, 12)];
        $values = [
            new DataSeriesValues('Number', "'{$sheet->getTitle()}'!\$B\$6:\$B\$17", null, 12, null, null),
            new DataSeriesValues('Number', "'{$sheet->getTitle()}'!\$C\$6:\$C\$17", null, 12, null, null),
        ];
        $values[0]->setDataSource("'{$sheet->getTitle()}'!\$B\$6:\$B\$17");
        $values[1]->setDataSource("'{$sheet->getTitle()}'!\$C\$6:\$C\$17");

        $seriesLabels = [
            new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$B\$5", null, 1),
            new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$C\$5", null, 1),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($values) - 1),
            $seriesLabels,
            $categories,
            $values
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new Title("Pendapatan Bulanan {$year}");

        $chart = new Chart('MonthlyRevenue', $title, $legend, $plotArea);
        $chart->setTopLeftPosition('A' . ($totalRow + 2));
        $chart->setBottomRightPosition('H' . ($totalRow + 18));
        $sheet->addChart($chart);

        // ── Sheet 2: Detail Tagihan SPP ──
        $detailSheet = $spreadsheet->createSheet();
        $detailSheet->setTitle('Detail Tagihan SPP');

        $this->applyExcelHeader($detailSheet, 'Detail Tagihan SPP', 'Dicetak: ' . now()->translatedFormat('d F Y'));

        $headers = ['No', 'Nama Siswa', 'Kelas', 'Periode', 'Jumlah', 'Jatuh Tempo', 'Status'];
        foreach ($headers as $ci => $h) {
            $detailSheet->setCellValue([$ci + 1, 5], $h);
        }
        $this->styleHeaderRow($detailSheet, 'A5:G5');

        $row = 6;
        foreach ($invoices as $i => $inv) {
            $detailSheet->setCellValue("A{$row}", $i + 1);
            $detailSheet->setCellValue("B{$row}", $inv->student?->nama_siswa ?? '-');
            $detailSheet->setCellValue("C{$row}", $inv->student?->classRoom?->nama_kelas ?? '-');
            $detailSheet->setCellValue("D{$row}", $inv->tanggal_tahun?->format('Y-m') ?? '-');
            $detailSheet->setCellValue("E{$row}", $inv->jumlah);
            $detailSheet->setCellValue("F{$row}", $inv->jatuh_tempo?->format('d/m/Y') ?? '-');
            $detailSheet->setCellValue("G{$row}", ucfirst($inv->status));
            $detailSheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        $detailSheet->getStyle("A5:G" . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $detailSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $this->streamExcel($spreadsheet, "laporan-keuangan-{$year}");
    }

    public function exportAbsensiSiswaExcel(int $classId, int $year, int $month): StreamedResponse
    {
        $classroom = ClassRoom::findOrFail($classId);
        $recap     = $this->studentAttendanceService->getMonthlyRecap($classId, $year, $month);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('IMS IQRA Creative House')
            ->setTitle("Rekap Absensi Siswa - {$classroom->nama_kelas}");

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Absensi Siswa');

        $this->applyExcelHeader($sheet, "Rekap Absensi Siswa - {$classroom->nama_kelas}", Carbon::create($year, $month)->translatedFormat('F Y'));

        $headers = ['No', 'Nama Siswa', 'Hadir', 'Izin', 'Sakit', 'Tanpa Ket.', 'Total'];
        foreach ($headers as $ci => $h) {
            $sheet->setCellValue([$ci + 1, 5], $h);
        }
        $this->styleHeaderRow($sheet, 'A5:G5');

        $row = 6;
        foreach ($recap as $i => $r) {
            $total = ($r['hadir'] ?? 0) + $r['izin'] + $r['sakit'] + $r['tanpa_keterangan'];
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $r['nama']);
            $sheet->setCellValue("C{$row}", $r['hadir'] ?? 0);
            $sheet->setCellValue("D{$row}", $r['izin']);
            $sheet->setCellValue("E{$row}", $r['sakit']);
            $sheet->setCellValue("F{$row}", $r['tanpa_keterangan']);
            $sheet->setCellValue("G{$row}", $total);
            $row++;
        }

        if ($recap->count() > 0) {
            $sheet->setCellValue("A{$row}", '');
            $sheet->setCellValue("B{$row}", 'TOTAL');
            $sheet->setCellValue("C{$row}", "=SUM(C6:C" . ($row - 1) . ")");
            $sheet->setCellValue("D{$row}", "=SUM(D6:D" . ($row - 1) . ")");
            $sheet->setCellValue("E{$row}", "=SUM(E6:E" . ($row - 1) . ")");
            $sheet->setCellValue("F{$row}", "=SUM(F6:F" . ($row - 1) . ")");
            $sheet->setCellValue("G{$row}", "=SUM(G6:G" . ($row - 1) . ")");
            $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']],
            ]);

            // Chart kehadiran
            $dataCount = $row - 6;
            if ($dataCount > 0 && $dataCount <= 30) {
                $categories = [new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$B\$6:\$B\$" . ($row - 1), null, $dataCount)];
                $values = [
                    new DataSeriesValues('Number', "'{$sheet->getTitle()}'!\$C\$6:\$C\$" . ($row - 1), null, $dataCount),
                    new DataSeriesValues('Number', "'{$sheet->getTitle()}'!\$D\$6:\$D\$" . ($row - 1), null, $dataCount),
                    new DataSeriesValues('Number', "'{$sheet->getTitle()}'!\$E\$6:\$E\$" . ($row - 1), null, $dataCount),
                    new DataSeriesValues('Number', "'{$sheet->getTitle()}'!\$F\$6:\$F\$" . ($row - 1), null, $dataCount),
                ];
                $seriesLabels = [
                    new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$C\$5", null, 1),
                    new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$D\$5", null, 1),
                    new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$E\$5", null, 1),
                    new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$F\$5", null, 1),
                ];

                $series = new DataSeries(
                    DataSeries::TYPE_BARCHART,
                    DataSeries::GROUPING_STACKED,
                    range(0, 3),
                    $seriesLabels,
                    $categories,
                    $values
                );

                $plotArea = new PlotArea(null, [$series]);
                $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
                $title    = new Title("Kehadiran Siswa - {$classroom->nama_kelas}");

                $chart = new Chart('Attendance', $title, $legend, $plotArea);
                $chart->setTopLeftPosition('A' . ($row + 2));
                $chart->setBottomRightPosition('H' . ($row + 18));
                $sheet->addChart($chart);
            }
        }

        $sheet->getStyle("A5:G{$row}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $this->streamExcel($spreadsheet, "rekap-absensi-siswa-{$classroom->nama_kelas}-{$year}-{$month}");
    }

    public function exportAbsensiGuruExcel(int $year, int $month): StreamedResponse
    {
        $recap = $this->attendanceService->getMonthlyRecap($year, $month);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('IMS IQRA Creative House')
            ->setTitle('Rekap Absensi Guru');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Absensi Guru');

        $this->applyExcelHeader($sheet, 'Rekap Absensi Guru', Carbon::create($year, $month)->translatedFormat('F Y'));

        $headers = ['No', 'Nama Guru', 'Hadir', 'Izin', 'Sakit', 'Tanpa Ket.'];
        foreach ($headers as $ci => $h) {
            $sheet->setCellValue([$ci + 1, 5], $h);
        }
        $this->styleHeaderRow($sheet, 'A5:F5');

        $row = 6;
        foreach ($recap as $i => $r) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $r['nama']);
            $sheet->setCellValue("C{$row}", $r['hadir']);
            $sheet->setCellValue("D{$row}", $r['izin']);
            $sheet->setCellValue("E{$row}", $r['sakit']);
            $sheet->setCellValue("F{$row}", $r['tanpa_keterangan']);
            $row++;
        }

        if ($recap->count() > 0) {
            $sheet->setCellValue("A{$row}", '');
            $sheet->setCellValue("B{$row}", 'TOTAL');
            $sheet->setCellValue("C{$row}", "=SUM(C6:C" . ($row - 1) . ")");
            $sheet->setCellValue("D{$row}", "=SUM(D6:D" . ($row - 1) . ")");
            $sheet->setCellValue("E{$row}", "=SUM(E6:E" . ($row - 1) . ")");
            $sheet->setCellValue("F{$row}", "=SUM(F6:F" . ($row - 1) . ")");
            $sheet->getStyle("A{$row}:F{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']],
            ]);

            $dataCount = $row - 6;
            if ($dataCount > 0 && $dataCount <= 30) {
                $categories = [new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$B\$6:\$B\$" . ($row - 1), null, $dataCount)];
                $values = [
                    new DataSeriesValues('Number', "'{$sheet->getTitle()}'!\$C\$6:\$C\$" . ($row - 1), null, $dataCount),
                    new DataSeriesValues('Number', "'{$sheet->getTitle()}'!\$D\$6:\$D\$" . ($row - 1), null, $dataCount),
                    new DataSeriesValues('Number', "'{$sheet->getTitle()}'!\$E\$6:\$E\$" . ($row - 1), null, $dataCount),
                    new DataSeriesValues('Number', "'{$sheet->getTitle()}'!\$F\$6:\$F\$" . ($row - 1), null, $dataCount),
                ];
                $seriesLabels = [
                    new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$C\$5", null, 1),
                    new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$D\$5", null, 1),
                    new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$E\$5", null, 1),
                    new DataSeriesValues('String', "'{$sheet->getTitle()}'!\$F\$5", null, 1),
                ];

                $series = new DataSeries(
                    DataSeries::TYPE_BARCHART,
                    DataSeries::GROUPING_CLUSTERED,
                    range(0, 3),
                    $seriesLabels,
                    $categories,
                    $values
                );

                $plotArea = new PlotArea(null, [$series]);
                $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
                $title    = new Title('Kehadiran Guru - ' . Carbon::create($year, $month)->translatedFormat('F Y'));

                $chart = new Chart('GuruAttendance', $title, $legend, $plotArea);
                $chart->setTopLeftPosition('A' . ($row + 2));
                $chart->setBottomRightPosition('H' . ($row + 18));
                $sheet->addChart($chart);
            }
        }

        $sheet->getStyle("A5:F{$row}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $this->streamExcel($spreadsheet, "rekap-absensi-guru-{$year}-{$month}");
    }

    // ─── Helpers ─────────────────────────────────────

    private function applyExcelHeader($sheet, string $title, string $subtitle): void
    {
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', "TK IQRA' Creative House");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('3DA746'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Jl. Karya Wisata, Medan Johor, Kota Medan, Sumatera Utara');
        $sheet->getStyle('A2')->getFont()->setSize(9)->setColor(new Color('888888'));

        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', $title . ' — ' . $subtitle);
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(11);

        $sheet->getRowDimension(4)->setRowHeight(6);
    }

    private function styleHeaderRow($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3DA746']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '2E7D32']],
            ],
        ]);
    }

    private function streamExcel(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->setIncludeCharts(true);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
