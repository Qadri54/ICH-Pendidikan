<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id('whatsapp_setting_id');
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value')->nullable();
            $table->timestamps();
        });

        DB::table('whatsapp_settings')->insert([
            ['setting_key' => 'whatsapp_enabled', 'setting_value' => 'false', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'whatsapp_driver', 'setting_value' => 'fonnte', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'fonnte_token', 'setting_value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'self_hosted_url', 'setting_value' => 'http://localhost:3000', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_settings');
    }
};
