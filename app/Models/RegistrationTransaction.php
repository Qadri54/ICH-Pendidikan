<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'registration_transaction_id';

    protected $fillable = [
        'registration_fee_id',
        'approved_by',
        'payment_date',
        'jumlah_bayar',
        'nama_bank',
        'gambar_bukti_pembayaran',
        'payment_category',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
        ];
    }

    // Relationships
    public function registrationFee()
    {
        return $this->belongsTo(RegistrationFee::class, 'registration_fee_id', 'registration_fee_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Admin::class, 'approved_by', 'admin_id');
    }
}
