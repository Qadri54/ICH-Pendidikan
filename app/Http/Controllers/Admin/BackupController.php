<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use ZipArchive;

class BackupController extends Controller
{
    private $backupDir = 'backups';

    public function index()
    {
        $disk = Storage::disk('local');
        
        $files = [];
        if ($disk->exists($this->backupDir)) {
            $files = $disk->files($this->backupDir);
        }

        $backups = [];
        foreach ($files as $f) {
            if (substr($f, -4) == ".zip") {
                $backups[] = [
                    "file_path" => $f,
                    "file_name" => str_replace($this->backupDir . "/", "", $f),
                    "file_size" => $this->humanFilesize($disk->size($f)),
                    "last_modified" => Carbon::createFromTimestamp($disk->lastModified($f))->translatedFormat("d M Y, H:i"),
                    "timestamp" => $disk->lastModified($f),
                ];
            }
        }
        
        $backups = collect($backups)->sortByDesc("timestamp")->values();
        
        $totalSize = $backups->sum(function ($b) use ($disk) { return $disk->size($b["file_path"]); });
        
        $stats = [
            "count" => $backups->count(),
            "size" => $this->humanFilesize($totalSize),
            "latest" => $backups->first()["last_modified"] ?? "Belum ada backup"
        ];
        
        return view("admin.backup.index", compact("backups", "stats"));
    }
    
    public function store()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('backup:run-custom');
            return back()->with("success", "Pencadangan (Database & Storage) berhasil diselesaikan!");
        } catch (\Exception $e) {
            return back()->withErrors(["error" => "Backup gagal: " . $e->getMessage()]);
        }
    }
    
    public function download(Request $request)
    {
        $file = $request->query("file");
        $disk = Storage::disk('local');
        
        if ($file && $disk->exists($this->backupDir . '/' . $file)) {
            return $disk->download($this->backupDir . '/' . $file);
        }
        return abort(404, "File tidak ditemukan.");
    }
    
    public function destroy(Request $request)
    {
        $file = $request->query("file");
        $disk = Storage::disk('local');
        
        if ($file && $disk->exists($this->backupDir . '/' . $file)) {
            $disk->delete($this->backupDir . '/' . $file);
            return back()->with("success", "File backup berhasil dihapus.");
        }
        return abort(404, "File tidak ditemukan.");
    }
    
    private function humanFilesize($bytes, $decimals = 2)
    {
        if ($bytes == 0) return "0 B";
        $size = array("B","KB","MB","GB","TB","PB","EB","ZB","YB");
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . " " . @$size[$factor];
    }
}
