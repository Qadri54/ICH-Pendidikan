<?php

namespace App\Console\Commands;

use App\Services\Spp\SppInvoiceService;
use Illuminate\Console\Command;

class GenerateSppInvoices extends Command
{
    protected $signature   = 'spp:generate-invoices';
    protected $description = 'Generate invoice SPP bulanan untuk semua siswa aktif (dijalankan setiap tanggal 1)';

    public function __construct(
        private SppInvoiceService $sppInvoiceService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $count = $this->sppInvoiceService->generateMonthlyInvoices();

        $this->info("✅  GenerateSppInvoices: {$count} invoice berhasil dibuat.");
    }
}
