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
        Schema::create('student_passbooks', function (Blueprint $table) {
            $table->id('passbook_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('ledger_id');
            $table->date('opening_date');
            $table->integer('opening_balance')->default(0);
            $table->integer('current_balance')->default(0);
            $table->string('passbook_file')->nullable();
            $table->dateTime('last_update')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('ledger_id')->references('ledger_id')->on('saving_ledgers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_passbooks');
    }
};
