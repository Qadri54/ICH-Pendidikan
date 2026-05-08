<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationSetting extends Model
{
    protected $fillable = ['is_registration_period'];

    protected function casts(): array
    {
        return ['is_registration_period' => 'boolean'];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], ['is_registration_period' => false]);
    }
}
