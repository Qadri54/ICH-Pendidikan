<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Teacher;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = ClassRoom::with('homeroomTeacher.user')
            ->withCount('students')
            ->orderBy('nama_kelas')
            ->paginate(15);

        $guru = Teacher::with('user')->get();

        return view('admin.kelas.index', compact('kelas', 'guru'));
    }

    public function create()
    {
        $guru = Teacher::with('user')->get();
        return view('admin.kelas.create', compact('guru'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kelas'          => 'required|string|max:100|unique:classes,nama_kelas',
            'nama_ruangan'        => 'required|string|max:100',
            'homeroom_teacher_id' => 'nullable|exists:teachers,teacher_id',
        ]);

        ClassRoom::create($data);

        return redirect()->route('admin.kelas.index')
            ->with('success', "Kelas {$data['nama_kelas']} berhasil ditambahkan.");
    }

    public function edit(ClassRoom $kelas)
    {
        $guru = Teacher::with('user')->get();
        return view('admin.kelas.edit', compact('kelas', 'guru'));
    }

    public function update(Request $request, ClassRoom $kelas)
    {
        $data = $request->validate([
            'nama_kelas'          => "required|string|max:100|unique:classes,nama_kelas,{$kelas->class_id},class_id",
            'nama_ruangan'        => 'required|string|max:100',
            'homeroom_teacher_id' => 'nullable|exists:teachers,teacher_id',
        ]);

        $kelas->update($data);

        return redirect()->route('admin.kelas.index')
            ->with('success', "Kelas {$kelas->nama_kelas} berhasil diperbarui.");
    }

    public function destroy(ClassRoom $kelas)
    {
        if ($kelas->students()->exists()) {
            return redirect()->route('admin.kelas.index')
                ->with('error', "Kelas {$kelas->nama_kelas} tidak bisa dihapus karena masih memiliki siswa.");
        }

        $nama = $kelas->nama_kelas;
        $kelas->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', "Kelas {$nama} berhasil dihapus.");
    }
}
