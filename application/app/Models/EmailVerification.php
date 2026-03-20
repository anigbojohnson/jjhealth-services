<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class EmailVerification extends Model
{
    use HasFactory;
    use Notifiable;


    protected $fillable = [
        'email', // Add 'email' to the fillable array
        'token',
        'expires_at',
        'last_name',
        'first_name',
        'password',
        'change_time',
        'solution_id'
        // Add other fillable attributes here if any
    ];
}
