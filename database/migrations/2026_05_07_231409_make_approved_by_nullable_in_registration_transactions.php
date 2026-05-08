<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_transactions', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->unsignedBigInteger('approved_by')->nullable()->change();
            $table->foreign('approved_by')->references('admin_id')->on('admins')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('registration_transactions', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->unsignedBigInteger('approved_by')->nullable(false)->change();
            $table->foreign('approved_by')->references('admin_id')->on('admins')->onDelete('cascade');
        });
    }
};
