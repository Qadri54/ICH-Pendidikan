<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $primaryKey = 'class_id';

    protected $fillable = [
        'nama_kelas',
        'nama_ruangan',
    ];

    // Relationships
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id', 'class_id');
    }

    public function studentsGrades()
    {
        return $this->hasMany(StudentGrade::class, 'class_id', 'class_id');
    }

    public function reportCards()
    {
        return $this->hasMany(StudentReportCard::class, 'class_id', 'class_id');
    }
}
