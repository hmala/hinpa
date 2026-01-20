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
    public function index(Request $request)
    {
        // التحقق من وجود أي معايير بحث
        $hasSearchCriteria = $request->hasAny(['date_from', 'date_to', 'receipt_number', 'month', 'year']);

        if (!$hasSearchCriteria) {
            // إذا لم يكن هناك بحث، نرسل collection فارغة
            $receipts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            return view('receipts.index', compact('receipts'));
        }

        $query = PrivateWingReceipt::with('privateWing');

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

        return view('receipts.index', compact('receipts'));
    }

    public function show($id)
    {
        $receipt = PrivateWingReceipt::with('privateWing')->findOrFail($id);

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