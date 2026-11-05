<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivateVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'private_visits';

    protected $fillable = [
        'hospital',
        'health_department',
        'moh_id',
        'fck_id',
        'visitor_name',
        'visitor_phone',
        'visitor_nationality',
        'patient_name',
        'patient_phone',
        'patient_nationality',
        'annual_number',
        'sequential_number',
        'fiscal_year',
        'visit_date',
        'visit_time',
        'service_id',
        'doctor_id',
        'service_fee',
        'payment_type',
        'interest_rate',
        'interest_amount',
        'receipt_number',
        'receipt_date',
        'total_amount',
        'cancelled_at',
        'cancellation_reason'
    ];

    protected $casts = [
        'visit_date' => 'date',
        'receipt_date' => 'date',
        'cancelled_at' => 'datetime',
        'service_fee' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function services()
    {
        return $this->hasMany(PrivateVisitService::class, 'private_visit_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function serviceSpecialization()
    {
        return $this->belongsTo(Service_Specialization::class, 'service_id');
    }

    public function moh()
    {
        return $this->belongsTo(mohs::class, 'moh_id');
    }

    public function fck()
    {
        return $this->belongsTo(Fck::class, 'fck_id');
    }
}
