<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialistReferrals extends Model
{
    use HasFactory;


     protected $table = 'specialist_referrals';

    // Define the fields that are mass-assignable
    protected $fillable = ['user_email', 'request_reason', 'image_uploaded', 'file_name', 'request_status'];

    /**
     * Define a relationship to the User model using the email field.
     * This assumes the User model has an `email` field.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_email', 'email');
    }

}
