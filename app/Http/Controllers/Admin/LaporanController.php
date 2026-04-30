<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SppInvoice;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $stats = [
            'total_siswa'      => Student::count(),
            'tagihan_berjalan' => SppInvoice::where('status', 'Belum Bayar')->count(),
            'tagihan_lunas'    => SppInvoice::where('status', 'Lunas')->count(),
            'total_tagihan'    => SppInvoice::where('status', 'Belum Bayar')->sum('jumlah'),
        ];

        return view('admin.laporan.index', compact('stats'));
    }
}
