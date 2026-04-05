<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppPayment extends Model
{
    use HasFactory;

    protected $table = 'spp_payments';

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'student_id',
        'invoice_id',
        'approved_by',
        'payment_date',
        'jumlah_bayar',
        'nama_bank',
        'gambar_bukti_pembayaran',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function invoice()
    {
        return $this->belongsTo(SppInvoice::class, 'invoice_id', 'invoice_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }
}
