<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Referral extends Model
{
    protected $table = 'referrals';

    protected $fillable = [
        'user_email',
        'catalogue_id',
        'request_status',
    ];

    /**
     * The catalogue item for this referral.
     *
     * Example:
     * Referral
     *   ├── Specialist Referral
     *   └── Pathology Referral
     */
    public function catalogue(): BelongsTo
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id');
    }

    /**
     * Specialist referral details.
     */
    public function specialist(): HasOne
    {
        return $this->hasOne(
            SpecialistReferral::class,
            'referral_id'
        );
    }

    /**
     * Pathology referral details.
     */
    public function pathology(): HasOne
    {
        return $this->hasOne(
            PathologyReferral::class,
            'referral_id'
        );
    }

}