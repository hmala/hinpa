<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivateVisit;
use App\Models\Mohs;
use App\Models\Fck;
use App\Models\Doctor;
use App\Models\Service_Specialization;
use Illuminate\Support\Facades\Auth;
use App\Exports\PrivateVisitDetailsExport;
use Maatwebsite\Excel\Facades\Excel;

class PrivateVisitDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $userMohId = $currentUser->mohcode;
        $userFckId = $currentUser->fckid;

        $userRoles = is_array($currentUser->roles_name) ? $currentUser->roles_name : (is_string($currentUser->roles_name) ? json_decode($currentUser->roles_name, true) : []);
        $isAdmin = (is_array($userRoles) && in_array('Admin', $userRoles)) || $currentUser->hasRole('Admin') || (is_array($userRoles) && in_array('mohst', $userRoles));

        // استعلام أساسي للسجلات المصرح بها للمستخدم
        $baseVisitsQuery = PrivateVisit::query();
        if (!$isAdmin) {
            if (is_array($userRoles) && in_array('stat-doh', $userRoles)) {
                $baseVisitsQuery->where('moh_id', $userMohId);
            } else {
                $baseVisitsQuery->where('fck_id', $userFckId);
            }
        }

        // جلب الدوائر والمؤسسات والأطباء المدخلة فعلياً فقط في سجلات الزيارات
        $activeMohIds = (clone $baseVisitsQuery)->whereNotNull('moh_id')->distinct()->pluck('moh_id');
        $activeFckIds = (clone $baseVisitsQuery)->whereNotNull('fck_id')->distinct()->pluck('fck_id');
        $activeDoctorIds = (clone $baseVisitsQuery)->whereNotNull('doctor_id')->distinct()->pluck('doctor_id');

        $availableMohs = Mohs::whereIn('id', $activeMohIds)->orderBy('mohname', 'asc')->get();
        $availableFcks = Fck::whereIn('id', $activeFckIds)->orderBy('Fckname', 'asc')->get();
        $doctors = Doctor::whereIn('id', $activeDoctorIds)->orderBy('name', 'asc')->get();

        // جلب الخدمات المدخلة فعلياً فقط في سجلات الزيارات
        $mainServiceIds = (clone $baseVisitsQuery)->whereNotNull('service_id')->distinct()->pluck('service_id');
        $subServiceIds = \Illuminate\Support\Facades\DB::table('private_visit_services')
            ->whereIn('private_visit_id', (clone $baseVisitsQuery)->select('id'))
            ->whereNotNull('service_id')
            ->distinct()
            ->pluck('service_id');

        $activeServiceIds = $mainServiceIds->merge($subServiceIds)->unique()->filter();
        $services = Service_Specialization::whereIn('id', $activeServiceIds)->orderBy('namesv', 'asc')->get();

        $query = PrivateVisit::with(['serviceSpecialization', 'doctor']);

        // تقييد الصلاحيات حسب الدور
        if (!$isAdmin) {
            if (is_array($userRoles) && in_array('stat-doh', $userRoles)) {
                $query->where('moh_id', $userMohId);
            } else {
                $query->where('fck_id', $userFckId);
            }
        }

        // فلاتر البحث
        if ($request->filled('moh_id')) {
            $query->where('moh_id', $request->moh_id);
        }
        if ($request->filled('fck_id')) {
            $query->where('fck_id', $request->fck_id);
        }
        if ($request->filled('patient_name')) {
            $name = $request->patient_name;
            $query->where('visitor_name', 'like', "%$name%");
        }
        if ($request->filled('receipt_number')) {
            $query->where('receipt_number', 'like', '%' . $request->receipt_number . '%');
        }
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('service_id')) {
            $serviceId = $request->service_id;
            $query->where(function($q) use ($serviceId) {
                $q->where('service_id', $serviceId)
                  ->orWhereHas('services', function($sq) use ($serviceId) {
                      $sq->where('service_id', $serviceId);
                  });
            });
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('cancelled_at');
            } elseif ($request->status === 'cancelled') {
                $query->whereNotNull('cancelled_at');
            }
        }

        // فلترة الفترة الزمنية المرنة
        $dateFrom = $request->input('date_from', $request->input('receipt_date_from', $request->input('created_at_from')));
        $dateTo = $request->input('date_to', $request->input('receipt_date_to', $request->input('created_at_to')));

        if ($dateFrom && $dateTo) {
            $query->where(function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('visit_date', [$dateFrom, $dateTo])
                  ->orWhereBetween('receipt_date', [$dateFrom, $dateTo]);
            });
        } elseif ($dateFrom) {
            $query->where(function($q) use ($dateFrom) {
                $q->whereDate('visit_date', '>=', $dateFrom)
                  ->orWhereDate('receipt_date', '>=', $dateFrom);
            });
        } elseif ($dateTo) {
            $query->where(function($q) use ($dateTo) {
                $q->whereDate('visit_date', '<=', $dateTo)
                  ->orWhereDate('receipt_date', '<=', $dateTo);
            });
        }

        // حساب الإحصائيات والمجاميع الإجمالية للفترة المحددة
        $statsQuery = clone $query;
        $totalVisitsCount = $statsQuery->count();
        $totalCash = (clone $statsQuery)->where('payment_type', 'كاش')->sum('total_amount');
        $totalElectronic = (clone $statsQuery)->where('payment_type', 'إلكتروني')->sum('total_amount');
        $grandTotalAmount = (clone $statsQuery)->sum('total_amount');
        $totalServiceFees = (clone $statsQuery)->sum('service_fee');

        $visits = $query->orderBy('id', 'desc')->paginate(25)->appends($request->query());
        $receipts = $visits;

        $selectedServiceName = '';
        if ($request->filled('service_id')) {
            $serviceObj = Service_Specialization::find($request->service_id);
            $selectedServiceName = $serviceObj ? $serviceObj->namesv : '';
        }

        return view('private-visit-details.index', compact(
            'visits',
            'receipts',
            'availableFcks',
            'availableMohs',
            'doctors',
            'services',
            'totalVisitsCount',
            'totalCash',
            'totalElectronic',
            'grandTotalAmount',
            'totalServiceFees',
            'dateFrom',
            'dateTo',
            'selectedServiceName'
        ));
    }

    public function export(Request $request)
    {
        $fileName = 'تقرير_الزيارات_الخارجية_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new PrivateVisitDetailsExport($request), $fileName);
    }

    /**
     * طباعة كشف التقرير المعتمد (صفحة طباعة مستقلة ونظيفة بدون عناصر موقع وبكل سجلات الفترة)
     */
    public function printReport(Request $request)
    {
        $currentUser = Auth::user();
        $userMohId = $currentUser->mohcode;
        $userFckId = $currentUser->fckid;

        $userRoles = is_array($currentUser->roles_name) ? $currentUser->roles_name : (is_string($currentUser->roles_name) ? json_decode($currentUser->roles_name, true) : []);
        $isAdmin = (is_array($userRoles) && in_array('Admin', $userRoles)) || $currentUser->hasRole('Admin') || (is_array($userRoles) && in_array('mohst', $userRoles));

        $query = PrivateVisit::with(['serviceSpecialization', 'doctor']);

        // تقييد الصلاحيات
        if (!$isAdmin) {
            if (is_array($userRoles) && in_array('stat-doh', $userRoles)) {
                $query->where('moh_id', $userMohId);
            } else {
                $query->where('fck_id', $userFckId);
            }
        }

        // تطبيق الفلاتر
        if ($request->filled('moh_id')) {
            $query->where('moh_id', $request->moh_id);
        }
        if ($request->filled('fck_id')) {
            $query->where('fck_id', $request->fck_id);
        }
        if ($request->filled('patient_name')) {
            $name = $request->patient_name;
            $query->where('visitor_name', 'like', "%$name%");
        }
        if ($request->filled('receipt_number')) {
            $query->where('receipt_number', 'like', '%' . $request->receipt_number . '%');
        }
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('service_id')) {
            $serviceId = $request->service_id;
            $query->where(function($q) use ($serviceId) {
                $q->where('service_id', $serviceId)
                  ->orWhereHas('services', function($sq) use ($serviceId) {
                      $sq->where('service_id', $serviceId);
                  });
            });
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('cancelled_at');
            } elseif ($request->status === 'cancelled') {
                $query->whereNotNull('cancelled_at');
            }
        }

        $dateFrom = $request->input('date_from', $request->input('receipt_date_from', $request->input('created_at_from')));
        $dateTo = $request->input('date_to', $request->input('receipt_date_to', $request->input('created_at_to')));

        if ($dateFrom && $dateTo) {
            $query->where(function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('visit_date', [$dateFrom, $dateTo])
                  ->orWhereBetween('receipt_date', [$dateFrom, $dateTo]);
            });
        } elseif ($dateFrom) {
            $query->where(function($q) use ($dateFrom) {
                $q->whereDate('visit_date', '>=', $dateFrom)
                  ->orWhereDate('receipt_date', '>=', $dateFrom);
            });
        } elseif ($dateTo) {
            $query->where(function($q) use ($dateTo) {
                $q->whereDate('visit_date', '<=', $dateTo)
                  ->orWhereDate('receipt_date', '<=', $dateTo);
            });
        }

        // جلب جميع السجلات للتقرير بدون Pagination للطباعة الكاملة
        $visits = $query->orderBy('id', 'asc')->get();

        // حساب الإجماليات
        $totalCount = $visits->count();
        $totalCash = $visits->where('payment_type', 'كاش')->sum('total_amount');
        $totalElectronic = $visits->where('payment_type', 'إلكتروني')->sum('total_amount');
        $grandTotal = $visits->sum('total_amount');
        $totalFees = $visits->sum('service_fee');
        $totalInterests = $visits->sum('interest_amount');

        // تحديد اسم المستشفى والدائرة
        $hospitalName = '';
        $mohName = '';
        if ($request->filled('fck_id')) {
            $fck = Fck::find($request->fck_id);
            $hospitalName = $fck ? $fck->Fckname : '';
        } elseif ($currentUser->fck) {
            $hospitalName = $currentUser->fck->Fckname;
        }

        if ($request->filled('moh_id')) {
            $moh = Mohs::find($request->moh_id);
            $mohName = $moh ? $moh->mohname : '';
        } elseif ($currentUser->moh) {
            $mohName = $currentUser->moh->mohname ?? '';
        }

        $selectedServiceName = '';
        if ($request->filled('service_id')) {
            $serviceObj = Service_Specialization::find($request->service_id);
            $selectedServiceName = $serviceObj ? $serviceObj->namesv : '';
        }

        return view('private-visit-details.print', compact(
            'visits',
            'totalCount',
            'totalCash',
            'totalElectronic',
            'grandTotal',
            'totalFees',
            'totalInterests',
            'dateFrom',
            'dateTo',
            'hospitalName',
            'mohName',
            'selectedServiceName'
        ));
    }
}
