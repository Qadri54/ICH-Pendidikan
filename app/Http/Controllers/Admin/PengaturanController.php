<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use App\Models\RegistrationSetting;
use App\Services\Attendance\GeofenceService;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function __construct(
        private GeofenceService $geofenceService,
    ) {}

    public function index()
    {
        $settings            = AttendanceSetting::pluck('setting_value', 'setting_key');
        $registrationSetting = RegistrationSetting::current();

        return view('admin.pengaturan.index', compact('settings', 'registrationSetting'));
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

        // Simpan koordinat geofence via service
        $this->geofenceService->saveZone(
            (float) $data['geofence_latitude'],
            (float) $data['geofence_longitude'],
            (float) $data['geofence_radius_meter']
        );

        // Setting jam non-geofence disimpan langsung
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
}
