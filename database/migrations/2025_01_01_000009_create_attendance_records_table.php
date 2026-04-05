<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id('attendance_record_id');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedBigInteger('religious_teacher_id')->nullable();
            $table->dateTime('check_in_time')->nullable();
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->string('check_in_accuracy')->nullable();
            $table->enum('is_within_geofence', ['ya', 'tidak'])->nullable();
            $table->enum('attendance_status', ['Masuk', 'Izin', 'Sakit']);
            $table->dateTime('check_out_time')->nullable();
            $table->decimal('check_out_latitude', 10, 7)->nullable();
            $table->decimal('check_out_longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')->references('teacher_id')->on('teachers')->onDelete('set null');
            $table->foreign('religious_teacher_id')->references('religious_teacher_id')->on('religious_teachers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
