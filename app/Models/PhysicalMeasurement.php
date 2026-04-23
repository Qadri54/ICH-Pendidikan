<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalMeasurement extends Model
{
    use HasFactory;

    protected $primaryKey = 'measurement_id';

    protected $fillable = [
        'report_card_id',
        'tinggi_badan',
        'berat_badan',
        'lingkar_kepala',
        'tanggal_ukur',
    ];

    protected function casts(): array
    {
        return [
            'tinggi_badan' => 'decimal:2',
            'berat_badan' => 'decimal:2',
            'lingkar_kepala' => 'decimal:2',
            'tanggal_ukur' => 'date',
        ];
    }

    // Relationships
    public function reportCard()
    {
        return $this->belongsTo(StudentReportCard::class, 'report_card_id', 'report_card_id');
    }
}
