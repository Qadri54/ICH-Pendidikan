<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $primaryKey = 'subject_id';

    protected $fillable = [
        'subject_name',
        'description',
    ];

    // Relationships
    public function subjectTeachers()
    {
        return $this->hasMany(SubjectTeacher::class, 'subject_id', 'subject_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'subject_teachers', 'subject_id', 'teacher_id', 'subject_id', 'teacher_id');
    }
}
