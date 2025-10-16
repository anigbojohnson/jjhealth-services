<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pre_existing_health',
        'information_pre_existing_health',
        'medications_regularly',
        'medications_regularly_info',
        'start_date_symptoms',
        'detailed_symptoms',
        'treatment_category',
        'user_email',
         'request_status'
    ];
}
