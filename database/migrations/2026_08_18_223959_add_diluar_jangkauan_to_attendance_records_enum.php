<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE attendance_records MODIFY attendance_status ENUM('Hadir', 'Izin', 'Sakit', 'Tanpa Keterangan', 'Diluar Jangkauan') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_records_enum', function (Blueprint $table) {
            //
        });
    }
};
