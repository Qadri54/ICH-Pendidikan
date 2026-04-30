<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $pendaftaran = Registration::with('user')
            ->when($request->search, fn($q) =>
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"))
            )
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)->withQueryString();

        return view('admin.pendaftaran.index', compact('pendaftaran'));
    }

    public function show(Registration $pendaftaran)
    {
        $pendaftaran->load('user');
        return view('admin.pendaftaran.show', compact('pendaftaran'));
    }

    public function update(Request $request, Registration $pendaftaran)
    {
        $data = $request->validate([
            'status' => 'required|in:Menunggu,Diterima,Ditolak',
        ]);

        $pendaftaran->update($data);

        return redirect()->route('admin.pendaftaran.show', $pendaftaran)
            ->with('success', "Status pendaftaran berhasil diperbarui.");
    }
}
