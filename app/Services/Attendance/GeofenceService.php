<?php

namespace App\Services\Attendance;

use App\Models\AttendanceSetting;

class GeofenceService
{
    // Radius bumi dalam meter, dipakai dalam perhitungan Haversine
    private const EARTH_RADIUS_METERS = 6371000;

    // Cek apakah koordinat guru berada dalam radius yang diizinkan.
    // Titik pusat dan radius diambil dari tabel attendance_settings.
    // Setting key yang dibutuhkan:
    //   geofence_latitude     → latitude pusat sekolah
    //   geofence_longitude    → longitude pusat sekolah
    //   geofence_radius_meter → radius dalam meter (misal: 100)
    public function isWithinZone(float $lat, float $lng): bool
    {
        $centerLat    = (float) AttendanceSetting::where('setting_key', 'geofence_latitude')->value('setting_value');
        $centerLng    = (float) AttendanceSetting::where('setting_key', 'geofence_longitude')->value('setting_value');
        $radiusMeter  = (float) AttendanceSetting::where('setting_key', 'geofence_radius_meter')->value('setting_value');

        $distance = $this->calculateDistance($lat, $lng, $centerLat, $centerLng);

        return $distance <= $radiusMeter;
    }

    // Simpan atau update titik koordinat dan radius geofence sekolah.
    // Menggunakan AttendanceSetting agar konsisten dengan isWithinZone().
    public function saveZone(float $lat, float $lng, float $radiusMeter): void
    {
        $settings = [
            'geofence_latitude'     => $lat,
            'geofence_longitude'    => $lng,
            'geofence_radius_meter' => $radiusMeter,
        ];

        foreach ($settings as $key => $value) {
            AttendanceSetting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value]
            );
        }
    }

    // Ambil konfigurasi geofence aktif. Null jika belum pernah disimpan.
    public function getZone(): ?array
    {
        $settings = AttendanceSetting::whereIn('setting_key', [
            'geofence_latitude', 'geofence_longitude', 'geofence_radius_meter',
        ])->pluck('setting_value', 'setting_key');

        if ($settings->count() < 3) {
            return null;
        }

        return [
            'latitude'     => (float) $settings['geofence_latitude'],
            'longitude'    => (float) $settings['geofence_longitude'],
            'radius_meter' => (float) $settings['geofence_radius_meter'],
        ];
    }

    // Hitung jarak antara dua titik koordinat GPS menggunakan rumus Haversine.
    // Haversine memperhitungkan kelengkungan bumi sehingga hasilnya akurat
    // untuk jarak pendek maupun jauh. Return nilai dalam meter.
    //
    // Rumus:
    //   a = sin²(Δlat/2) + cos(lat1) × cos(lat2) × sin²(Δlng/2)
    //   c = 2 × atan2(√a, √(1−a))
    //   d = R × c
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        // Konversi derajat ke radian
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $c;
    }
}
