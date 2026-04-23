<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('narrative_photos', function (Blueprint $table) {
            $table->id('photo_id');
            $table->unsignedBigInteger('narrative_id');
            $table->string('photo_path');
            $table->string('caption')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->foreign('narrative_id')->references('narrative_id')->on('narrative_assessments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('narrative_photos');
    }
};
