<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('verified')->default(false)->after('email_verified_at');
            $table->index(['verified', 'created_at'], 'users_verified_created_at_index');
        });

        // Pastikan seluruh user yang sudah ada di database tetap terverifikasi
        DB::table('users')->update(['verified' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_verified_created_at_index');
            $table->dropColumn('verified');
        });
    }
};
