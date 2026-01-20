<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class surgery extends Model
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
    public function surg()
    { return $this->belongsTo(surg::class, 'surgtyp');
    }
}
