<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_conditions', function (Blueprint $table) {
            $table->id('health_id');
            $table->unsignedBigInteger('report_card_id');
            $table->string('pendengaran');
            $table->string('penglihatan');
            $table->text('catatan_tambahan')->nullable();
            $table->timestamps();

            $table->unique('report_card_id');
            $table->foreign('report_card_id')->references('report_card_id')->on('student_report_cards')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_conditions');
    }
};
