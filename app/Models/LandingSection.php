<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSection extends Model
{
    protected $primaryKey = 'section_id';

    protected $fillable = ['key', 'title', 'content', 'is_active', 'order'];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
