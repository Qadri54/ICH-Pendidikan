<?php

namespace App\Services\User;

use App\Models\ReligiousTeacher;

class ReligiousTeacherProfileService {
    public function createProfile(int $userId, array $data): ReligiousTeacher {
        return ReligiousTeacher::create([
            'user_id' => $userId,
            'NIP' => $data['NIP'],
            'tipe' => $data['tipe'],
            'hire_date' => $data['hire_date'],
        ]);
    }

    public function updateProfile(int $userId, array $data): bool {
        return ReligiousTeacher::where('user_id', $userId)->update([
            'NIP' => $data['NIP'],
            'tipe' => $data['tipe'],
            'hire_date' => $data['hire_date'],
        ]);
    }

    public function deleteProfile(int $userId): bool {
        return ReligiousTeacher::where('user_id', $userId)->delete();
    }
}
