<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Service_Specialization extends Model
{

    protected $table = 'service_specialization';
    
    protected $fillable = [
        'service_id',
        'type_specializations_id',
        'codesv',
        'namesv',
        'price',
        'notes'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function typeSpecialization()
    {
        return $this->belongsTo(TypeSpecialization::class, 'type_specializations_id');
    }
}
