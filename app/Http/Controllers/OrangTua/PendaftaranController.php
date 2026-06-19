<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Registration;
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

    public function detail(Registration $registration): View
    {
        abort_if($registration->user_id !== auth()->id(), 403);

        return view('orang-tua.pendaftaran.detail', compact('registration'));
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
            'jenis_pendaftaran'  => 'required|in:TK,Mengaji',
            // Biodata siswa
            'nama_siswa'         => 'required|string|max:255',
            'tanggal_lahir'      => 'required|date',
            'tempat_lahir'       => 'required|string|max:255',
            'jenis_kelamin'      => 'required|in:L,P',
            'alamat'             => 'required|string|max:1000',
            'anak_ke'            => 'required|integer|min:1|max:20',
            'ukuran_baju'        => 'nullable|in:S,M,L',
            // Biodata ayah
            'nama_ayah'          => 'required|string|max:255',
            'tempat_lahir_ayah'  => 'required|string|max:255',
            'tanggal_lahir_ayah' => 'required|date',
            'alamat_ayah'        => 'required|string|max:1000',
            'pendidikan_ayah'    => 'required|string|max:100',
            'pekerjaan_ayah'     => 'required|string|max:255',
            'no_telp_ayah'       => 'required|string|max:20',
            // Biodata ibu
            'nama_ibu'           => 'required|string|max:255',
            'tempat_lahir_ibu'   => 'required|string|max:255',
            'tanggal_lahir_ibu'  => 'required|date',
            'alamat_ibu'         => 'required|string|max:1000',
            'pekerjaan_ibu'      => 'required|string|max:255',
            'pendidikan_ibu'     => 'required|string|max:100',
            'no_telp_ibu'        => 'required|string|max:20',
        ]);

        // ukuran_baju hanya relevan untuk TK
        if ($data['jenis_pendaftaran'] !== 'TK') {
            $data['ukuran_baju'] = null;
        }

        $this->registrationService->submit(auth()->id(), $data);

        return redirect()->route('pendaftaran')
            ->with('success', 'Pendaftaran berhasil dikirim! Mohon tunggu konfirmasi dari pihak sekolah.');
    }
}
