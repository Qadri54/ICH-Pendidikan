<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'student_id';

    protected $fillable = [
        'class_id',
        'user_id',
        'nama_siswa',
        'NIS',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'nama_ayah',
        'nama_ibu',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id', 'class_id');
    }

    public function sppInvoices()
    {
        return $this->hasMany(SppInvoice::class, 'student_id', 'student_id');
    }

    public function sppPayments()
    {
        return $this->hasMany(SppPayment::class, 'student_id', 'student_id');
    }

    public function registrationFees()
    {
        return $this->hasMany(RegistrationFee::class, 'student_id', 'student_id');
    }

    public function studentsGrades()
    {
        return $this->hasMany(StudentGrade::class, 'student_id', 'student_id');
    }

    public function passbooks()
    {
        return $this->hasMany(StudentPassbook::class, 'student_id', 'student_id');
    }

    public function savingTransactions()
    {
        return $this->hasMany(SavingTransaction::class, 'student_id', 'student_id');
    }

    public function reportCards()
    {
        return $this->hasMany(StudentReportCard::class, 'student_id', 'student_id');
    }
}
