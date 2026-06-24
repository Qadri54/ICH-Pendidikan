<?php

namespace App\Services\Export;

use App\Models\ClassRoom;
use App\Models\RegistrationTransaction;
use App\Models\SavingLedger;
use App\Models\SppInvoice;
use App\Models\SppPayment;
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

    // ─── PDF ─────────────────────────────────────────

    public function exportKeuanganPdf(?int $year = null): Response
    {
        $year             = $year ?? now()->year;
        $invoices         = SppInvoice::with('student.classRoom')->latest('jatuh_tempo')->get();
        $totalSpp         = $invoices->where('status', 'paid')->sum('jumlah');
        $totalPendaftaran = $this->registrationFeeService->getTotalCollected();
        $totalTabungan    = SavingLedger::sum('total_balance');
        $monthlySummary   = $this->getMonthlySummary($year);

        $pdf = Pdf::loadView('exports.keuangan-pdf', compact(
            'invoices', 'totalSpp', 'totalPendaftaran', 'totalTabungan', 'monthlySummary'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-keuangan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportAbsensiSiswaPdf(int $classId, int $year, int $month): Response
    {
        $classroom = ClassRoom::findOrFail($classId);
        $recap     = $this->studentAttendanceService->getMonthlyRecap($classId, $year, $month);

        $pdf = Pdf::loadView('exports.absensi-siswa-pdf', compact(
            'classroom', 'recap', 'year', 'month'
        ));

        return $pdf->download("rekap-absensi-siswa-{$classroom->nama_kelas}-{$year}-{$month}.pdf");
    }

    public function exportAbsensiGuruPdf(int $year, int $month): Response
    {
        $recap = $this->attendanceService->getMonthlyRecap($year, $month);

        $pdf = Pdf::loadView('exports.absensi-guru-pdf', compact('recap', 'year', 'month'));

        return $pdf->download("rekap-absensi-guru-{$year}-{$month}.pdf");
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
