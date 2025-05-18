<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Spport\Facades\Auth;

class Service_Specialization extends Model
{

    protected $guarded = [];
    public function TypeSpecialization()
    {
    return $this->belongsTo('App\Models\TypeSpecialization');
    }
    
}
