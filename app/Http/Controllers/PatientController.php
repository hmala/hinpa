<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivateWing;

class PatientController extends Controller
{
    /**
     * عرض قائمة المرضى مع بياناتهم
     */
    public function index()
    {
        $patients = PrivateWing::select([
            'id',
            'patient_name',
            'file_number',
            'deposit_amount',
            'total_amount'
        ])->get();

        return view('patients.index', compact('patients'));
    }
}