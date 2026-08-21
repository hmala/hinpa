<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
      protected $fillable = [
        'sercode',
        'sername',
        'type_specializations_id'
    ];    /**
     * العلاقة مع التخصصات
     */    public function typeSpecializations()
    {
        return $this->belongsToMany(TypeSpecialization::class, 'service_specialization', 'service_id', 'type_specializations_id', 'sercode')
                    ->withPivot('codesv', 'namesv', 'price', 'notes')
                    ->withTimestamps();
    }
}
