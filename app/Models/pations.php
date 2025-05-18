<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class pations extends Model
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
    public function rdhs()
     { return $this->belongsTo(rdhs::class, 'spebed');
     }
}
