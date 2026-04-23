<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id('student_attendance_id');
            $table->unsignedBigInteger('student_id');
            $table->enum('status', ['izin', 'sakit', 'tanpa keterangan']);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('student_attendance');
    }
};
