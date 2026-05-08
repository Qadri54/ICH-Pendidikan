<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\RegistrationSetting;
use App\Services\Registration\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    public function __construct(private RegistrationService $registrationService) {}

    public function index(): View
    {
        $registrations      = $this->registrationService->getAllByUserId(auth()->id());
        $isRegistrationOpen = RegistrationSetting::current()->is_registration_period;

        return view('orang-tua.pendaftaran.index', compact('registrations', 'isRegistrationOpen'));
    }

    public function create(): View
    {
        $isRegistrationOpen = RegistrationSetting::current()->is_registration_period;
        $lastRegistration   = $this->registrationService->getAllByUserId(auth()->id())->first();

        return view('orang-tua.pendaftaran.create', compact('isRegistrationOpen', 'lastRegistration'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (! RegistrationSetting::current()->is_registration_period) {
            return redirect()->route('pendaftaran')
                ->with('error', 'Masa pendaftaran sedang ditutup. Silakan coba lagi saat pendaftaran dibuka.');
        }

        $data = $request->validate([
            'nama_siswa'    => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir'  => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'nama_ayah'     => 'required|string|max:255',
            'nama_ibu'      => 'required|string|max:255',
            'alamat'        => 'required|string|max:1000',
        ]);

        $this->registrationService->submit(auth()->id(), $data);

        return redirect()->route('pendaftaran')
            ->with('success', 'Pendaftaran berhasil dikirim! Mohon tunggu konfirmasi dari pihak sekolah.');
    }
}
