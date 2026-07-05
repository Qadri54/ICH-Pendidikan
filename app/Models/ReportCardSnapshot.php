<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCardSnapshot extends Model
{
    protected $primaryKey = 'snapshot_id';

    protected $fillable = [
        'report_card_id',
        'snapshot_data',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_data' => 'array',
        ];
    }

    public function reportCard()
    {
        return $this->belongsTo(StudentReportCard::class, 'report_card_id', 'report_card_id');
    }
}
