<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivateWing;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoicesExport;

class InvoicesController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:الأجنحة الخاصة', ['only' => ['index', 'show', 'export']]);
    }
    
    public function index(Request $request)
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $userMohId = $currentUser->mohcode;
        $userFckId = $currentUser->fckid;

        $activeFckIds = \Illuminate\Support\Facades\DB::table('private_wings')->select('fck_id')
            ->union(\Illuminate\Support\Facades\DB::table('private_visits')->select('fck_id'))
            ->pluck('fck_id');

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

        // التحقق من وجود أي معايير بحث
        $hasSearchCriteria = $request->hasAny(['date_from', 'date_to', 'receipt_number', 'patient_name', 'month', 'year', 'moh_id', 'fck_id']);

        if (!$hasSearchCriteria) {
            // إذا لم يكن هناك بحث، نرسل collection فارغة
            $invoices = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            return view('invoices.index', compact('invoices', 'availableFcks', 'availableMohs'));
        }

        $query = PrivateWing::with(['receipts', 'privateWingServices']);

        // تطبيق قيود الصلاحيات والبحث حسب الدائرة والمستشفى
        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            if ($request->filled('moh_id')) {
                $query->where('moh_id', $request->moh_id);
            }
            if ($request->filled('fck_id')) {
                $query->where('fck_id', $request->fck_id);
            }
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            $query->where('moh_id', $userMohId);
            if ($request->filled('fck_id')) {
                $query->where('fck_id', $request->fck_id);
            }
        } else {
            $query->where('fck_id', $userFckId);
        }

        // فلترة حسب التاريخ إذا تم تحديده
        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->date_to);
        }

        // فلترة حسب رقم القائمة
        if ($request->filled('receipt_number')) {
            $query->where('file_number', 'like', '%' . $request->receipt_number . '%');
        }

        // فلترة حسب اسم المريض
        if ($request->filled('patient_name')) {
            $query->where('patient_name', 'like', '%' . $request->patient_name . '%');
        }

        // فلترة حسب الشهر
        if ($request->filled('month')) {
            $query->whereMonth('entry_date', $request->month);
        }

        // فلترة حسب السنة
        if ($request->filled('year')) {
            $query->whereYear('entry_date', $request->year);
        }

        $invoices = $query->orderBy('entry_date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        return view('invoices.index', compact('invoices', 'availableFcks', 'availableMohs'));
    }

    public function show($id)
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();

        $invoice = PrivateWing::with([
            'receipts', 
            'privateWingServices.service.service', 
            'privateWingServices.doctor'
        ])->findOrFail($id);
        // تحقق من صلاحية الوصول
        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                if ($invoice->moh_id != $currentUser->mohcode) abort(403);
            } else {
                if ($invoice->fck_id != $currentUser->fckid) abort(403);
            }
        }

        return view('invoices.show', compact('invoice'));
    }

    /**
     * تصدير بيانات الفواتير إلى Excel
     */
    public function export(Request $request)
    {
        $fileName = 'invoices_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new InvoicesExport($request), $fileName);
    }
}