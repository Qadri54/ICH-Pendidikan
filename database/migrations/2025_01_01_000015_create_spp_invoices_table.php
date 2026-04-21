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
        Schema::create('spp_invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->unsignedBigInteger('student_id');
            $table->date('tanggal_tahun');
            $table->integer('jumlah');
            $table->date('jatuh_tempo');
            $table->enum('status', ['unpaid', 'paid', 'pending', 'cancelled', 'overdue'])->default('unpaid');
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spp_invoices');
    }
};
