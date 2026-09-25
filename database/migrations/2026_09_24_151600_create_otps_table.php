<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id('otp_id');
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();
            $table->string('otp_code', 6);
            $table->unsignedTinyInteger('send_count')->default(1);
            $table->timestamp('send_window_started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
