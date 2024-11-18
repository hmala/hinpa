<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pations extends Model
{
    use HasFactory;

    protected $table = 'pations';
    
    protected $fillable = [
        'Recno', 'Untno', 'Doh', 'Spcu', 'Facility_type', 'Hospital',
        'City', 'Kda', 'Nha', 'Gender', 'Edu', 'Job', 'Status',
        'B_date', 'F_City', 'F_Kda', 'F_Nha', 'Place', 'Nation',
        'Date_in', 'Date_out', 'Diag', 'Srg_type', 'Degree1', 'P_s',
        'IsDisabled', 'disty', 'Res1', 'Res2', 'Res3', 'Res4',
        'Totalstay', 'Smoking', 'Sugr', 'Weight', 'Hyper', 'P_Name',
        'Mother_Name', 'Doctor_Name', 'Doctor_Cer', 'Doctor_Spe',
        'PImg1', 'PImg2', 'PImg3', 'Send1', 'Blood1', 'creator', 'save'
    ];

    protected $casts = [
        'B_date' => 'datetime',
        'Date_in' => 'datetime',
        'Date_out' => 'datetime'
    ];
}
