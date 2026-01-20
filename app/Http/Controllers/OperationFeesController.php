<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivateWingService;
use App\Models\Service;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OperationFeesExport;

class OperationFeesController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $operationName = $request->input('operation_name');
        $operationDegree = $request->input('operation_degree');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // التحقق من وجود أي معايير بحث
        $hasSearchCriteria = $request->hasAny(['month', 'year', 'operation_name', 'operation_degree', 'date_from', 'date_to']);

        // Get operation fees grouped by operation degree
        $operationDegrees = ['الخاصة', 'فوق الكبرى', 'كبرى', 'متوسطة', 'صغرى'];

        if (!$hasSearchCriteria) {
            // إذا لم يكن هناك بحث، نرسل collections فارغة
            $operationFeesByDegree = [];
            foreach ($operationDegrees as $degree) {
                $operationFeesByDegree[$degree] = collect([]);
            }
            $uniqueServices = collect([]);
            return view('operation-fees.index', compact('operationFeesByDegree', 'operationDegrees', 'uniqueServices', 'month', 'year'));
        }

        $operationFeesByDegree = [];

        foreach ($operationDegrees as $degree) {
            $query = PrivateWingService::with(['service' => function($query) {
                $query->with('typeSpecialization');
            }])
            ->where('operation_degree', $degree)
            ->whereHas('service', function($query) {
                $query->where('namesv', 'like', '%عملية%')
                      ->orWhere('namesv', 'like', '%جراحة%')
                      ->orWhere('namesv', 'like', '%عمليات%');
            });

            // فلترة حسب اسم العملية
            if ($operationName) {
                $query->whereHas('service', function($q) use ($operationName) {
                    $q->where('namesv', 'like', '%' . $operationName . '%');
                });
            }

            // فلترة حسب درجة العملية
            if ($operationDegree && $operationDegree !== $degree) {
                continue;
            }

            // إضافة فلتر الشهر والسنة
            if ($month && $year) {
                $query->whereHas('privateWing', function($q) use ($month, $year) {
                    $q->whereMonth('entry_date', $month)
                      ->whereYear('entry_date', $year);
                });
            } elseif ($year) {
                $query->whereHas('privateWing', function($q) use ($year) {
                    $q->whereYear('entry_date', $year);
                });
            } elseif ($month) {
                $query->whereHas('privateWing', function($q) use ($month) {
                    $q->whereMonth('entry_date', $month);
                });
            }

            // فلترة حسب تاريخ من - إلى
            if ($dateFrom) {
                $query->whereHas('privateWing', function($q) use ($dateFrom) {
                    $q->whereDate('entry_date', '>=', $dateFrom);
                });
            }
            if ($dateTo) {
                $query->whereHas('privateWing', function($q) use ($dateTo) {
                    $q->whereDate('entry_date', '<=', $dateTo);
                });
            }

            $operationFeesByDegree[$degree] = $query->selectRaw('service_id, service_fee, COUNT(*) as count, SUM(total_amount) as total_amount')
                ->groupBy('service_id', 'service_fee')
                ->get()
                ->keyBy('service_id');
        }

        // Get all unique services across all degrees
        $allServices = collect();
        foreach ($operationFeesByDegree as $degree => $fees) {
            $allServices = $allServices->merge($fees);
        }
        $uniqueServices = $allServices->unique('service_id')->sortBy(function($service) {
            return $service->service->namesv ?? '';
        });

        return view('operation-fees.index', compact('operationFeesByDegree', 'operationDegrees', 'uniqueServices', 'month', 'year'));
    }

    /**
     * تصدير بيانات رسوم العمليات إلى Excel
     */
    public function export(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $fileName = 'operation_fees';
        if ($month && $year) {
            $fileName .= '_month_' . $month . '_year_' . $year;
        } elseif ($year) {
            $fileName .= '_year_' . $year;
        } elseif ($month) {
            $fileName .= '_month_' . $month;
        }
        $fileName .= '_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new OperationFeesExport($month, $year), $fileName);
    }
}
