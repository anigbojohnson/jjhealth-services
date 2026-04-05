<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathologyReferrals extends Model
{
    use HasFactory;


     protected $table = 'pathology_referrals';

    // Define the fields that are mass-assignable
      protected $fillable = [
        'user_email',
        'imageUpload',
        'solution_available_testing',
        'requestReason',
        'request_status',
    ];

    protected $casts = [
        'solution_available_testing' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_email', 'email');
    }

}
