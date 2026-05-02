<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\Registration\RegistrationService;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function __construct(private RegistrationService $registrationService) {}

    public function index(Request $request)
    {
        $pendaftaran = $this->registrationService->getPaginated(
            $request->search,
            $request->status,
        );

        return view('admin.pendaftaran.index', compact('pendaftaran'));
    }

    public function show(Registration $pendaftaran)
    {
        $pendaftaran = $this->registrationService->getById($pendaftaran->registration_id);
        return view('admin.pendaftaran.show', compact('pendaftaran'));
    }

    public function update(Request $request, Registration $pendaftaran)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        if ($request->status === 'accepted') {
            $this->registrationService->approve($pendaftaran->registration_id);
        } else {
            $this->registrationService->reject($pendaftaran->registration_id);
        }

        return redirect()->route('admin.pendaftaran.show', $pendaftaran)
            ->with('success', "Status pendaftaran berhasil diperbarui.");
    }
}
