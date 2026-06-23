<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private AdminProfileService $adminProfileService,
        private TeacherProfileService $teacherProfileService,
        private StudentProfileService $studentProfileService,
        private FoundationHeadProfileService $foundationHeadProfileService,
    ) {}

    public function getAllUsers()
    {
        return User::with('role')->get();
    }

    public function getPaginated(?string $search, ?string $role = null, int $perPage = 15)
    {
        return User::with('role')
            ->when($search, fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            )
            ->when($role, fn($q) =>
                $q->whereHas('role', fn($r) => $r->where('role_name', $role))
            )
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getUserById(int $id)
    {
        return User::with('role')->findOrFail($id);
    }

    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'no_hp'    => $data['no_hp'],
                'password' => Hash::make($data['password']),
                'status'   => $data['status'],
            ]);

            Role::create([
                'user_id'   => $user->user_id,
                'role_name' => $data['role_name'],
            ]);

            $this->createProfileByRole($user->user_id, $data['role_name'], $data);

            return $user;
        });
    }

    public function updateUser(int $id, array $data): User
    {
        return DB::transaction(function () use ($id, $data) {
            $user = User::findOrFail($id);

            $user->update([
                'name'   => $data['name'],
                'email'  => $data['email'],
                'no_hp'  => $data['no_hp'],
                'status' => $data['status'],
            ]);

            if (isset($data['password'])) {
                $user->update(['password' => Hash::make($data['password'])]);
            }

            $currentRole = $user->role->role_name;
            $newRole     = $data['role_name'];

            if ($currentRole !== $newRole) {
                $this->deleteProfileByRole($user->user_id, $currentRole);
                $user->role->update(['role_name' => $newRole]);
                $this->createProfileByRole($user->user_id, $newRole, $data);
            } else {
                $this->updateProfileByRole($user->user_id, $currentRole, $data);
            }

            return $user;
        });
    }

    public function deleteUser(int $id): bool
    {
        return User::findOrFail($id)->delete();
    }

    private function createProfileByRole(int $userId, string $role, array $data): void
    {
        match ($role) {
            'Admin'           => $this->adminProfileService->createProfile($userId, $data),
            'Guru'            => $this->teacherProfileService->createProfile($userId, $data),
            'Guru Ngaji'      => $this->teacherProfileService->createProfile($userId, $data),
            'Student'         => $this->studentProfileService->createProfile($userId, $data),
            'Kepala Sekolah',
            'Kepala Yayasan'  => $this->foundationHeadProfileService->createProfile($userId, $data),
            default           => null,
        };
    }

    private function updateProfileByRole(int $userId, string $role, array $data): void
    {
        match ($role) {
            'Admin'           => $this->adminProfileService->updateProfile($userId, $data),
            'Guru'            => $this->teacherProfileService->updateProfile($userId, $data),
            'Guru Ngaji'      => $this->teacherProfileService->updateProfile($userId, $data),
            'Student'         => $this->studentProfileService->updateProfile($userId, $data),
            'Kepala Sekolah',
            'Kepala Yayasan'  => $this->foundationHeadProfileService->updateProfile($userId, $data),
            default           => null,
        };
    }

    private function deleteProfileByRole(int $userId, string $role): void
    {
        match ($role) {
            'Admin'           => $this->adminProfileService->deleteProfile($userId),
            'Guru'            => $this->teacherProfileService->deleteProfile($userId),
            'Guru Ngaji'      => $this->teacherProfileService->deleteProfile($userId),
            'Student'         => $this->studentProfileService->deleteProfile($userId),
            'Kepala Sekolah',
            'Kepala Yayasan'  => $this->foundationHeadProfileService->deleteProfile($userId),
            default           => null,
        };
    }
}
