<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeofenceZone extends Model
{
    use HasFactory;

    protected $primaryKey = 'geofence_zone_id';

    protected $fillable = [
        'center_latitude',
        'center_longitude',
        'radius_meter',
    ];

    protected function casts(): array
    {
        return [
            'center_latitude'  => 'decimal:7',
            'center_longitude' => 'decimal:7',
            'radius_meter'     => 'decimal:2',
        ];
    }
}
