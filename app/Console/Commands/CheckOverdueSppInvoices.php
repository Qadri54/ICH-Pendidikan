<?php

namespace App\Console\Commands;

use App\Services\Spp\SppInvoiceService;
use Illuminate\Console\Command;

class CheckOverdueSppInvoices extends Command
{
    protected $signature   = 'spp:check-overdue';
    protected $description = 'Update invoice SPP yang melewati jatuh tempo menjadi overdue';

    public function __construct(
        private SppInvoiceService $sppInvoiceService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $count = $this->sppInvoiceService->checkOverdue();

        $this->info("✅  CheckOverdueSppInvoices: {$count} invoice ditandai overdue.");
    }
}
