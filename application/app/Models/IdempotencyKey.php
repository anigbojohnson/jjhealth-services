<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdempotencyKey extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'endpoint',
        'request_hash',
        'status',
        'response_code',
        'response_body',
        'expires_at',
    ];

    protected $casts = [
        'response_body' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * The user who owns this idempotency key.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}