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
        Schema::create('saving_ledgers', function (Blueprint $table) {
            $table->id('ledger_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('ledger_name');
            $table->date('academic_year');
            $table->date('opening_date');
            $table->integer('opening_balance')->default(0);
            $table->integer('total_balance')->default(0);
            $table->enum('status', ['Active', 'Closed'])->default('Active');
            $table->timestamps();

            $table->foreign('teacher_id')->references('teacher_id')->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saving_ledgers');
    }
};
