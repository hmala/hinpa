<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'fck_id',
        'month',
        'year',
        'attachment',
    ];
    public function fck()
{
    return $this->belongsTo(fck::class, 'fck_id'); // استخدم صيغة Laravel بشكل كامل
}
}