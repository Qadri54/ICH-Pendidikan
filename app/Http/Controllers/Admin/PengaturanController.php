<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\AttendanceSetting;
use App\Models\RegistrationSetting;
use App\Models\WhatsAppSetting;
use App\Services\Attendance\GeofenceService;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanController extends Controller
{
    public function __construct(
        private GeofenceService $geofenceService,
    ) {}

    public function index()
    {
        $settings            = AttendanceSetting::pluck('setting_value', 'setting_key');
        $registrationSetting = RegistrationSetting::current();
        $semesters           = AcademicPeriod::orderByDesc('tahun_ajaran')
                                    ->orderByDesc('semester')
                                    ->get();
        $whatsappSettings    = WhatsAppSetting::getAll();

        return view('admin.pengaturan.index', compact('settings', 'registrationSetting', 'semesters', 'whatsappSettings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'geofence_latitude'     => 'required|numeric',
            'geofence_longitude'    => 'required|numeric',
            'geofence_radius_meter' => 'required|integer|min:10',
            'check_in_start'        => 'required|date_format:H:i',
            'check_in_end'          => 'required|date_format:H:i',
        ]);

        $this->geofenceService->saveZone(
            (float) $data['geofence_latitude'],
            (float) $data['geofence_longitude'],
            (float) $data['geofence_radius_meter']
        );

        foreach (['check_in_start', 'check_in_end'] as $key) {
            AttendanceSetting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $data[$key]]
            );
        }

        return redirect()->route('admin.pengaturan.index')
            ->with('success', "Pengaturan berhasil disimpan.");
    }

    public function togglePendaftaran()
    {
        $setting = RegistrationSetting::current();
        $setting->update(['is_registration_period' => ! $setting->is_registration_period]);

        $status = $setting->is_registration_period ? 'dibuka' : 'ditutup';

        return redirect()->route('admin.pengaturan.index')
            ->with('success', "Masa pendaftaran berhasil {$status}.");
    }

    public function storeSemester(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran'    => ['required', 'string', 'regex:/^\d{4}-\d{4}$/', 'max:20'],
            'semester'        => 'required|in:1,2',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ], [
            'tahun_ajaran.regex' => 'Format tahun ajaran harus seperti 2025-2026.',
        ]);

        AcademicPeriod::create([
            ...$data,
            'is_active' => false,
        ]);

        return redirect()->route('admin.pengaturan.index')
            ->with('success', "Semester {$data['semester']} T.A {$data['tahun_ajaran']} berhasil ditambahkan.");
    }

    public function activateSemester(AcademicPeriod $semester)
    {
        DB::transaction(function () use ($semester) {
            AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
            $semester->update(['is_active' => true]);
        });

        return redirect()->route('admin.pengaturan.index')
            ->with('success', "Semester {$semester->semester} T.A {$semester->tahun_ajaran} berhasil diaktifkan.");
    }

    public function destroySemester(AcademicPeriod $semester)
    {
        if ($semester->is_active) {
            return redirect()->route('admin.pengaturan.index')
                ->with('error', 'Semester aktif tidak dapat dihapus.');
        }

        $semester->delete();

        return redirect()->route('admin.pengaturan.index')
            ->with('success', 'Semester berhasil dihapus.');
    }

    public function updateWhatsApp(Request $request)
    {
        $data = $request->validate([
            'whatsapp_enabled' => 'required|in:true,false',
            'whatsapp_driver'  => 'required|in:fonnte,self-hosted',
            'fonnte_token'     => 'nullable|string|max:500',
            'self_hosted_url'  => 'nullable|string|max:255',
        ]);

        foreach ($data as $key => $value) {
            WhatsAppSetting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value ?? '']
            );
        }

        return redirect()->route('admin.pengaturan.index')
            ->with('success', 'Pengaturan WhatsApp berhasil disimpan.');
    }

    public function testWhatsApp(Request $request, WhatsAppService $whatsAppService)
    {
        $request->validate(['test_phone' => 'required|string']);

        $success = $whatsAppService->testSend($request->test_phone);

        return redirect()->route('admin.pengaturan.index')
            ->with($success ? 'success' : 'error', $success ? 'Pesan uji coba berhasil dikirim!' : 'Gagal mengirim pesan. Periksa konfigurasi.');
    }

    public function whatsappQr(WhatsAppService $whatsAppService)
    {
        return response()->json([
            'qr'     => $whatsAppService->getQrCode(),
            'status' => $whatsAppService->getSessionStatus(),
        ]);
    }
}
