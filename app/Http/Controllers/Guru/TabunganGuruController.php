<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\SavingLedger;
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
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        abort_if($ledger->teacher_id !== $teacher->teacher_id, 403);

        $passbooks = $this->passbookService->getByLedger($ledger->ledger_id);

        return view('guru.tabungan.show', compact('ledger', 'passbooks'));
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
