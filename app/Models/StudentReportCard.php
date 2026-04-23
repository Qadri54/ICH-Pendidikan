<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReportCard extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_card_id';

    protected $fillable = [
        'student_id',
        'period_id',
        'class_id',
        'homeroom_teacher_id',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function period()
    {
        return $this->belongsTo(AcademicPeriod::class, 'period_id', 'period_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id', 'class_id');
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id', 'teacher_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    public function narrativeAssessments()
    {
        return $this->hasMany(NarrativeAssessment::class, 'report_card_id', 'report_card_id');
    }

    public function checklistAssessments()
    {
        return $this->hasMany(StudentChecklistAssessment::class, 'report_card_id', 'report_card_id');
    }

    public function physicalMeasurement()
    {
        return $this->hasOne(PhysicalMeasurement::class, 'report_card_id', 'report_card_id');
    }

    public function healthCondition()
    {
        return $this->hasOne(HealthCondition::class, 'report_card_id', 'report_card_id');
    }
}
