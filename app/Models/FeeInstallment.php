<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeInstallment extends Model
{
    use HasFactory;

    protected $primaryKey = 'installment_id';

    protected $fillable = [
        'registration_fee_id',
        'jumlah',
        'tanggal_jatuh_tempo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_jatuh_tempo' => 'datetime',
        ];
    }

    // Relationships
    public function registrationFee()
    {
        return $this->belongsTo(RegistrationFee::class, 'registration_fee_id', 'registration_fee_id');
    }
}
