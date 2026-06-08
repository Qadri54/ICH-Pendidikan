<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE student_attendance MODIFY status ENUM('hadir', 'izin', 'sakit', 'tanpa keterangan') NOT NULL DEFAULT 'tanpa keterangan'");
    }

    public function down(): void
    {
        DB::table('student_attendance')->where('status', 'hadir')->delete();
        DB::statement("ALTER TABLE student_attendance MODIFY status ENUM('izin', 'sakit', 'tanpa keterangan') NOT NULL DEFAULT 'tanpa keterangan'");
    }
};
