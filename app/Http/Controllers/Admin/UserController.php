<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('role')
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
            )
            ->orderBy('name')
            ->paginate(15)->withQueryString();

        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        $roles = ['Admin', 'Guru', 'Guru Ngaji', 'Kepala Sekolah', 'Kepala Yayasan', 'Orang Tua'];
        return view('admin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'no_hp'     => 'required|string|max:20',
            'password'  => 'required|string|min:8|confirmed',
            'role_name' => 'required|string',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'no_hp'    => $data['no_hp'],
            'password' => Hash::make($data['password']),
        ]);

        $user->role()->create(['role_name' => $data['role_name']]);

        return redirect()->route('admin.user.index')
            ->with('success', "User {$data['name']} berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        $roles = ['Admin', 'Guru', 'Guru Ngaji', 'Kepala Sekolah', 'Kepala Yayasan', 'Orang Tua'];
        return view('admin.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => "required|email|unique:users,email,{$user->user_id},user_id",
            'no_hp'     => 'required|string|max:20',
            'password'  => 'nullable|string|min:8|confirmed',
            'role_name' => 'required|string',
        ]);

        $user->update(array_filter([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'no_hp'    => $data['no_hp'],
            'password' => $data['password'] ? Hash::make($data['password']) : null,
        ]));

        $user->role()->updateOrCreate(
            ['user_id' => $user->user_id],
            ['role_name' => $data['role_name']]
        );

        return redirect()->route('admin.user.index')
            ->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->user_id === auth()->id()) {
            return redirect()->route('admin.user.index')
                ->with('error', "Tidak bisa menghapus akun sendiri.");
        }

        $nama = $user->name;
        $user->role()->delete();
        $user->delete();

        return redirect()->route('admin.user.index')
            ->with('success', "User {$nama} berhasil dihapus.");
    }
}
