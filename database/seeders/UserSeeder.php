<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Services\User\UserService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {
    public function __construct(
        private UserService $userService,
    ) {
    }

    public function run(): void {
        // Admin
        $this->userService->createUser([
            'name' => 'Admin IQRA',
            'email' => 'admin@iqra.com',
            'no_hp' => '08123456701',
            'password' => 'password123',
            'status' => 'active',
            'role_name' => 'Admin',
            'NIP' => 'NIP001',
        ]);

        // Guru Kelas — Sofia Aurora Susanto, S.Pd
        $sofia = $this->userService->createUser([
            'name' => 'Sofia Aurora Susanto',
            'email' => 'guru@iqra.com',
            'no_hp' => '08123456702',
            'password' => 'password123',
            'status' => 'active',
            'role_name' => 'Guru',
            'NIP' => 'NIP002',
            'tipe' => 'Guru TK',
            'hire_date' => '2023-01-01',
        ]);

        // Guru Kelas — Lisma Farida Pane, S.Pd
        $lisma = $this->userService->createUser([
            'name' => 'Lisma Farida Pane',
            'email' => 'lisma.pane@iqra.com',
            'no_hp' => '08123456708',
            'password' => 'password123',
            'status' => 'active',
            'role_name' => 'Guru',
            'NIP' => 'NIP006',
            'tipe' => 'Guru TK',
            'hire_date' => '2023-01-01',
        ]);

        // Guru Ngaji
        $this->userService->createUser([
            'name' => 'Guru Ngaji Satu',
            'email' => 'guruNgaji@iqra.com',
            'no_hp' => '08123456703',
            'password' => 'password123',
            'status' => 'active',
            'role_name' => 'Guru Ngaji',
            'NIP' => 'NIP003',
            'tipe' => 'Guru Ngaji',
            'hire_date' => '2023-01-01',
        ]);

        // Kepala Sekolah — Adli Qarin, S.S. M.Ikom
        $this->userService->createUser([
            'name' => 'Adli Qarin',
            'email' => 'kepsek@iqra.com',
            'no_hp' => '08123456704',
            'password' => 'password123',
            'status' => 'active',
            'role_name' => 'Kepala Sekolah',
            'NIP' => 'NIP004',
        ]);

        // Kepala Yayasan
        $this->userService->createUser([
            'name' => 'Kepala Yayasan IQRA',
            'email' => 'yayasan@iqra.com',
            'no_hp' => '08123456705',
            'password' => 'password123',
            'status' => 'active',
            'role_name' => 'Kepala Yayasan',
            'NIP' => 'NIP005',
        ]);

        // Guest
        $this->userService->createUser([
            'name' => 'Guest IQRA',
            'email' => 'guest@iqra.com',
            'no_hp' => '08123456706',
            'password' => 'password123',
            'status' => 'active',
            'role_name' => 'Guest',
        ]);

        // Student
        $this->userService->createUser([
            'name' => 'Siswa Satu',
            'email' => 'siswa@iqra.com',
            'no_hp' => '08123456707',
            'password' => 'password123',
            'status' => 'active',
            'role_name' => 'Student',
            'NIS' => 'NIS001',
            'nama_siswa' => 'Siswa Satu',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2020-01-01',
            'tempat_lahir' => 'Medan',
            'nama_ayah' => 'Ayah Satu',
            'nama_ibu' => 'Ibu Satu',
            'class_id' => 1,
        ]);

        // Assign wali kelas
        ClassRoom::where('class_id', 1)->update([
            'homeroom_teacher_id' => $lisma->teacher->teacher_id,
        ]);
        ClassRoom::where('class_id', 2)->update([
            'homeroom_teacher_id' => $sofia->teacher->teacher_id,
        ]);
    }
}
