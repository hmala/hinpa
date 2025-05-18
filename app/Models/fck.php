<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fck extends Model
{
    protected $guarded = [];
    public function moh()
    {
    return $this->belongsTo('App\Models\mohs');
    }
    public function fck()
    {
    return $this->belongsTo('App\Models\fck');
    }
}
