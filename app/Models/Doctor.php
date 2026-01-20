<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'job_title',
        'institution',
        'moh_id',
        'fck_id'
    ];

    public function moh()
    {
        return $this->belongsTo(Moh::class);
    }

    public function fck()
    {
        return $this->belongsTo(Fck::class);
    }
}
