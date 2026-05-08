<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Services\User\StudentProfileService;
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
}
