<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_attendance', function (Blueprint $table) {
            // Mencatat guru yang menginput absensi siswa.
            // Nullable karena data lama tidak memiliki informasi ini.
            // onDelete set null agar rekaman absensi tidak hilang jika guru dihapus.
            $table->unsignedBigInteger('teacher_id')->nullable()->after('student_id');
            $table->foreign('teacher_id')->references('teacher_id')->on('teachers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('student_attendance', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }
};
