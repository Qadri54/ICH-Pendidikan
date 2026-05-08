<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
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
        $students = $this->studentService->getAllByUserId(auth()->id());

        $tabunganData = $students->map(fn ($student) => [
            'student'   => $student,
            'passbooks' => $this->passbookService->getByStudent($student->student_id)
                ->map(fn ($pb) => [
                    'passbook'     => $pb,
                    'transactions' => $this->transactionService->getByPassbook($pb->passbook_id)->take(10),
                ]),
        ]);

        return view('orang-tua.tabungan.index', compact('tabunganData'));
    }
}
