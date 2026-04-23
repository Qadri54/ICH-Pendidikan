<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NarrativePhoto extends Model
{
    use HasFactory;

    protected $primaryKey = 'photo_id';

    protected $fillable = [
        'narrative_id',
        'photo_path',
        'caption',
        'urutan',
    ];

    // Relationships
    public function narrative()
    {
        return $this->belongsTo(NarrativeAssessment::class, 'narrative_id', 'narrative_id');
    }
}
