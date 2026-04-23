<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'parent_id',
        'nama',
        'urutan',
        'usia_min',
        'usia_max',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function parent()
    {
        return $this->belongsTo(DevelopmentCategory::class, 'parent_id', 'category_id');
    }

    public function children()
    {
        return $this->hasMany(DevelopmentCategory::class, 'parent_id', 'category_id');
    }

    public function checklistAssessments()
    {
        return $this->hasMany(StudentChecklistAssessment::class, 'category_id', 'category_id');
    }

    // Helper: check if this is a leaf node (no children)
    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    // Helper: get all leaf descendants
    public function getLeafDescendants()
    {
        $leaves = collect();

        if ($this->isLeaf()) {
            $leaves->push($this);
        } else {
            foreach ($this->children as $child) {
                $leaves = $leaves->concat($child->getLeafDescendants());
            }
        }

        return $leaves;
    }
}
