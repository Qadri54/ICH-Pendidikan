<?php

namespace App\Services\User;

use App\Models\Teacher;

class TeacherProfileService
{
    public function createProfile(int $userId, array $data): Teacher
    {
        return Teacher::create([
            'user_id'   => $userId,
            'NIP'       => $data['NIP'] ?? null,
            'tipe'      => $data['tipe'] ?? (($data['role_name'] ?? '') === 'Guru Ngaji' ? 'Guru Ngaji' : 'Guru TK'),
            'hire_date' => $data['hire_date'] ?? now()->toDateString(),
        ]);
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $teacher = Teacher::where('user_id', $userId)->first();
        if (!$teacher) return false;

        $update = [];
        if (isset($data['NIP']))       $update['NIP']       = $data['NIP'];
        if (isset($data['tipe']))      $update['tipe']      = $data['tipe'];
        if (isset($data['hire_date'])) $update['hire_date'] = $data['hire_date'];

        return empty($update) ? true : $teacher->update($update);
    }

    public function deleteProfile(int $userId): bool
    {
        return Teacher::where('user_id', $userId)->delete();
    }
}
