<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'no_hp',
        'foto',
        'password',
        'status',
        'verified',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password'          => 'hashed',
            'verified'          => 'boolean',
            'email_verified_at' => 'datetime',
        ];
    }

    // Relationships
    public function otp()
    {
        return $this->hasOne(Otp::class, 'user_id', 'user_id');
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'user_id', 'user_id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id', 'user_id');
    }

    public function registration()
    {
        return $this->hasOne(Registration::class, 'user_id', 'user_id');
    }

    public function foundationHead()
    {
        return $this->hasOne(FoundationHead::class, 'user_id', 'user_id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id', 'user_id');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'user_id');
    }

    public function approvedSppPayments()
    {
        return $this->hasMany(SppPayment::class, 'approved_by', 'user_id');
    }
}
