<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pations;
use App\Exports\PationsExport;
use Maatwebsite\Excel\Facades\Excel;

class PationController extends Controller
{    public function export(Request $request)
    {
        $mohId = $request->input('moh_id');
        
        if ($mohId == 'all') {
            $pations = Pations::with(['moh', 'fck', 'rdhs'])->get();
        } else {
            $pations = Pations::with(['moh', 'fck', 'rdhs'])
                ->whereHas('moh', function($query) use ($mohId) {
                    $query->where('id', $mohId);
                })->get();
        }

        return Excel::download(new PationsExport($pations), 'pations.xlsx');
    }
}
