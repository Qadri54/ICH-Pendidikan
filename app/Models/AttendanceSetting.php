<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $table = 'attendance_settings';

    protected $primaryKey = 'attendance_setting_id';

    protected $fillable = [
        'setting_key',
        'setting_value',
    ];
}
