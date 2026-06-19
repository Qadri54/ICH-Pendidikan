<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE attendance_records MODIFY attendance_status ENUM('Hadir', 'Izin', 'Sakit', 'Tanpa Keterangan') NOT NULL");
        DB::table('attendance_records')->where('attendance_status', 'Masuk')->update(['attendance_status' => 'Hadir']);
    }

    public function down(): void
    {
        DB::table('attendance_records')->where('attendance_status', 'Hadir')->update(['attendance_status' => 'Masuk']);
        DB::table('attendance_records')->where('attendance_status', 'Tanpa Keterangan')->delete();
        DB::statement("ALTER TABLE attendance_records MODIFY attendance_status ENUM('Masuk', 'Izin', 'Sakit') NOT NULL");
    }
};
