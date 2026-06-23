<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $primaryKey = 'attendance_record_id';

    protected $fillable = [
        'teacher_id',
        'check_in_time',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_accuracy',
        'selfie_path',
        'is_within_geofence',
        'attendance_status',
    ];

    protected function casts(): array
    {
        return [
            'check_in_time'  => 'datetime',
            'check_in_latitude'   => 'decimal:7',
            'check_in_longitude'  => 'decimal:7',
        ];
    }

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }

}
