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
        Schema::create('saving_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('ledger_id');
            $table->unsignedBigInteger('passbook_id');
            $table->dateTime('transaction_date');
            $table->string('transaction_type');
            $table->integer('amount');
            $table->string('description')->nullable();
            $table->string('transaction_number')->unique();
            $table->dateTime('last_update')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('ledger_id')->references('ledger_id')->on('saving_ledgers')->onDelete('cascade');
            $table->foreign('passbook_id')->references('passbook_id')->on('student_passbooks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saving_transactions');
    }
};
