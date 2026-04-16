<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use Illuminate\Database\Seeder;

class ClassRoomSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['nama_kelas' => 'Kelas A', 'nama_ruangan' => 'Ruangan 1'],
            ['nama_kelas' => 'Kelas B', 'nama_ruangan' => 'Ruangan 2'],
            ['nama_kelas' => 'Kelas C', 'nama_ruangan' => 'Ruangan 3'],
        ];

        foreach ($classes as $class) {
            ClassRoom::create($class);
        }
    }
}
