<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\SavingLedger;
use App\Models\Student;
use App\Models\StudentPassbook;
use App\Models\Teacher;
use App\Services\Saving\PassbookService;
use App\Services\Saving\SavingLedgerService;
use App\Services\Saving\SavingTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TabunganGuruController extends Controller
{
    public function __construct(
        private SavingLedgerService $ledgerService,
        private PassbookService $passbookService,
        private SavingTransactionService $transactionService,
    ) {}

    public function index(): View
    {
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        $ledgers = $this->ledgerService->getByTeacher($teacher->teacher_id);

        return view('guru.tabungan.index', compact('ledgers'));
    }

    public function show(SavingLedger $ledger): View
    {
        $teacher = Teacher::with('homeroomClass')->where('user_id', auth()->id())->firstOrFail();
        abort_if($ledger->teacher_id !== $teacher->teacher_id, 403);

        $passbooks = $this->passbookService->getByLedger($ledger->ledger_id);

        $ledger->load('classRoom');
        $existingStudentIds = $ledger->passbooks()->pluck('student_id');
        $availableStudents = Student::with('classRoom')
            ->where('status', 'aktif')
            ->whereNotIn('student_id', $existingStudentIds)
            ->when($ledger->class_id, fn($q) => $q->where('class_id', $ledger->class_id))
            ->orderBy('nama_siswa')
            ->get();
        $classes = ClassRoom::orderBy('nama_kelas')->get();
        $homeroomClassId = $ledger->class_id ?? $teacher->homeroomClass?->class_id;

        return view('guru.tabungan.show', compact('ledger', 'passbooks', 'availableStudents', 'classes', 'homeroomClassId'));
    }

    public function storePassbook(Request $request, SavingLedger $ledger): RedirectResponse
    {
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        abort_if($ledger->teacher_id !== $teacher->teacher_id, 403);

        $data = $request->validate([
            'student_ids'     => 'required|array|min:1',
            'student_ids.*'   => 'integer|exists:students,student_id',
            'opening_date'    => 'required|date',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        try {
            $count = $this->passbookService->bulkOpen(
                $ledger->ledger_id,
                $data['student_ids'],
                $data['opening_date'],
                (int) ($data['opening_balance'] ?? 0)
            );
            return redirect()->route('guru.tabungan.show', $ledger)
                ->with('success', "{$count} buku tabungan berhasil dibuka.");
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showPassbook(StudentPassbook $passbook): View
    {
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        abort_if($passbook->ledger->teacher_id !== $teacher->teacher_id, 403);

        $passbook = $this->passbookService->getById($passbook->passbook_id);
        $transactions = $this->transactionService->getByPassbook($passbook->passbook_id);

        return view('guru.tabungan.passbook-show', compact('passbook', 'transactions'));
    }

    public function deposit(Request $request, StudentPassbook $passbook): RedirectResponse
    {
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        abort_if($passbook->ledger->teacher_id !== $teacher->teacher_id, 403);

        $data = $request->validate([
            'amount'           => 'required|integer|min:1000',
            'description'      => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        try {
            $this->transactionService->deposit($passbook->passbook_id, $data);
            return back()->with('success', 'Setoran berhasil dicatat.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function withdraw(Request $request, StudentPassbook $passbook): RedirectResponse
    {
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        abort_if($passbook->ledger->teacher_id !== $teacher->teacher_id, 403);

        $data = $request->validate([
            'amount'           => 'required|integer|min:1000',
            'description'      => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        try {
            $this->transactionService->withdraw($passbook->passbook_id, $data);
            return back()->with('success', 'Penarikan berhasil dicatat.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
