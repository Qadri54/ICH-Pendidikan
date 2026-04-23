<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model {
    use HasFactory;

    protected $table = 'student_attendance';
    protected $primaryKey = 'student_attendance_id';

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'status',
        'created_at',
    ];

    protected function casts(): array {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function student() {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
