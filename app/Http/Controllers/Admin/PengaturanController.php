<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $settings = AttendanceSetting::pluck('setting_value', 'setting_key');
        return view('admin.pengaturan.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'geofence_latitude'    => 'required|numeric',
            'geofence_longitude'   => 'required|numeric',
            'geofence_radius_meter'=> 'required|integer|min:10',
            'check_in_start'       => 'required|date_format:H:i',
            'check_in_end'         => 'required|date_format:H:i',
        ]);

        foreach ($data as $key => $value) {
            AttendanceSetting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value]
            );
        }

        return redirect()->route('admin.pengaturan.index')
            ->with('success', "Pengaturan berhasil disimpan.");
    }
}
