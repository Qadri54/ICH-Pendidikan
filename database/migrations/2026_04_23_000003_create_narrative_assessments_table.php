<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('narrative_assessments', function (Blueprint $table) {
            $table->id('narrative_id');
            $table->unsignedBigInteger('report_card_id');
            $table->enum('kategori', ['intrakurikuler', 'kokurikuler']);
            $table->string('judul');
            $table->text('isi_naratif');
            $table->timestamps();

            $table->unique(['report_card_id', 'judul']);
            $table->foreign('report_card_id')->references('report_card_id')->on('student_report_cards')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('narrative_assessments');
    }
};
