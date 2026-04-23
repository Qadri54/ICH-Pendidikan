<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentChecklistAssessment extends Model
{
    use HasFactory;

    protected $primaryKey = 'checklist_id';

    protected $fillable = [
        'report_card_id',
        'category_id',
        'status',
        'catatan',
    ];

    // Relationships
    public function reportCard()
    {
        return $this->belongsTo(StudentReportCard::class, 'report_card_id', 'report_card_id');
    }

    public function category()
    {
        return $this->belongsTo(DevelopmentCategory::class, 'category_id', 'category_id');
    }
}
