<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            // Kolom selfie_path menyimpan path file foto guru saat check-in
            // Diletakkan setelah check_in_accuracy agar urutannya logis
            $table->string('selfie_path')->nullable()->after('check_in_accuracy');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn('selfie_path');
        });
    }
};
