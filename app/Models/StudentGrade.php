<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    use HasFactory;

    protected $table = 'students_grades';

    protected $primaryKey = 'penilaian_id';

    protected $fillable = [
        'student_id',
        'teacher_id',
        'class_id',
        'subject_teacher_id',
        'grades',
    ];

    protected function casts(): array
    {
        return [
            'grades' => 'decimal:2',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id', 'class_id');
    }

    public function subjectTeacher()
    {
        return $this->belongsTo(SubjectTeacher::class, 'subject_teacher_id', 'subject_teacher_id');
    }
}
