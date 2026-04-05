<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectTeacher extends Model
{
    use HasFactory;

    protected $primaryKey = 'subject_teacher_id';

    protected $fillable = [
        'teacher_id',
        'subject_id',
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function studentsGrades()
    {
        return $this->hasMany(StudentGrade::class, 'subject_teacher_id', 'subject_teacher_id');
    }
}
