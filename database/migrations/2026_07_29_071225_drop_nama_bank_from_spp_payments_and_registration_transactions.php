<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spp_payments', function (Blueprint $table) {
            $table->dropColumn('nama_bank');
        });

        Schema::table('registration_transactions', function (Blueprint $table) {
            $table->dropColumn('nama_bank');
        });
    }

    public function down(): void
    {
        Schema::table('spp_payments', function (Blueprint $table) {
            $table->string('nama_bank', 100)->nullable()->after('jumlah_bayar');
        });

        Schema::table('registration_transactions', function (Blueprint $table) {
            $table->string('nama_bank', 100)->nullable()->after('jumlah_bayar');
        });
    }
};
