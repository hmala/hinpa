<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivateWingReceipt;
use App\Models\PrivateWing;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReceiptsExport;

class ReceiptsController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:الأجنحة الخاصة', ['only' => ['index', 'export']]);
    }
    
    public function index(Request $request)
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
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

        // التحقق من وجود أي معايير بحث
        $hasSearchCriteria = $request->hasAny(['date_from', 'date_to', 'receipt_number', 'month', 'year', 'moh_id', 'fck_id']);

        if (!$hasSearchCriteria) {
            // إذا لم يكن هناك بحث، نرسل collection فارغة
            $receipts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            return view('receipts.index', compact('receipts', 'availableFcks', 'availableMohs'));
        }

        $query = PrivateWingReceipt::with('privateWing');

        // تطبيق قيود الصلاحيات والبحث حسب الدائرة والمستشفى
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

        // فلترة حسب التاريخ إذا تم تحديده
        if ($request->filled('date_from')) {
            $query->where('receipt_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('receipt_date', '<=', $request->date_to);
        }

        // فلترة حسب رقم الوصل
        if ($request->filled('receipt_number')) {
            $query->where('receipt_number', 'like', '%' . $request->receipt_number . '%');
        }

        // فلترة حسب الشهر
        if ($request->filled('month')) {
            $query->whereMonth('receipt_date', $request->month);
        }

        // فلترة حسب السنة
        if ($request->filled('year')) {
            $query->whereYear('receipt_date', $request->year);
        }

        $receipts = $query->orderBy('receipt_date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        return view('receipts.index', compact('receipts', 'availableFcks', 'availableMohs'));
    }

    public function show($id)
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $receipt = PrivateWingReceipt::with('privateWing')->findOrFail($id);

        // تحقق من وصول المستخدم لسجل الوصل
        $pw = $receipt->privateWing;
        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                if ($pw->moh_id != $currentUser->mohcode) abort(403);
            } else {
                if ($pw->fck_id != $currentUser->fckid) abort(403);
            }
        }

        return view('receipts.show', compact('receipt'));
    }

    /**
     * تصدير بيانات الوصولات إلى Excel
     */
    public function export(Request $request)
    {
        $fileName = 'receipts_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new ReceiptsExport($request), $fileName);
    }
}