<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoInsertStudentAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-insert-students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically insert "Hadir" status for active students who have no attendance record for today.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        // Dapatkan semua siswa aktif
        $activeStudents = Student::where('status', 'Aktif')->get();
        
        if ($activeStudents->isEmpty()) {
            $this->info('Tidak ada siswa aktif.');
            return;
        }

        $now = now()->toDateTimeString();
        $recordsToInsert = [];
        $count = 0;

        foreach ($activeStudents as $student) {
            // Cek apakah siswa sudah punya record absensi hari ini
            $hasRecord = StudentAttendance::where('student_id', $student->student_id)
                ->whereDate('created_at', $today)
                ->exists();

            if (! $hasRecord) {
                $recordsToInsert[] = [
                    'student_id'      => $student->student_id,
                    'teacher_id'      => null, // Sistem yang mengisi
                    'status'          => 'hadir',
                    'keterangan_izin' => null,
                    'created_at'      => $now,
                ];
                $count++;
            }
        }

        if (!empty($recordsToInsert)) {
            // Chunk insert to avoid too large query if many students
            $chunks = array_chunk($recordsToInsert, 500);
            foreach ($chunks as $chunk) {
                StudentAttendance::insert($chunk);
            }
            
            $this->info("Berhasil menambahkan {$count} data absensi 'Hadir' otomatis.");
            Log::info("AutoInsertStudentAttendance: {$count} records inserted.");
        } else {
            $this->info('Semua siswa aktif sudah memiliki record absensi hari ini.');
        }
    }
}
