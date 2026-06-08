<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function create()
    {
        $kelas   = ClassRoom::orderBy('nama_kelas')->get();
        $parents = User::whereHas('role', fn($q) => $q->where('role_name', 'Orang Tua'))
            ->orderBy('name')
            ->get();

        return view('admin.siswa.create', compact('kelas', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_siswa'    => 'required|string|max:255',
            'NIS'           => 'nullable|string|max:50|unique:students,NIS',
            'class_id'      => 'required|exists:classes,class_id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir'  => 'required|string|max:255',
            'nama_ayah'     => 'required|string|max:255',
            'nama_ibu'      => 'required|string|max:255',
            'user_id'       => 'nullable|exists:users,user_id',
        ]);

        Student::create($data);

        return redirect()->route('admin.siswa.index')
            ->with('success', "Siswa {$data['nama_siswa']} berhasil ditambahkan.");
    }

    public function index(Request $request)
    {
        $siswa = Student::with('classRoom')
            ->when($request->search, fn($q) =>
                $q->where('nama_siswa', 'like', "%{$request->search}%")
                  ->orWhere('NIS', 'like', "%{$request->search}%")
            )
            ->when($request->kelas, fn($q) => $q->where('class_id', $request->kelas))
            ->orderBy('nama_siswa')
            ->paginate(15)->withQueryString();

        $kelas = ClassRoom::orderBy('nama_kelas')->get();
        $parents = User::whereHas('role', fn($q) => $q->where('role_name', 'Orang Tua'))
            ->orderBy('name')->get();

        return view('admin.siswa.index', compact('siswa', 'kelas', 'parents'));
    }

    public function show(Student $siswa)
    {
        $siswa->load('classRoom');
        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit(Student $siswa)
    {
        $kelas = ClassRoom::orderBy('nama_kelas')->get();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, Student $siswa)
    {
        $data = $request->validate([
            'nama_siswa'    => 'required|string|max:255',
            'NIS'           => "required|string|max:50|unique:students,NIS,{$siswa->student_id},student_id",
            'class_id'      => 'required|exists:classes,class_id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir'  => 'required|string|max:255',
            'nama_ayah'     => 'required|string|max:255',
            'nama_ibu'      => 'required|string|max:255',
        ]);

        $siswa->update($data);

        return redirect()->route('admin.siswa.index')
            ->with('success', "Data {$siswa->nama_siswa} berhasil diperbarui.");
    }

    public function destroy(Student $siswa)
    {
        $nama = $siswa->nama_siswa;
        $siswa->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', "Siswa {$nama} berhasil dihapus.");
    }
}
