<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    protected $table = 'otps';

    protected $primaryKey = 'otp_id';

    protected $fillable = [
        'user_id',
        'otp_code',
        'send_count',
        'send_window_started_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'send_count'             => 'integer',
            'send_window_started_at' => 'datetime',
            'expires_at'             => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
