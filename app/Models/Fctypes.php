<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fctypes extends Model
{
    protected $guarded = [];

    public function moh()
    {
    return $this->belongsTo('App\Models\mohs');
    }
}
