<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PrivateWingService;

class PrivateWing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hospital',
        'health_department',
        'moh_id',
        'fck_id',
        'patient_name',
        'patient_phone',
        'patient_nationality',
        'sequential_number',
        'annual_number',
        'fiscal_year',
        'statistical_number',
        'entry_date',
        'entry_time',
        'exit_date',
        'exit_time',
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
        'total_amount',
        'is_insured',
        'exception_from',
        'exception_percentage',
        'exception_attachment',
        'interest_amount',
        'interest_rate',
        'refund_amount',
        'refund_status',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'entry_time' => 'datetime:H:i',
        'exit_date' => 'date',
        'exit_time' => 'datetime:H:i',
        'receipt_date' => 'date',
        'cancelled_at' => 'datetime',
        'is_insured' => 'boolean',
        'patient_bed_fee' => 'decimal:2',
        'companion_bed_fee' => 'decimal:2',
        'nutrition_fee' => 'decimal:2',
        'medicine_supplies_fee' => 'decimal:2',
        'laboratory_tests_fee' => 'decimal:2',
        'xray_fees' => 'decimal:2',
        'sonar_fees' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2'
    ];

    /**
     * علاقة مع خدمات الجناح الخاص
     */    public function services()
    {
        return $this->belongsToMany(Service_Specialization::class, 'private_wing_services', 'private_wing_id', 'service_id')
                    ->withPivot('service_fee', 'is_daily', 'total_amount')
                    ->withTimestamps();
    }

    /**
     * علاقة مع دائرة الصحة
     */
    public function moh()
    {
        return $this->belongsTo(Mohs::class, 'moh_id');
    }

    /**
     * علاقة مع المؤسسة
     */
    public function fck()
    {
        return $this->belongsTo(Fck::class, 'fck_id');
    }

    /**
     * علاقة مع صفوف الخدمات الخاصة بالجناح (الوصول إلى pivot كـ model)
     */
    public function privateWingServices()
    {
        return $this->hasMany(PrivateWingService::class, 'private_wing_id');
    }

    /**
     * علاقة مع وصولات التأمين
     */
    public function receipts()
    {
        return $this->hasMany(PrivateWingReceipt::class)->orderBy('receipt_date', 'desc');
    }

    /**
     * علاقة مع وصولات الاسترداد
     */
    public function refunds()
    {
        return $this->hasMany(PrivateWingRefund::class)->orderBy('refund_date', 'desc');
    }

    /**
     * علاقة مع الأطباء
     */
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    /**
     * علاقة مع المستخدم الذي أبطل القائمة
     */
    public function cancelledByUser()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * حساب إجمالي مبالغ الوصولات
     */
    public function getTotalDepositsAttribute()
    {
        return $this->receipts()->sum('amount');
    }

}
