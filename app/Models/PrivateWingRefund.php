<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateWingRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'private_wing_id',
        'amount',
        'receipt_number',
        'refund_date',
        'notes',
        'receiver_name',
        'receiver_id_number',
    ];

    protected $casts = [
        'refund_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * العلاقة مع الجناح الخاص
     */
    public function privateWing()
    {
        return $this->belongsTo(PrivateWing::class);
    }
}
