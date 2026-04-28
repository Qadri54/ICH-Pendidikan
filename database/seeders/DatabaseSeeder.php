<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            ClassRoomSeeder::class,
            UserSeeder::class,
            AcademicPeriodSeeder::class,
            DevelopmentCategorySeeder::class,
        ]);
    }
}
