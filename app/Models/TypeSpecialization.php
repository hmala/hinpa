<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeSpecialization extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tscode',
        'tsname'
    ];    /**
     * العلاقة مع الخدمات
     */
   public function Service_Specialization()
    {
    return $this->belongsTo('App\Models\Service_Specialization');
    }
}
