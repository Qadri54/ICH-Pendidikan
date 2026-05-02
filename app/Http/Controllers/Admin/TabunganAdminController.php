<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Services\Saving\SavingLedgerService;
use Illuminate\Http\Request;

class TabunganAdminController extends Controller
{
    public function __construct(private SavingLedgerService $ledgerService) {}

    public function index(Request $request)
    {
        $ledgers = $this->ledgerService->getPaginated($request->search, $request->status);

        return view('admin.tabungan.index', compact('ledgers'));
    }

    public function create()
    {
        $guru = Teacher::with('user')->get();
        return view('admin.tabungan.create', compact('guru'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'teacher_id'      => 'required|exists:teachers,teacher_id',
            'ledger_name'     => 'required|string|max:255',
            'academic_year'   => 'required|date',
            'opening_date'    => 'required|date',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $this->ledgerService->create($data);

        return redirect()->route('admin.tabungan.index')
            ->with('success', "Ledger tabungan berhasil dibuat.");
    }

    public function show(\App\Models\SavingLedger $tabungan)
    {
        $tabungan = $this->ledgerService->getById($tabungan->ledger_id);
        return view('admin.tabungan.show', compact('tabungan'));
    }

    public function destroy(\App\Models\SavingLedger $tabungan)
    {
        try {
            $this->ledgerService->delete($tabungan->ledger_id);
            return redirect()->route('admin.tabungan.index')
                ->with('success', "Ledger berhasil dihapus.");
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('admin.tabungan.index')
                ->with('error', $e->getMessage());
        }
    }
}
