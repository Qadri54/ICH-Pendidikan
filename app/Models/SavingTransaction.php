<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'student_id',
        'ledger_id',
        'passbook_id',
        'transaction_date',
        'transaction_type',
        'amount',
        'description',
        'transaction_number',
        'last_update',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'last_update'      => 'datetime',
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

    public function passbook()
    {
        return $this->belongsTo(StudentPassbook::class, 'passbook_id', 'passbook_id');
    }
}
