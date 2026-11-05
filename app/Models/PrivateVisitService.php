<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateVisitService extends Model
{
    use HasFactory;

    protected $table = 'private_visit_services';

    protected $fillable = [
        'private_visit_id',
        'service_id',
        'service_fee',
        'patient_amount',
        'insurance_amount',
        'doctor_id',
        'total_amount',
        'operation_degree'
    ];

    public function privateVisit()
    {
        return $this->belongsTo(PrivateVisit::class, 'private_visit_id');
    }

    public function serviceSpecialization()
    {
        return $this->belongsTo(Service_Specialization::class, 'service_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
