<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('NIP')->nullable()->change();
        });

        Schema::table('foundation_heads', function (Blueprint $table) {
            $table->string('NIP')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('NIP')->nullable(false)->change();
        });

        Schema::table('foundation_heads', function (Blueprint $table) {
            $table->string('NIP')->nullable(false)->change();
        });
    }
};
