<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivateWing;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoicesExport;

class InvoicesController extends Controller
{
    public function index(Request $request)
    {
        // التحقق من وجود أي معايير بحث
        $hasSearchCriteria = $request->hasAny(['date_from', 'date_to', 'receipt_number', 'patient_name', 'month', 'year']);

        if (!$hasSearchCriteria) {
            // إذا لم يكن هناك بحث، نرسل collection فارغة
            $invoices = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            return view('invoices.index', compact('invoices'));
        }

        $query = PrivateWing::with(['receipts', 'privateWingServices']);

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

        return view('invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = PrivateWing::with([
            'receipts', 
            'privateWingServices.service.service', 
            'privateWingServices.doctor'
        ])->findOrFail($id);

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