<?php

namespace App\Services\User;

use App\Models\FoundationHead;

class FoundationHeadProfileService {
    public function createProfile(int $userId, array $data): FoundationHead {
        return FoundationHead::create([
            'user_id' => $userId,
            'NIP' => $data['NIP'],
        ]);
    }

    public function updateProfile(int $userId, array $data): bool {
        return FoundationHead::where('user_id', $userId)->update([
            'NIP' => $data['NIP'],
        ]);
    }

    public function deleteProfile(int $userId): bool {
        return FoundationHead::where('user_id', $userId)->delete();
    }
}
