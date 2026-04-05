<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CacheInvalidation extends Model
{
    public $timestamps = false; // We use invalidated_at, not created_at/updated_at

    protected $table = 'cache_invalidation';

    protected $fillable = [
        'cache_key',
        'invalidated_at',
    ];

    protected $casts = [
        'invalidated_at' => 'datetime',
    ];

    // ─── Scopes ───────────────────────────────────────────

    public function scopeForKey($query, string $key)
    {
        return $query->where('cache_key', $key);
    }

    // ─── Static Helpers ──────────────────────────────────
    /**
     * Check if a cache key has been invalidated.
     */
    public static function wasInvalidated(string $key): bool
    {
        return static::forKey($key)->exists();
    }
    /**
     * Mark a cache key as invalidated.
     */
    public static function invalidate(string $key): void
    {
        static::create(['cache_key' => $key]);
    }

    /**
     * Clear the invalidation record after cache is refreshed.
     */
    public static function clearFlag(string $key): void
    {
        static::forKey($key)->delete();
    }
}