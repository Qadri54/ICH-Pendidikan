<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\User\StudentProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilAnakController extends Controller
{
    public function __construct(private StudentProfileService $studentService) {}

    public function index(): View
    {
        $students = $this->studentService->getAllByUserId(auth()->id())
            ->load('classRoom');

        return view('orang-tua.profil-anak.index', compact('students'));
    }

    public function detail(Student $student): View
    {
        abort_if($student->user_id !== auth()->id(), 403);

        $student->load('classRoom');

        return view('orang-tua.profil-anak.detail', compact('student'));
    }

    public function updateFoto(Request $request, Student $student): RedirectResponse
    {
        abort_if($student->user_id !== auth()->id(), 403);

        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($student->foto) {
            Storage::disk('public')->delete($student->foto);
        }

        $path = $request->file('foto')->store('foto-siswa', 'public');
        $student->update(['foto' => $path]);

        return redirect()->route('profil-anak.detail', $student->student_id)
            ->with('success', 'Foto profil berhasil diperbarui.');
    }
}
