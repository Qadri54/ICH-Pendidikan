<?php

namespace App\Console\Commands;

use App\Services\Auth\OtpService;
use Illuminate\Console\Command;

class PruneUnverifiedUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:prune-unverified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus akun user yang belum terverifikasi (verified = false) lebih dari 30 hari sejak created_at';

    public function handle(OtpService $otpService): int
    {
        $deletedCount = $otpService->pruneExpiredUnverifiedUsers();

        $this->info("Berhasil menghapus {$deletedCount} akun yang belum terverifikasi (> 30 hari).");

        return self::SUCCESS;
    }
}
