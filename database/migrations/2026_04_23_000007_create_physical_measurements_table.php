<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_measurements', function (Blueprint $table) {
            $table->id('measurement_id');
            $table->unsignedBigInteger('report_card_id');
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('lingkar_kepala', 5, 2)->nullable();
            $table->date('tanggal_ukur')->nullable();
            $table->timestamps();

            $table->unique('report_card_id');
            $table->foreign('report_card_id')->references('report_card_id')->on('student_report_cards')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_measurements');
    }
};
