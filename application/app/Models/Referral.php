<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Category;
use App\Models\SpecialistReferrals;
use App\Models\PathologyReferrals;



class Referral extends Model
{
    protected $table = 'referrals';

    protected $fillable = [
        'user_email',
        'category_id',
        'request_status',
        'request_reason',
        'referral_image',
        'condition_image',
    ];

    /**
     * The catalogue item for this referral.
     *
     * Example:
     * Referral
     *   ├── Specialist Referral
     *   └── Pathology Referral
     */
    public function catagory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Specialist referral details.
     */
    public function specialist(): HasOne
    {
        return $this->hasOne(
            SpecialistReferrals::class,
            'referral_id'
        );
    }

    /**
     * Pathology referral details.
     */
    public function pathology(): HasOne
    {
        return $this->hasOne(
            PathologyReferrals::class,
            'referral_id'
        );
    }

}