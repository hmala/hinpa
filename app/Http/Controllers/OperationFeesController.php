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
    function __construct()
    {
        $this->middleware('permission:الأجنحة الخاصة', ['only' => ['index', 'export']]);
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

        $month = $request->input('month');
        $year = $request->input('year');
        $operationName = $request->input('operation_name');
        $operationDegree = $request->input('operation_degree');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // التحقق من وجود أي معايير بحث
        $hasSearchCriteria = $request->hasAny(['month', 'year', 'operation_name', 'operation_degree', 'date_from', 'date_to', 'moh_id', 'fck_id']);

        // Get operation fees grouped by operation degree
        $operationDegrees = ['الخاصة', 'فوق الكبرى', 'كبرى', 'متوسطة', 'صغرى'];

        if (!$hasSearchCriteria) {
            // إذا لم يكن هناك بحث، نرسل collections فارغة
            $operationFeesByDegree = [];
            foreach ($operationDegrees as $degree) {
                $operationFeesByDegree[$degree] = collect([]);
            }
            $uniqueServices = collect([]);
            return view('operation-fees.index', compact('operationFeesByDegree', 'operationDegrees', 'uniqueServices', 'month', 'year', 'availableFcks', 'availableMohs'));
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
                $query->whereHas('privateWing', function($q) use ($userMohId) {
                    $q->where('moh_id', $userMohId);
                });
                if ($request->filled('fck_id')) {
                    $query->whereHas('privateWing', function($q) use ($request) {
                        $q->where('fck_id', $request->fck_id);
                    });
                }
            } else {
                $query->whereHas('privateWing', function($q) use ($userFckId) {
                    $q->where('fck_id', $userFckId);
                });
            }

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

        return view('operation-fees.index', compact('operationFeesByDegree', 'operationDegrees', 'uniqueServices', 'month', 'year', 'availableFcks', 'availableMohs'));
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

        return Excel::download(new OperationFeesExport($month, $year, $request), $fileName);
    }
}
