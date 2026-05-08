<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE roles MODIFY COLUMN role_name ENUM(
            'Admin',
            'Guest',
            'Student',
            'Guru',
            'Guru Ngaji',
            'Kepala Sekolah',
            'Kepala Yayasan',
            'Orang Tua'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE roles MODIFY COLUMN role_name ENUM(
            'Admin',
            'Guest',
            'Student',
            'Guru',
            'Guru Ngaji',
            'Kepala Sekolah',
            'Kepala Yayasan'
        ) NOT NULL");
    }
};
