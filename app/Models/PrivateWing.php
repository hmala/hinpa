<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PrivateWingService;

class PrivateWing extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital',
        'health_department',
        'patient_name',
        'file_number',
        'statistical_number',
        'entry_date',
        'exit_date',
        'days_count',
        'patient_bed_fee',
        'companion_bed_fee',
        'nutrition_fee',
        'medicine_supplies_fee',
        'laboratory_tests_fee',
        'xray_fees',
        'sonar_fees',
        'deposit_amount',
        'receipt_number',
        'receipt_date',
        'total_amount'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'exit_date' => 'date',
        'receipt_date' => 'date',
        'patient_bed_fee' => 'decimal:2',
        'companion_bed_fee' => 'decimal:2',
        'nutrition_fee' => 'decimal:2',
        'medicine_supplies_fee' => 'decimal:2',
        'laboratory_tests_fee' => 'decimal:2',
        'xray_fees' => 'decimal:2',
        'sonar_fees' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'total_amount' => 'decimal:2'    ];

    /**
     * علاقة مع خدمات الجناح الخاص
     */    public function services()
    {
        return $this->belongsToMany(Service_Specialization::class, 'private_wing_services', 'private_wing_id', 'service_id')
                    ->withPivot('service_fee', 'is_daily', 'total_amount')
                    ->withTimestamps();
    }
}
