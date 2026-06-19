<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id('registration_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('jenis_pendaftaran', ['TK', 'Mengaji'])->default('TK');

            // Biodata siswa
            $table->string('nama_siswa');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->enum('ukuran_baju', ['S', 'M', 'L'])->nullable(); // hanya TK

            // Biodata ayah
            $table->string('nama_ayah');
            $table->string('tempat_lahir_ayah')->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();
            $table->text('alamat_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('no_telp_ayah', 20)->nullable();

            // Biodata ibu
            $table->string('nama_ibu');
            $table->string('tempat_lahir_ibu')->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->text('alamat_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('no_telp_ibu', 20)->nullable();

            $table->enum('status', ['accepted', 'rejected', 'pending'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('registrations');
    }
};
