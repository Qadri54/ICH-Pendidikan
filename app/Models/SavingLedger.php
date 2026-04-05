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
        'ledger_name',
        'academic_year',
        'opening_date',
        'opening_balance',
        'total_balance',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'academic_year'  => 'date',
            'opening_date'   => 'date',
        ];
    }

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
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
