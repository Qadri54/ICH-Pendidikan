<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('registration_fees', function (Blueprint $table) {
            $table->id('registration_fee_id');
            $table->unsignedBigInteger('student_id');
            $table->integer('total_jumlah');
            $table->enum('status', ['unpaid', 'installments', 'paid'])->default('unpaid');
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('registration_fees');
    }
};
