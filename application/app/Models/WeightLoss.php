<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightLoss extends Model
{
    use HasFactory;
    protected $table = 'weight_loss'; // Explicitly set the correct table name if needed

    protected $fillable = [
        'user_email',
        'medication_used',
        'diseases_pancreas_liver_kidneys',
        'taking_insulin',
        'allergic_reaction',
        'any_allergies',
        'pregnant',
        'eating_disorder',
        'cardiovascular_disease',
        'strong_pain_killers',
        'severe_heart_failure',
        'brain_tumour',
        'bariatric_surgery',
        'gastroparesis',
        'requestReason',
        'height',
        'weight',
         'request_status'
    ];

   
}
