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
        'jenis_pendaftaran',
        'nama_siswa',
        'tanggal_lahir',
        'tempat_lahir',
        'jenis_kelamin',
        'alamat',
        'anak_ke',
        'ukuran_baju',
        'nama_ayah',
        'tempat_lahir_ayah',
        'tanggal_lahir_ayah',
        'alamat_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'no_telp_ayah',
        'nama_ibu',
        'tempat_lahir_ibu',
        'tanggal_lahir_ibu',
        'alamat_ibu',
        'pekerjaan_ibu',
        'pendidikan_ibu',
        'no_telp_ibu',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir'     => 'date',
            'tanggal_lahir_ayah' => 'date',
            'tanggal_lahir_ibu'  => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
