<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReligiousTeacher extends Model
{
    use HasFactory;

    protected $table = 'religious_teachers';

    protected $primaryKey = 'religious_teacher_id';

    protected $fillable = [
        'user_id',
        'NIP',
        'tipe',
        'hire_date',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'religious_teacher_id', 'religious_teacher_id');
    }
}
