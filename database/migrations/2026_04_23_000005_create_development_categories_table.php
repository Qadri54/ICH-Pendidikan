<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('nama');
            $table->integer('urutan')->default(0);
            $table->integer('usia_min')->default(5);
            $table->integer('usia_max')->default(6);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('category_id')->on('development_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_categories');
    }
};
