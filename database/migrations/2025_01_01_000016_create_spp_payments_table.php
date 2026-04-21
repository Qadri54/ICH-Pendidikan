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
        Schema::create('spp_payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('payment_date');
            $table->integer('jumlah_bayar');
            $table->string('nama_bank');
            $table->string('gambar_bukti_pembayaran');
            $table->enum('status', ['unpaid', 'paid', 'pending', 'cancelled', 'overdue'])->default('pending');
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('invoice_id')->references('invoice_id')->on('spp_invoices')->onDelete('cascade');
            $table->foreign('approved_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spp_payments');
    }
};
