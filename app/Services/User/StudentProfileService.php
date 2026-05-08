<?php

namespace App\Services\User;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

class StudentProfileService {
    public function getAllByUserId(int $userId): Collection
    {
        return Student::where('user_id', $userId)->get();
    }

    public function createProfile(int $userId, array $data): Student {
        return Student::create([
            'user_id' => $userId,
            'NIS' => $data['NIS'],
            'nama_siswa' => $data['nama_siswa'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'tempat_lahir' => $data['tempat_lahir'],
            'nama_ayah' => $data['nama_ayah'],
            'nama_ibu' => $data['nama_ibu'],
            'class_id' => $data['class_id'],
        ]);
    }

    public function updateProfile(int $userId, array $data): bool {
        return Student::where('user_id', $userId)->update([
            'NIS' => $data['NIS'],
            'nama_siswa' => $data['nama_siswa'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'tempat_lahir' => $data['tempat_lahir'],
            'nama_ayah' => $data['nama_ayah'],
            'nama_ibu' => $data['nama_ibu'],
            'class_id' => $data['class_id'],
        ]);
    }

    public function deleteProfile(int $userId): bool {
        return Student::where('user_id', $userId)->delete();
    }
}
