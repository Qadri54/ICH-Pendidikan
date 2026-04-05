<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppInvoice extends Model
{
    use HasFactory;

    protected $table = 'spp_invoices';

    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'student_id',
        'tanggal_tahun',
        'jumlah',
        'jatuh_tempo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_tahun' => 'date',
            'jatuh_tempo'   => 'date',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(SppPayment::class, 'invoice_id', 'invoice_id');
    }
}
