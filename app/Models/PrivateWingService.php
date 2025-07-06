<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PrivateWing;
use App\Models\Service_Specialization;

class PrivateWingService extends Model
{
    use HasFactory;

    protected $table = 'private_wing_services';

    protected $fillable = [
        'private_wing_id',
        'service_id',
        'service_fee',
        'is_daily',
        'total_amount'
    ];

    protected $casts = [
        'service_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_daily' => 'boolean'
    ];

    public function privateWing()
    {
        return $this->belongsTo(PrivateWing::class, 'private_wing_id');
    }

    public function service()
    {
        return $this->belongsTo(Service_Specialization::class, 'service_id');
    }
}
