<?php

namespace App\Services\User;

use App\Models\Admin;

class AdminProfileService
{
    public function createProfile(int $userId, array $data): Admin
    {
        return Admin::create([
            'user_id' => $userId,
            'NIP'     => $data['NIP'],
        ]);
    }

    public function updateProfile(int $userId, array $data): bool
    {
        return Admin::where('user_id', $userId)->update([
            'NIP' => $data['NIP'],
        ]);
    }

    public function deleteProfile(int $userId): bool
    {
        return Admin::where('user_id', $userId)->delete();
    }
}
