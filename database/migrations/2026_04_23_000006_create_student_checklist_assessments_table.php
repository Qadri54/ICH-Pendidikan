<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_checklist_assessments', function (Blueprint $table) {
            $table->id('checklist_id');
            $table->unsignedBigInteger('report_card_id');
            $table->unsignedBigInteger('category_id');
            $table->enum('status', ['BM', 'MM', 'SM'])->default('BM');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['report_card_id', 'category_id']);
            $table->foreign('report_card_id')->references('report_card_id')->on('student_report_cards')->onDelete('cascade');
            $table->foreign('category_id')->references('category_id')->on('development_categories')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_checklist_assessments');
    }
};
