<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_report_cards', function (Blueprint $table) {
            $table->id('report_card_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('homeroom_teacher_id')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'period_id']);
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('period_id')->references('period_id')->on('academic_periods')->onDelete('cascade');
            $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');
            $table->foreign('homeroom_teacher_id')->references('teacher_id')->on('teachers')->nullOnDelete();
            $table->foreign('approved_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('student_report_cards');
    }
};
