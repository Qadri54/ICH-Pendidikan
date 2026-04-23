<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $primaryKey = 'teacher_id';

    protected $fillable = [
        'user_id',
        'NIP',
        'tipe',
        'subject',
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

    public function subjectTeachers()
    {
        return $this->hasMany(SubjectTeacher::class, 'teacher_id', 'teacher_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teachers', 'teacher_id', 'subject_id', 'teacher_id', 'subject_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'teacher_id', 'teacher_id');
    }

    public function studentsGrades()
    {
        return $this->hasMany(StudentGrade::class, 'teacher_id', 'teacher_id');
    }

    public function savingLedgers()
    {
        return $this->hasMany(SavingLedger::class, 'teacher_id', 'teacher_id');
    }

    public function homeroomReportCards()
    {
        return $this->hasMany(StudentReportCard::class, 'homeroom_teacher_id', 'teacher_id');
    }
}
