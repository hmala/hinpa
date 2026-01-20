<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pations;
use App\Models\surgery;
use App\Models\hwadth;
use App\Models\cases;
use App\Models\fck;
use Illuminate\Support\Facades\DB;

class SummaryReportController extends Controller
{
    public function index()
    {
        // Get total patients
        $totalPatients = pations::count();
        
        // Get total surgeries
        $totalSurgeries = surgery::count();
        
        // Get total accidents
        $totalAccidents = hwadth::count();
        
        // Get total cases
        $totalCases = cases::count();        // Get facilities with patient counts
        $facilitiesData = fck::withCount('pations')
            ->orderBy('pations_count', 'desc')
            ->take(5)
            ->get();        // Monthly statistics for current year by department
        $monthlyStatsByDept = pations::select(
            'spebed',
            DB::raw('MONTH(pations.created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->join('rdhs', 'pations.spebed', '=', 'rdhs.id')
        ->whereYear('pations.created_at', date('Y'))
        ->groupBy('spebed', DB::raw('MONTH(pations.created_at)'))
        ->orderBy('month')
        ->get();

        // Get departments list
        $departments = \App\Models\rdhs::all();        return view('summary-report', compact(
            'totalPatients',
            'totalSurgeries',
            'totalAccidents',
            'totalCases',
            'facilitiesData',
            'monthlyStatsByDept',
            'departments'
        ));
    }
}
