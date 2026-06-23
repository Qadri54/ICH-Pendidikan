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

            $table->dateTime('check_in_time')->nullable();
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->string('check_in_accuracy')->nullable();
            $table->enum('is_within_geofence', ['ya', 'tidak'])->nullable();
            $table->enum('attendance_status', ['Hadir', 'Izin', 'Sakit', 'Tanpa Keterangan']);
            $table->timestamps();

            $table->foreign('teacher_id')->references('teacher_id')->on('teachers')->onDelete('set null');

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
