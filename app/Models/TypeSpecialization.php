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
     */    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_specialization', 'type_specializations_id', 'service_id')
            ->withPivot('codesv', 'namesv', 'price', 'notes')
            ->withTimestamps();
    }
}
