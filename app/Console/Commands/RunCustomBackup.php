<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use ZipArchive;

class RunCustomBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run-custom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Jalankan pencadangan custom untuk database dan storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pencadangan...');
        try {
            set_time_limit(300); // 5 minutes limit for backup
            
            $backupDir = 'backups';
            $disk = Storage::disk('local');
            if (!$disk->exists($backupDir)) {
                $disk->makeDirectory($backupDir);
            }

            $date = Carbon::now()->format('Y-m-d_H-i-s');
            $fileName = 'backup_' . env('APP_NAME', 'IMS') . '_' . $date . '.zip';
            $zipPath = $disk->path($backupDir . '/' . $fileName);

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception("Tidak dapat membuat file ZIP di: " . $zipPath);
            }

            // 1. Backup Database
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST', '127.0.0.1');
            $sqlFile = $disk->path('temp_db_backup.sql');
            
            $passwordStr = $dbPass ? "-p\"$dbPass\"" : "";
            $command = "mysqldump -h {$dbHost} -u {$dbUser} {$passwordStr} {$dbName} > \"{$sqlFile}\"";
            
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0 && file_exists($sqlFile)) {
                $zip->addFile($sqlFile, 'database.sql');
                $this->info('Database berhasil dicadangkan.');
            } else {
                $this->warn("Backup DB gagal. Command: " . $command);
            }

            // 2. Backup Storage/app/public (Uploaded files)
            $publicStoragePath = storage_path('app/public');
            if (File::exists($publicStoragePath)) {
                $files = File::allFiles($publicStoragePath);
                foreach ($files as $file) {
                    $relativePath = 'storage/' . $file->getRelativePathname();
                    $zip->addFile($file->getRealPath(), $relativePath);
                }
                $this->info('File storage berhasil dicadangkan.');
            }

            $zip->close();

            if (file_exists($sqlFile)) {
                unlink($sqlFile);
            }

            // 3. Clean up old backups (keep only last 14 days)
            $this->cleanOldBackups($disk, $backupDir);

            $this->info("Pencadangan berhasil disimpan di: {$zipPath}");
        } catch (\Exception $e) {
            $this->error('Backup gagal: ' . $e->getMessage());
        }
    }

    private function cleanOldBackups($disk, $dir)
    {
        $files = $disk->files($dir);
        $threshold = Carbon::now()->subDays(14)->timestamp;
        $deletedCount = 0;

        foreach ($files as $f) {
            if (substr($f, -4) == ".zip" && $disk->lastModified($f) < $threshold) {
                $disk->delete($f);
                $deletedCount++;
            }
        }
        
        if ($deletedCount > 0) {
            $this->info("Menghapus {$deletedCount} file backup lama (> 14 hari).");
        }
    }
}
