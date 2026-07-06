<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saving_ledgers', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable()->after('teacher_id');
            $table->foreign('class_id')->references('class_id')->on('classes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('saving_ledgers', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
        });
    }
};
