<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalCertificate extends Model
{
    use HasFactory;

    protected $table = 'medical_certificate';

    protected $fillable = [
        'requestDate',
        'user_email',
        'preExistingHealth',
        'medicationsRegularly',
        'seeking',
        'IAgree',
        'adjustmentsReasons',
        'preExistingHealthConditionInformation',
        'privacy',
        'medicationsRegularlyInfo',
        'symptomsDetailed',
        'validFrom',
        'medicalLetterReasons',
        'symptomsStartDate',
        'currentStatus',
        'validTo',
        'careForSomeone',
        'personCared',
         'request_status'
    ];
    
    

}
