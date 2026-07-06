<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saving_ledgers', function (Blueprint $table) {
            $table->unsignedBigInteger('period_id')->nullable()->after('teacher_id');
            $table->foreign('period_id')->references('period_id')->on('academic_periods')->nullOnDelete();
        });

        Schema::table('saving_ledgers', function (Blueprint $table) {
            $table->dropColumn('academic_year');
        });
    }

    public function down(): void
    {
        Schema::table('saving_ledgers', function (Blueprint $table) {
            $table->date('academic_year')->nullable()->after('ledger_name');
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');
        });
    }
};
