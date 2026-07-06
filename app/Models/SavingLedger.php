<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingLedger extends Model
{
    use HasFactory;

    protected $primaryKey = 'ledger_id';

    protected $fillable = [
        'teacher_id',
        'period_id',
        'ledger_name',
        'opening_date',
        'opening_balance',
        'total_balance',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'opening_date' => 'date',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriod::class, 'period_id', 'period_id');
    }

    public function passbooks()
    {
        return $this->hasMany(StudentPassbook::class, 'ledger_id', 'ledger_id');
    }

    public function savingTransactions()
    {
        return $this->hasMany(SavingTransaction::class, 'ledger_id', 'ledger_id');
    }
}
