<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\SavingLedger;
use App\Models\SppInvoice;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\Export\LaporanExportService;
use App\Services\Registration\RegistrationFeeService;
use App\Services\Spp\SppInvoiceService;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(
        private SppInvoiceService $invoiceService,
        private RegistrationFeeService $registrationFeeService,
        private LaporanExportService $exportService,
    ) {}

    public function index()
    {
        $summary = $this->invoiceService->getSummary();

        $totalSpp          = SppInvoice::where('status', 'paid')->sum('jumlah');
        $totalPendaftaran  = $this->registrationFeeService->getTotalCollected();

        $stats = [
            'total_siswa'            => Student::count(),
            'total_guru'             => Teacher::count(),
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

        $classes = ClassRoom::orderBy('nama_kelas')->get();

        return view('admin.laporan.index', compact('stats', 'pembayaranSpp', 'lunasPendaftaran', 'classes'));
    }

    public function exportKeuanganPdf()
    {
        return $this->exportService->exportKeuanganPdf();
    }

    public function exportKeuanganCsv()
    {
        return $this->exportService->exportKeuanganCsv();
    }

    public function exportAbsensiSiswaPdf(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,class_id',
            'year'     => 'required|integer|min:2020',
            'month'    => 'required|integer|min:1|max:12',
        ]);

        return $this->exportService->exportAbsensiSiswaPdf(
            $request->integer('class_id'),
            $request->integer('year'),
            $request->integer('month')
        );
    }

    public function exportAbsensiSiswaCsv(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,class_id',
            'year'     => 'required|integer|min:2020',
            'month'    => 'required|integer|min:1|max:12',
        ]);

        return $this->exportService->exportAbsensiSiswaCsv(
            $request->integer('class_id'),
            $request->integer('year'),
            $request->integer('month')
        );
    }

    public function exportAbsensiGuruPdf(Request $request)
    {
        $request->validate([
            'year'  => 'required|integer|min:2020',
            'month' => 'required|integer|min:1|max:12',
        ]);

        return $this->exportService->exportAbsensiGuruPdf(
            $request->integer('year'),
            $request->integer('month')
        );
    }

    public function exportAbsensiGuruCsv(Request $request)
    {
        $request->validate([
            'year'  => 'required|integer|min:2020',
            'month' => 'required|integer|min:1|max:12',
        ]);

        return $this->exportService->exportAbsensiGuruCsv(
            $request->integer('year'),
            $request->integer('month')
        );
    }
}
