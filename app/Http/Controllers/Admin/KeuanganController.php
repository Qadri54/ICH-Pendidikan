<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SppInvoice;
use App\Models\Student;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $invoices = SppInvoice::with('student.classRoom')
            ->when($request->search, fn($q) =>
                $q->whereHas('student', fn($s) => $s->where('nama_siswa', 'like', "%{$request->search}%"))
            )
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest('jatuh_tempo')
            ->paginate(15)->withQueryString();

        $totalTagihan = SppInvoice::where('status', '!=', 'Lunas')->sum('jumlah');
        $totalLunas   = SppInvoice::where('status', 'Lunas')->count();

        return view('admin.keuangan.index', compact('invoices', 'totalTagihan', 'totalLunas'));
    }

    public function create()
    {
        $siswa = Student::with('classRoom')->orderBy('nama_siswa')->get();
        return view('admin.keuangan.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id'    => 'required|exists:students,student_id',
            'tanggal_tahun' => 'required|date',
            'jumlah'        => 'required|numeric|min:0',
            'jatuh_tempo'   => 'required|date',
            'status'        => 'required|in:Belum Bayar,Lunas',
        ]);

        SppInvoice::create($data);

        return redirect()->route('admin.keuangan.index')
            ->with('success', "Tagihan SPP berhasil dibuat.");
    }

    public function edit(SppInvoice $keuangan)
    {
        $siswa = Student::orderBy('nama_siswa')->get();
        return view('admin.keuangan.edit', compact('keuangan', 'siswa'));
    }

    public function update(Request $request, SppInvoice $keuangan)
    {
        $data = $request->validate([
            'jumlah'      => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date',
            'status'      => 'required|in:Belum Bayar,Lunas',
        ]);

        $keuangan->update($data);

        return redirect()->route('admin.keuangan.index')
            ->with('success', "Tagihan berhasil diperbarui.");
    }

    public function destroy(SppInvoice $keuangan)
    {
        $keuangan->delete();

        return redirect()->route('admin.keuangan.index')
            ->with('success', "Tagihan berhasil dihapus.");
    }
}
