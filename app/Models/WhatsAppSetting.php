<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppSetting extends Model
{
    protected $table = 'whatsapp_settings';

    protected $primaryKey = 'whatsapp_setting_id';

    protected $fillable = [
        'setting_key',
        'setting_value',
    ];

    public static function getAll(): array
    {
        return static::pluck('setting_value', 'setting_key')->toArray();
    }

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::where('setting_key', $key)->value('setting_value') ?? $default;
    }
}
