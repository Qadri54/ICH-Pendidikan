<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id('installment_id');
            $table->unsignedBigInteger('registration_fee_id');
            $table->integer('jumlah');
            $table->dateTime('tanggal_jatuh_tempo');
            $table->enum('status', ['unpaid', 'paid', 'overdue'])->default('unpaid');
            $table->timestamps();

            $table->foreign('registration_fee_id')->references('registration_fee_id')->on('registration_fees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('fee_installments');
    }
};
