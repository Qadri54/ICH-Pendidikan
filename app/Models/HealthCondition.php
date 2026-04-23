<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthCondition extends Model
{
    use HasFactory;

    protected $primaryKey = 'health_id';

    protected $fillable = [
        'report_card_id',
        'pendengaran',
        'penglihatan',
        'catatan_tambahan',
    ];

    // Relationships
    public function reportCard()
    {
        return $this->belongsTo(StudentReportCard::class, 'report_card_id', 'report_card_id');
    }
}
