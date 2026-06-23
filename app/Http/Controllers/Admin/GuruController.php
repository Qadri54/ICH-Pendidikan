<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller {
    public function index(Request $request) {
        $guru = Teacher::with('user')
            ->when($request->search, fn($q) =>
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"))
                    ->orWhere('NIP', 'like', "%{$request->search}%")
            )
            ->get()
            ->toBase()
            ->map(fn($t) => (object) [
                'id' => $t->teacher_id,
                'tipe' => $t->tipe,
                'nama' => $t->user?->name ?? '-',
                'NIP' => $t->NIP,
                'info' => '-',
                'model' => 'teacher',
                'no_hp' => $t->user?->no_hp ?? '',
                'hire_date' => $t->hire_date ?? '',
                'subject' => '',
                'email' => $t->user?->email ?? '',
            ])
            ->sortBy('nama')
            ->values();

        return view('admin.guru.index', compact('guru'));
    }

    public function create() {
        return view('admin.guru.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'tipe_guru' => 'required|in:Guru,Guru Ngaji',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'NIP' => 'nullable|string|max:50',
            'hire_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'no_hp' => $data['no_hp'],
                'password' => Hash::make($data['password']),
                'status' => 'active',
            ]);

            $user->role()->create(['role_name' => $data['tipe_guru']]);

            Teacher::create([
                'user_id' => $user->user_id,
                'NIP' => $data['NIP'],
                'tipe' => $data['tipe_guru'] === 'Guru' ? 'Guru TK' : 'Guru Ngaji',
                'hire_date' => $data['hire_date'],
            ]);
        });

        return redirect()->route('admin.guru.index')
            ->with('success', "Guru {$data['name']} berhasil ditambahkan.");
    }

    public function edit(string $id) {
        $teacher = Teacher::with('user')->findOrFail($id);
        return view('admin.guru.edit', ['guru' => $teacher, 'tipe' => $teacher->tipe === 'Guru Ngaji' ? 'Guru Ngaji' : 'Guru']);
    }

    public function update(Request $request, string $id) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'NIP' => 'nullable|string|max:50',
            'hire_date' => 'nullable|date',
        ]);

        $teacher = Teacher::findOrFail($id);
        $teacher->user->update(['name' => $data['name'], 'no_hp' => $data['no_hp']]);
        $teacher->update([
            'NIP' => $data['NIP'],
            'hire_date' => $data['hire_date'],
        ]);

        return redirect()->route('admin.guru.index')
            ->with('success', "Data guru berhasil diperbarui.");
    }

    public function destroy(string $id) {
        DB::transaction(function () use ($id) {
            $teacher = Teacher::findOrFail($id);
            $user = $teacher->user;

            $teacher->delete();
            $user?->role?->delete();
            $user?->delete();
        });

        return redirect()->route('admin.guru.index')
            ->with('success', "Data guru berhasil dihapus.");
    }
}
