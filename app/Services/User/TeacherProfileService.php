<?php

namespace App\Services\User;

use App\Models\Teacher;

class TeacherProfileService
{
    public function createProfile(int $userId, array $data): Teacher
    {
        return Teacher::create([
            'user_id'   => $userId,
            'NIP'       => $data['NIP'],
            'tipe'      => $data['tipe'],
            'hire_date' => $data['hire_date'],
        ]);
    }

    public function updateProfile(int $userId, array $data): bool
    {
        return Teacher::where('user_id', $userId)->update([
            'NIP'       => $data['NIP'],
            'tipe'      => $data['tipe'],
            'hire_date' => $data['hire_date'],
        ]);
    }

    public function deleteProfile(int $userId): bool
    {
        return Teacher::where('user_id', $userId)->delete();
    }
}
