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

    public function create()
    {
        return view('admin.pendaftaran.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_pendaftaran'  => 'required|in:TK,Mengaji',
            'email'              => 'required|email|unique:users,email',
            'nama_siswa'         => 'required|string|max:255',
            'tanggal_lahir'      => 'required|date',
            'tempat_lahir'       => 'required|string|max:255',
            'jenis_kelamin'      => 'required|in:L,P',
            'alamat'             => 'required|string|max:1000',
            'anak_ke'            => 'required|integer|min:1|max:20',
            'ukuran_baju'        => 'nullable|in:S,M,L',
            'nama_ayah'          => 'required|string|max:255',
            'tempat_lahir_ayah'  => 'required|string|max:255',
            'tanggal_lahir_ayah' => 'required|date',
            'alamat_ayah'        => 'required|string|max:1000',
            'pendidikan_ayah'    => 'required|string|max:100',
            'pekerjaan_ayah'     => 'required|string|max:255',
            'no_telp_ayah'       => 'required|string|max:20',
            'nama_ibu'           => 'required|string|max:255',
            'tempat_lahir_ibu'   => 'required|string|max:255',
            'tanggal_lahir_ibu'  => 'required|date',
            'alamat_ibu'         => 'required|string|max:1000',
            'pekerjaan_ibu'      => 'required|string|max:255',
            'pendidikan_ibu'     => 'required|string|max:100',
            'no_telp_ibu'        => 'required|string|max:20',
        ]);

        if ($data['jenis_pendaftaran'] !== 'TK') {
            $data['ukuran_baju'] = null;
        }

        $registration = $this->registrationService->submitByAdmin($data);

        return redirect()->route('admin.pendaftaran.show', $registration)
            ->with('success', 'Pendaftaran berhasil dibuat. Akun orang tua telah dibuat otomatis.');
    }

    public function show(Registration $pendaftaran)
    {
        $pendaftaran = $this->registrationService->getById($pendaftaran->registration_id);
        return view('admin.pendaftaran.show', compact('pendaftaran'));
    }

    public function edit(Registration $pendaftaran)
    {
        $pendaftaran = $this->registrationService->getById($pendaftaran->registration_id);
        return view('admin.pendaftaran.edit', compact('pendaftaran'));
    }

    public function update(Request $request, Registration $pendaftaran)
    {
        if ($request->has('update_biodata')) {
            $data = $request->validate([
                'jenis_pendaftaran'  => 'required|in:TK,Mengaji',
                'nama_siswa'         => 'required|string|max:255',
                'tanggal_lahir'      => 'required|date',
                'tempat_lahir'       => 'required|string|max:255',
                'jenis_kelamin'      => 'required|in:L,P',
                'alamat'             => 'required|string|max:1000',
                'anak_ke'            => 'required|integer|min:1|max:20',
                'ukuran_baju'        => 'nullable|in:S,M,L',
                'nama_ayah'          => 'required|string|max:255',
                'tempat_lahir_ayah'  => 'required|string|max:255',
                'tanggal_lahir_ayah' => 'required|date',
                'alamat_ayah'        => 'required|string|max:1000',
                'pendidikan_ayah'    => 'required|string|max:100',
                'pekerjaan_ayah'     => 'required|string|max:255',
                'no_telp_ayah'       => 'required|string|max:20',
                'nama_ibu'           => 'required|string|max:255',
                'tempat_lahir_ibu'   => 'required|string|max:255',
                'tanggal_lahir_ibu'  => 'required|date',
                'alamat_ibu'         => 'required|string|max:1000',
                'pekerjaan_ibu'      => 'required|string|max:255',
                'pendidikan_ibu'     => 'required|string|max:100',
                'no_telp_ibu'        => 'required|string|max:20',
            ]);

            if ($data['jenis_pendaftaran'] !== 'TK') {
                $data['ukuran_baju'] = null;
            }

            // Simpan nama lama untuk mencari student jika status sudah accepted
            $oldNama = $pendaftaran->nama_siswa;
            $oldTgl = $pendaftaran->tanggal_lahir?->format('Y-m-d');

            $pendaftaran->update($data);

            if ($pendaftaran->status === 'accepted') {
                // Sync data dengan tabel students
                $student = \App\Models\Student::where('user_id', $pendaftaran->user_id)
                    ->where('nama_siswa', $oldNama)
                    ->whereDate('tanggal_lahir', $oldTgl)
                    ->first();
                if ($student) {
                    $student->update([
                        'nama_siswa'    => $data['nama_siswa'],
                        'jenis_kelamin' => $data['jenis_kelamin'],
                        'tanggal_lahir' => $data['tanggal_lahir'],
                        'tempat_lahir'  => $data['tempat_lahir'],
                        'nama_ayah'     => $data['nama_ayah'],
                        'nama_ibu'      => $data['nama_ibu'],
                    ]);
                }
            }

            return redirect()->route('admin.pendaftaran.show', $pendaftaran)
                ->with('success', "Biodata pendaftaran berhasil diperbarui.");
        }

        $request->validate([
            'status'           => 'required|in:accepted,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        if ($request->status === 'accepted') {
            $this->registrationService->approve($pendaftaran->registration_id);
        } else {
            $this->registrationService->reject($pendaftaran->registration_id, $request->rejection_reason);
        }

        return redirect()->route('admin.pendaftaran.show', $pendaftaran)
            ->with('success', "Status pendaftaran berhasil diperbarui.");
    }
}
