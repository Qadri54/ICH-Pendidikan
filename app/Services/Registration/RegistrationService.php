<?php

namespace App\Services\Registration;

use App\Models\Registration;
use App\Services\User\UserService;
use App\Services\User\StudentProfileService;
use Illuminate\Support\Facades\DB;

class RegistrationService {
    public function __construct(
        private UserService $userService,
        private StudentProfileService $studentProfileService,
        private RegistrationFeeService $registrationFeeService,
    ) {
    }

    public function register(array $data): Registration {
        return DB::transaction(function () use ($data) {
            $user = $this->userService->createUser([
                'name' => $data['name'],
                'email' => $data['email'],
                'no_hp' => $data['no_hp'],
                'password' => $data['password'],
                'status' => 'active',
                'role_name' => 'Guest',
            ]);

            return Registration::create([
                'user_id' => $user->user_id,
                'tanggal_lahir' => $data['tanggal_lahir'],
                'tempat_lahir' => $data['tempat_lahir'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'nama_ayah' => $data['nama_ayah'],
                'nama_ibu' => $data['nama_ibu'],
                'alamat' => $data['alamat'],
                'status' => 'pending',
            ]);
        });
    }

    public function getAll() {
        return Registration::with(['user', 'user.role'])->get();
    }

    public function getPaginated(?string $search, ?string $status, int $perPage = 15)
    {
        return Registration::with('user')
            ->when($search, fn($q) =>
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
            )
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getById(int $id): Registration {
        return Registration::with(['user', 'user.role'])->findOrFail($id);
    }

    public function approve(int $id): Registration {
        return DB::transaction(function () use ($id) {
            $registration = Registration::with('user')->findOrFail($id);

            $registration->update(['status' => 'accepted']);

            $user = $this->userService->updateUser($registration->user_id, [
                'name' => $registration->user->name,
                'email' => $registration->user->email,
                'no_hp' => $registration->user->no_hp,
                'status' => $registration->user->status,
                'role_name' => 'Student',
                'NIS' => null,
                'nama_siswa' => $registration->user->name,
                'jenis_kelamin' => $registration->jenis_kelamin,
                'tanggal_lahir' => $registration->tanggal_lahir,
                'tempat_lahir' => $registration->tempat_lahir,
                'nama_ayah' => $registration->nama_ayah,
                'nama_ibu' => $registration->nama_ibu,
                'class_id' => null,
            ]);

            $student = $user->student;

            $this->registrationFeeService->createFee($student->student_id);

            return $registration;
        });
    }
    public function reject(int $id): Registration {
        $registration = Registration::findOrFail($id);
        $registration->update(['status' => 'rejected']);

        return $registration;
    }
}
