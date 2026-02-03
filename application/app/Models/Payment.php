<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'payment_id',
        'customer_email',
        'product_id',
        'mc_id',
        'treatment_id',
        'payment_status',
        'specialist_refferrals_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_email', 'email');
    }

    public function solution()
    {
        return $this->belongsTo(Solutions::class, 'product_id', 'solution_id');
    }

    public function medical_certificate()
    {
        return $this->belongsTo(MedicalCertificate::class, 'mc_id', 'id');
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'treatment_id', 'id');
    }
}
