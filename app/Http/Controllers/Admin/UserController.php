<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\Request;

class UserController extends Controller {
    public function __construct(private UserService $userService) {
    }

    public function index(Request $request) {
        $roles = ['Admin', 'Kepala Sekolah', 'Kepala Yayasan', 'Guru', 'Guru Ngaji', 'Orang Tua'];
        $users = $this->userService->getPaginated($request->search, $request->role);

        return view('admin.user.index', compact('users', 'roles'));
    }

    public function create() {
        $roles = ['Admin', 'Kepala Sekolah', 'Kepala Yayasan', 'Orang Tua'];
        return view('admin.user.create', compact('roles'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role_name' => 'required|in:Admin,Kepala Sekolah,Kepala Yayasan,Orang Tua',
            'NIP' => 'nullable|string|max:50|unique:admins,NIP|unique:foundation_heads,NIP',
        ]);

        $user = $this->userService->createUser([
            ...$data,
            'status' => 'active',
        ]);

        return redirect()->route('admin.user.index')
            ->with('success', "User {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user) {
        $roles = ['Admin', 'Kepala Sekolah', 'Kepala Yayasan', 'Orang Tua'];
        return view('admin.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->user_id},user_id",
            'no_hp' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role_name' => 'required|in:Admin,Kepala Sekolah,Kepala Yayasan,Orang Tua',
            'NIP' => 'nullable|string|max:50',
        ]);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'],
            'role_name' => $data['role_name'],
            'status' => $user->status,
        ];

        if (!empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $this->userService->updateUser($user->user_id, $payload);

        return redirect()->route('admin.user.index')
            ->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user) {
        if ($user->user_id === auth()->id()) {
            return redirect()->route('admin.user.index')
                ->with('error', "Tidak bisa menghapus akun sendiri.");
        }

        $nama = $user->name;
        $this->userService->deleteUser($user->user_id);

        return redirect()->route('admin.user.index')
            ->with('success', "User {$nama} berhasil dihapus.");
    }
}
