<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
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

class TabunganAdminController extends Controller
{
    public function __construct(
        private SavingLedgerService $ledgerService,
        private PassbookService $passbookService,
        private SavingTransactionService $transactionService,
    ) {}

    public function index(Request $request): View
    {
        $ledgers = $this->ledgerService->getPaginated($request->search, $request->status);
        $guru    = Teacher::with(['user', 'homeroomClass'])->get();
        $periods = AcademicPeriod::orderByDesc('tanggal_mulai')->get();

        return view('admin.tabungan.index', compact('ledgers', 'guru', 'periods'));
    }

    public function create(): View
    {
        $guru = Teacher::with('user')->get();
        return view('admin.tabungan.create', compact('guru'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'teacher_id'      => 'required|exists:teachers,teacher_id',
            'period_id'       => 'required|exists:academic_periods,period_id',
            'ledger_name'     => 'required|string|max:255',
            'opening_date'    => 'required|date',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $this->ledgerService->create($data);

        return redirect()->route('admin.tabungan.index')
            ->with('success', 'Ledger tabungan berhasil dibuat.');
    }

    public function show(SavingLedger $tabungan): View
    {
        $tabungan = $this->ledgerService->getById($tabungan->ledger_id);
        $existingStudentIds = $tabungan->passbooks()->pluck('student_id');
        $availableStudents = Student::with('classRoom')
            ->where('status', 'aktif')
            ->whereNotIn('student_id', $existingStudentIds)
            ->orderBy('nama_siswa')
            ->get();
        $classes = ClassRoom::orderBy('nama_kelas')->get();

        return view('admin.tabungan.show', compact('tabungan', 'availableStudents', 'classes'));
    }

    public function close(SavingLedger $tabungan): RedirectResponse
    {
        try {
            $this->ledgerService->close($tabungan->ledger_id);
            return redirect()->route('admin.tabungan.show', $tabungan)
                ->with('success', 'Ledger berhasil ditutup.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(SavingLedger $tabungan): RedirectResponse
    {
        try {
            $this->ledgerService->delete($tabungan->ledger_id);
            return redirect()->route('admin.tabungan.index')
                ->with('success', 'Ledger berhasil dihapus.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('admin.tabungan.index')
                ->with('error', $e->getMessage());
        }
    }

    // ─── Passbook Management ─────────────────────────────────────────────────

    public function openPassbook(SavingLedger $tabungan): View
    {
        $existingStudentIds = $tabungan->passbooks()->pluck('student_id');
        $students = Student::whereNotIn('student_id', $existingStudentIds)->orderBy('nama_siswa')->get();

        return view('admin.tabungan.passbook-create', compact('tabungan', 'students'));
    }

    public function storePassbook(Request $request, SavingLedger $tabungan): RedirectResponse
    {
        $data = $request->validate([
            'student_ids'     => 'required|array|min:1',
            'student_ids.*'   => 'integer|exists:students,student_id',
            'opening_date'    => 'required|date',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        try {
            $count = $this->passbookService->bulkOpen(
                $tabungan->ledger_id,
                $data['student_ids'],
                $data['opening_date'],
                (int) ($data['opening_balance'] ?? 0)
            );
            return redirect()->route('admin.tabungan.show', $tabungan)
                ->with('success', "{$count} buku tabungan berhasil dibuka.");
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showPassbook(StudentPassbook $passbook): View
    {
        $passbook = $this->passbookService->getById($passbook->passbook_id);
        $transactions = $this->transactionService->getByPassbook($passbook->passbook_id);

        return view('admin.tabungan.passbook-show', compact('passbook', 'transactions'));
    }

    public function deposit(Request $request, StudentPassbook $passbook): RedirectResponse
    {
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
