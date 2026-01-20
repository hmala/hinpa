<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateWingReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'private_wing_id',
        'amount',
        'receipt_number',
        'receipt_date',
        'payment_type',
        'interest_rate',
        'interest_amount'
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'interest_rate' => 'decimal:2',
        'interest_amount' => 'decimal:2'
    ];

    public function privateWing()
    {
        return $this->belongsTo(PrivateWing::class);
    }
}
