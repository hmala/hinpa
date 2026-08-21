<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivateWing;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PrivateWingDetailsExport;

class PrivateWingDetailsController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:تفاصيل الأجنحة الخاصة', ['only' => ['index', 'export']]);
    }
    
    /**
     * عرض تفاصيل الأجنحة الخاصة
     */
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
        $hasSearchCriteria = $request->hasAny(['patient_name', 'file_number', 'entry_date_from', 'entry_date_to', 'month', 'year', 'moh_id', 'fck_id']);

        if (!$hasSearchCriteria) {
            // إذا لم يكن هناك بحث، نرسل collection فارغة
            $privateWings = collect([]);
            
            // جلب الخدمات المستخدمة في الأجنحة الخاصة (مع تطبيق قيود الصلاحيات)
            $usedServicesQuery = \App\Models\PrivateWingService::with('service')->select('service_id');

            if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
                // لا فلترة
            } elseif ($currentUser->roles_name === ["stat-doh"]) {
                $usedServicesQuery->whereHas('privateWing', function ($q) use ($currentUser) {
                    $q->where('moh_id', $currentUser->mohcode);
                });
            } else {
                $usedServicesQuery->whereHas('privateWing', function ($q) use ($currentUser) {
                    $q->where('fck_id', $currentUser->fckid);
                });
            }

            $usedServices = $usedServicesQuery
                ->distinct()
                ->get()
                ->pluck('service.namesv', 'service_id')
                ->filter()
                ->sort();

            return view('private-wing-details.index', compact('privateWings', 'usedServices', 'availableFcks', 'availableMohs'));
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

        // جلب الخدمات المستخدمة في الأجنحة الخاصة (مع تطبيق نفس قيود الصلاحيات أعلاه)
        $usedServicesQuery = \App\Models\PrivateWingService::with('service')->select('service_id');

        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            // لا فلترة
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            $usedServicesQuery->whereHas('privateWing', function ($q) use ($currentUser) {
                $q->where('moh_id', $currentUser->mohcode);
            });
        } else {
            $usedServicesQuery->whereHas('privateWing', function ($q) use ($currentUser) {
                $q->where('fck_id', $currentUser->fckid);
            });
        }

        $usedServices = $usedServicesQuery
            ->distinct()
            ->get()
            ->pluck('service.namesv', 'service_id')
            ->filter()
            ->sort();

        return view('private-wing-details.index', compact('privateWings', 'usedServices', 'availableFcks', 'availableMohs'));
    }

    /**
     * تصدير تفاصيل الأجنحة الخاصة إلى Excel
     */
    public function export(Request $request)
    {
        return Excel::download(new PrivateWingDetailsExport($request), 'private_wing_details_' . date('Y-m-d') . '.xlsx');
    }
}