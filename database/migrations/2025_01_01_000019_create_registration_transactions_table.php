<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('registration_transactions', function (Blueprint $table) {
            $table->id('registration_transaction_id');
            $table->unsignedBigInteger('registration_fee_id');
            $table->unsignedBigInteger('approved_by');
            $table->dateTime('payment_date');
            $table->integer('jumlah_bayar');
            $table->string('nama_bank')->nullable();
            $table->string('gambar_bukti_pembayaran');
            $table->enum('payment_category', ['full', 'installment'])->default('installment');
            $table->enum('status', ['approved', 'rejected', 'pending'])->default('pending');
            $table->timestamps();

            $table->foreign('registration_fee_id')->references('registration_fee_id')->on('registration_fees')->onDelete('cascade');
            $table->foreign('approved_by')->references('admin_id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('registration_transactions');
    }
};
