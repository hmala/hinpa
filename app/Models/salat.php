<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class salat extends Model
{
    protected $guarded = [];
    public function fck()
    {
    return $this->belongsTo('App\Models\fck');
    }
    public function moh()
    {
    return $this->belongsTo('App\Models\mohs');
    }
    public function salsurs()
    { return $this->belongsTo(salsurs::class, 'salid');
    }
}
