<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        Schema::disableForeignKeyConstraints();

        $this->call([
            ClassRoomSeeder::class,
            UserSeeder::class,
            ParentStudentSeeder::class,
            AcademicPeriodSeeder::class,
            DevelopmentCategorySeeder::class,
            ReportCardSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
