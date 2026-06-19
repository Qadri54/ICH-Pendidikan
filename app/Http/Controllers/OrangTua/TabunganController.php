<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\Saving\PassbookService;
use App\Services\Saving\SavingTransactionService;
use App\Services\User\StudentProfileService;
use Illuminate\View\View;

class TabunganController extends Controller
{
    public function __construct(
        private StudentProfileService $studentService,
        private PassbookService $passbookService,
        private SavingTransactionService $transactionService,
    ) {}

    public function index(): View
    {
        $students = $this->studentService->getAllByUserId(auth()->id())->load('classRoom');

        $data = $students->map(function ($student) {
            $passbooks = $this->passbookService->getByStudent($student->student_id);
            $totalSaldo = $passbooks->sum('current_balance');
            $jumlahBuku = $passbooks->count();

            return [
                'student' => $student,
                'totalSaldo' => $totalSaldo,
                'jumlahBuku' => $jumlahBuku,
            ];
        });

        return view('orang-tua.tabungan.index', compact('data'));
    }

    public function detail(Student $student): View
    {
        abort_if($student->user_id !== auth()->id(), 403);

        $student->load('classRoom');

        $passbooks = $this->passbookService->getByStudent($student->student_id)
            ->map(fn ($pb) => [
                'passbook'     => $pb,
                'transactions' => $this->transactionService->getByPassbook($pb->passbook_id)->take(10),
            ]);

        return view('orang-tua.tabungan.detail', compact('student', 'passbooks'));
    }
}
