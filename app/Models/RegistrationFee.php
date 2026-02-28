<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationFee extends Model
{
    use HasFactory;

    protected $primaryKey = 'registration_fee_id';

    protected $fillable = [
        'student_id',
        'total_jumlah',
        'status',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function installments()
    {
        return $this->hasMany(FeeInstallment::class, 'registration_fee_id', 'registration_fee_id');
    }

    public function transactions()
    {
        return $this->hasMany(RegistrationTransaction::class, 'registration_fee_id', 'registration_fee_id');
    }
}
