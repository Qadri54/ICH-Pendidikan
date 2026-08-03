<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Export\LaporanExportService;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(
        private LaporanExportService $exportService,
    ) {}

    public function exportKeuanganPdf(Request $request)
    {
        return $this->exportService->exportKeuanganPdf($request->integer('year', now()->year));
    }

    public function exportKeuanganExcel(Request $request)
    {
        return $this->exportService->exportKeuanganExcel($request->integer('year', now()->year));
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

    public function exportAbsensiSiswaExcel(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,class_id',
            'year'     => 'required|integer|min:2020',
            'month'    => 'required|integer|min:1|max:12',
        ]);

        return $this->exportService->exportAbsensiSiswaExcel(
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

    public function exportAbsensiGuruExcel(Request $request)
    {
        $request->validate([
            'year'  => 'required|integer|min:2020',
            'month' => 'required|integer|min:1|max:12',
        ]);

        return $this->exportService->exportAbsensiGuruExcel(
            $request->integer('year'),
            $request->integer('month')
        );
    }

    public function exportDataSiswaPdf()
    {
        return $this->exportService->exportDataSiswaPdf();
    }

    public function exportDataGuruPdf()
    {
        return $this->exportService->exportDataGuruPdf();
    }

    public function exportDataOrangTuaPdf()
    {
        return $this->exportService->exportDataOrangTuaPdf();
    }

    public function exportSppPdf(Request $request)
    {
        return $this->exportService->exportSppPdf($request->integer('period_id') ?: null);
    }

    public function exportPendaftaranPdf(Request $request)
    {
        return $this->exportService->exportPendaftaranPdf($request->integer('period_id') ?: null);
    }

    public function exportKelasPdf()
    {
        return $this->exportService->exportKelasPdf();
    }

    public function exportTabunganPdf()
    {
        return $this->exportService->exportTabunganPdf();
    }

    public function exportRingkasanEksekutifPdf(Request $request)
    {
        return $this->exportService->exportRingkasanEksekutifPdf($request->integer('period_id') ?: null);
    }
}
