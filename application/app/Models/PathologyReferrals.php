<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Referral;


class PathologyReferrals extends Model
{
    use HasFactory;


     protected $table = 'pathology_referrals';

    // Define the fields that are mass-assignable
    protected $fillable = [
        'solution_available_testing',
        'requestReason',
    ];

    protected $casts = [
        'solution_available_testing' => 'array',
    ];

      public function referral()
    {
        return $this->belongsTo(Referral::class, 'referral_id');
    } 

}
