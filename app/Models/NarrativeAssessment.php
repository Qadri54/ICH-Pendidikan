<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NarrativeAssessment extends Model
{
    use HasFactory;

    protected $primaryKey = 'narrative_id';

    protected $fillable = [
        'report_card_id',
        'kategori',
        'judul',
        'isi_naratif',
    ];

    // Relationships
    public function reportCard()
    {
        return $this->belongsTo(StudentReportCard::class, 'report_card_id', 'report_card_id');
    }

    public function photos()
    {
        return $this->hasMany(NarrativePhoto::class, 'narrative_id', 'narrative_id');
    }
}
