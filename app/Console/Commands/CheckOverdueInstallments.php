<?php

namespace App\Console\Commands;

use App\Services\Registration\FeeInstallmentService;
use Illuminate\Console\Command;

class CheckOverdueInstallments extends Command {
    protected $signature = 'installments:check-overdue';
    protected $description = 'Update status cicilan yang melewati tanggal jatuh tempo menjadi overdue';

    public function __construct(
        private FeeInstallmentService $feeInstallmentService,
    ) {
        parent::__construct();
    }

    public function handle(): void {
        $this->feeInstallmentService->checkOverdue();
        $this->info('Pengecekan cicilan overdue berhasil dijalankan.');
    }
}
