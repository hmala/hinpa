<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivateWing;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PrivateWingDetailsExport;

class PrivateWingDetailsController extends Controller
{
    /**
     * عرض تفاصيل الأجنحة الخاصة
     */
    public function index(Request $request)
    {
        // التحقق من وجود أي معايير بحث
        $hasSearchCriteria = $request->hasAny(['patient_name', 'file_number', 'entry_date_from', 'entry_date_to', 'month', 'year']);

        if (!$hasSearchCriteria) {
            // إذا لم يكن هناك بحث، نرسل collection فارغة
            $privateWings = collect([]);
            
            // جلب الخدمات المستخدمة في الأجنحة الخاصة
            $usedServices = \App\Models\PrivateWingService::with('service')
                ->whereHas('privateWing')
                ->select('service_id')
                ->distinct()
                ->get()
                ->pluck('service.namesv', 'service_id')
                ->filter()
                ->sort();

            return view('private-wing-details.index', compact('privateWings', 'usedServices'));
        }

        $query = PrivateWing::with(['receipts', 'privateWingServices.service', 'privateWingServices.doctor', 'privateWingServices.service.typeSpecialization'])
            ->select([
                'id',
                'patient_name',
                'file_number',
                'deposit_amount',
                'total_amount',
                'entry_date',
                'exit_date',
                'days_count',
                'is_insured'
            ]);

        // فلترة حسب اسم المريض
        if ($request->filled('patient_name')) {
            $query->where('patient_name', 'like', '%' . $request->patient_name . '%');
        }

        // فلترة حسب رقم الحساب
        if ($request->filled('file_number')) {
            $query->where('file_number', 'like', '%' . $request->file_number . '%');
        }

        // فلترة حسب تاريخ الدخول من
        if ($request->filled('entry_date_from')) {
            $query->whereDate('entry_date', '>=', $request->entry_date_from);
        }

        // فلترة حسب تاريخ الدخول إلى
        if ($request->filled('entry_date_to')) {
            $query->whereDate('entry_date', '<=', $request->entry_date_to);
        }

        // فلترة حسب الشهر
        if ($request->filled('month')) {
            $query->whereMonth('entry_date', $request->month);
        }

        // فلترة حسب السنة
        if ($request->filled('year')) {
            $query->whereYear('entry_date', $request->year);
        }

        $privateWings = $query->orderBy('created_at', 'desc')->get();

        // جلب الخدمات المستخدمة في الأجنحة الخاصة
        $usedServices = \App\Models\PrivateWingService::with('service')
            ->whereHas('privateWing')
            ->select('service_id')
            ->distinct()
            ->get()
            ->pluck('service.namesv', 'service_id')
            ->filter()
            ->sort();

        return view('private-wing-details.index', compact('privateWings', 'usedServices'));
    }

    /**
     * تصدير تفاصيل الأجنحة الخاصة إلى Excel
     */
    public function export()
    {
        return Excel::download(new PrivateWingDetailsExport, 'private_wing_details_' . date('Y-m-d') . '.xlsx');
    }
}