<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\Spp\SppInvoiceService;

class LaporanController extends Controller
{
    public function __construct(private SppInvoiceService $invoiceService) {}

    public function index()
    {
        $summary = $this->invoiceService->getSummary();

        $stats = [
            'total_siswa'      => Student::count(),
            'tagihan_berjalan' => $summary['tagihan_berjalan'],
            'tagihan_lunas'    => $summary['total_lunas'],
            'total_tagihan'    => $summary['total_tagihan'],
        ];

        return view('admin.laporan.index', compact('stats'));
    }
}
