<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivateWingReceipt;
use Illuminate\Support\Facades\Auth;
use App\Exports\PrivateVisitDetailsExport;
use Maatwebsite\Excel\Facades\Excel;

class PrivateVisitDetailsController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $userMohId = $currentUser->mohcode;
        $userFckId = $currentUser->fckid;

        $activeFckIds = \Illuminate\Support\Facades\DB::table('private_wings')->select('fck_id')->pluck('fck_id');

        // Fetch available lists for dropdown
        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            $availableFcks = \App\Models\Fck::whereIn('id', $activeFckIds)->get();
            $availableMohs = \App\Models\Mohs::all();
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            $availableFcks = \App\Models\Fck::where('moh_id', $userMohId)->whereIn('id', $activeFckIds)->get();
            $availableMohs = \App\Models\Mohs::where('id', $userMohId)->get();
        } else {
            $availableFcks = \App\Models\Fck::where('id', $userFckId)->get();
            $availableMohs = \App\Models\Mohs::where('id', $userMohId)->get();
        }

        $hasSearchCriteria = $request->hasAny(['patient_name', 'receipt_number', 'receipt_date_from', 'receipt_date_to', 'payment_type', 'moh_id', 'fck_id']);

        if (!$hasSearchCriteria) {
            $receipts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            return view('private-visit-details.index', compact('receipts', 'availableFcks', 'availableMohs'));
        }

        $query = PrivateWingReceipt::with('privateWing')
            ->whereHas('privateWing', function ($q) {
                $q->where('patient_type', 'outpatient');
            });

        // Apply institutional bounds and query filters
        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            if ($request->filled('moh_id')) {
                $query->whereHas('privateWing', function($q) use ($request) {
                    $q->where('moh_id', $request->moh_id);
                });
            }
            if ($request->filled('fck_id')) {
                $query->whereHas('privateWing', function($q) use ($request) {
                    $q->where('fck_id', $request->fck_id);
                });
            }
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            $query->whereHas('privateWing', function ($q) use ($userMohId) {
                $q->where('moh_id', $userMohId);
            });
            if ($request->filled('fck_id')) {
                $query->whereHas('privateWing', function($q) use ($request) {
                    $q->where('fck_id', $request->fck_id);
                });
            }
        } else {
            $query->whereHas('privateWing', function ($q) use ($userFckId) {
                $q->where('fck_id', $userFckId);
            });
        }

        // Search filters
        if ($request->filled('patient_name')) {
            $query->whereHas('privateWing', function($q) use ($request) {
                $q->where('patient_name', 'like', '%' . $request->patient_name . '%');
            });
        }

        if ($request->filled('receipt_number')) {
            $query->where('receipt_number', 'like', '%' . $request->receipt_number . '%');
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('receipt_date_from')) {
            $query->whereDate('receipt_date', '>=', $request->receipt_date_from);
        }
        if ($request->filled('receipt_date_to')) {
            $query->whereDate('receipt_date', '<=', $request->receipt_date_to);
        }

        $receipts = $query->orderBy('receipt_date', 'desc')->paginate(15)->appends($request->query());

        return view('private-visit-details.index', compact('receipts', 'availableFcks', 'availableMohs'));
    }

    public function export(Request $request)
    {
        return Excel::download(new PrivateVisitDetailsExport($request), 'private_visit_receipts_details.xlsx');
    }
}
