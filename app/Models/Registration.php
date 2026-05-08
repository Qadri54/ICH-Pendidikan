<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $primaryKey = 'registration_id';

    protected $fillable = [
        'user_id',
        'nama_siswa',
        'tanggal_lahir',
        'tempat_lahir',
        'jenis_kelamin',
        'nama_ayah',
        'nama_ibu',
        'alamat',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
