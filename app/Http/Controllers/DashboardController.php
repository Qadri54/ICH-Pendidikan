<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Registration;
use App\Models\RegistrationTransaction;
use App\Models\SavingLedger;
use App\Models\SppInvoice;
use App\Models\Student;
use App\Models\StudentAttendance;
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
            $attendanceData = $this->getAttendanceData();
            $teacherId = $user->teacher?->teacher_id;
            $teacherRecap = $teacherId ? $this->getTeacherRecap($teacherId) : [];
            return view('guru.dashboard', compact('user') + $attendanceData + $teacherRecap);
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

        return view('admin.dashboard', compact('user', 'role', 'stats', 'recentPayments', 'monthlyIncome', 'currentYear'));
    }

    private function getAttendanceData(): array
    {
        $tanggal = request('tanggal', now()->toDateString());
        $filterKelas = request('kelas');

        $query = StudentAttendance::with(['student.classRoom'])
            ->whereDate('created_at', $tanggal);

        if ($filterKelas) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $filterKelas));
        }

        $attendances = $query->get();

        $attendanceSummary = [
            'hadir' => $attendances->where('status', 'Hadir')->count(),
            'sakit' => $attendances->where('status', 'Sakit')->count(),
            'izin'  => $attendances->where('status', 'Izin')->count(),
            'alpha' => $attendances->where('status', 'Alpha')->count(),
        ];

        $classes = ClassRoom::orderBy('nama_kelas')->get();

        return compact('tanggal', 'filterKelas', 'attendances', 'attendanceSummary', 'classes');
    }

    private function getTeacherRecap(int $teacherId): array
    {
        $bulan = request('bulan', now()->format('Y-m'));
        $parsed = Carbon::createFromFormat('Y-m', $bulan);

        $records = AttendanceRecord::where('teacher_id', $teacherId)
            ->whereYear('check_in_time', $parsed->year)
            ->whereMonth('check_in_time', $parsed->month)
            ->orderBy('check_in_time')
            ->get();

        $recapSummary = [
            'hadir'             => $records->where('attendance_status', 'Hadir')->count(),
            'izin'              => $records->where('attendance_status', 'Izin')->count(),
            'sakit'             => $records->where('attendance_status', 'Sakit')->count(),
            'tanpa_keterangan'  => $records->where('attendance_status', 'Tanpa Keterangan')->count(),
            'total'             => $records->count(),
        ];

        return compact('bulan', 'records', 'recapSummary');
    }
}
