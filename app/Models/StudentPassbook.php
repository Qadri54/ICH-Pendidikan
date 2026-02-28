<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPassbook extends Model
{
    use HasFactory;

    protected $primaryKey = 'passbook_id';

    protected $fillable = [
        'student_id',
        'ledger_id',
        'opening_date',
        'opening_balance',
        'current_balance',
        'passbook_file',
        'last_update',
    ];

    protected function casts(): array
    {
        return [
            'opening_date' => 'date',
            'last_update'  => 'datetime',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function ledger()
    {
        return $this->belongsTo(SavingLedger::class, 'ledger_id', 'ledger_id');
    }

    public function savingTransactions()
    {
        return $this->hasMany(SavingTransaction::class, 'passbook_id', 'passbook_id');
    }
}
