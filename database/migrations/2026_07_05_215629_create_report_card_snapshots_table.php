<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_card_snapshots', function (Blueprint $table) {
            $table->id('snapshot_id');
            $table->foreignId('report_card_id')
                  ->unique()
                  ->constrained('student_report_cards', 'report_card_id')
                  ->cascadeOnDelete();
            $table->json('snapshot_data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_card_snapshots');
    }
};
