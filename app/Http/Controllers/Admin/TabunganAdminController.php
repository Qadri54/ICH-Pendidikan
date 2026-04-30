<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingLedger;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TabunganAdminController extends Controller
{
    public function index(Request $request)
    {
        $ledgers = SavingLedger::with('teacher.user')
            ->when($request->search, fn($q) =>
                $q->where('ledger_name', 'like', "%{$request->search}%")
            )
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)->withQueryString();

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
            'status'          => 'required|in:Aktif,Tutup',
        ]);

        $data['total_balance'] = $data['opening_balance'];
        SavingLedger::create($data);

        return redirect()->route('admin.tabungan.index')
            ->with('success', "Ledger tabungan berhasil dibuat.");
    }

    public function show(SavingLedger $tabungan)
    {
        $tabungan->load('teacher.user', 'passbooks.student', 'savingTransactions');
        return view('admin.tabungan.show', compact('tabungan'));
    }

    public function destroy(SavingLedger $tabungan)
    {
        $tabungan->delete();
        return redirect()->route('admin.tabungan.index')
            ->with('success', "Ledger berhasil dihapus.");
    }
}
