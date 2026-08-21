<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fck extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    public function moh()
    {
        return $this->belongsTo('App\Models\mohs');
    }
    
    public function fckType()
    {
        return $this->belongsTo(Fctypes::class, 'fctypesid');
    }
    
    public function fck()
    {
        return $this->belongsTo('App\Models\fck');
    }

    public function pations()
    {
        return $this->hasMany('App\Models\pations', 'fck_id', 'id');
    }
}
