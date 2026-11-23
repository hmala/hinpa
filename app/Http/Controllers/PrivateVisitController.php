<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\TypeSpecialization;
use App\Models\Service_Specialization;
use App\Models\PrivateVisit;
use App\Models\PrivateVisitService;
use App\Models\Doctor;
use App\Models\Fck;
use App\Models\mohs;
use App\Helpers\NumberToArabicWords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class PrivateVisitController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:الأجنحة الخاصة', ['only' => [
            'index', 'create', 'store', 'edit', 'update', 'destroy', 'show',
            'invoice', 'getSpecializations', 'getInstitutions', 'getServicePrice',
            'trashed', 'export', 'qrView', 'getNextNumbers', 'addRefund', 'deleteRefund'
        ]]);
        
        $this->middleware('permission:الأجنحة الخاصة.اضافة', ['only' => ['create', 'store']]);
        $this->middleware('permission:الأجنحة الخاصة.تعديل', ['only' => ['edit', 'update']]);
        $this->middleware('permission:الأجنحة الخاصة.حذف', ['only' => ['destroy']]);
        $this->middleware('permission:الأجنحة الخاصة.فاتورة', ['only' => ['invoice', 'show']]);
        $this->middleware('permission:الأجنحة الخاصة.تصدير', ['only' => ['export']]);
        $this->middleware('permission:الأجنحة الخاصة.ارشيف', ['only' => ['trashed']]);
        $this->middleware('permission:الأجنحة الخاصة.استرداد', ['only' => ['addRefund', 'deleteRefund']]);
    }

    public function invoice($visitId)
    {
        $visit = PrivateVisit::withTrashed()->with([
            'serviceSpecialization',
            'doctor',
            'services.serviceSpecialization',
            'services.doctor'
        ])->findOrFail($visitId);
        
        $privateVisit = $visit;
        $services = $visit->services;
        $totalAmount = $visit->total_amount;

        return view('private-visits.invoice', compact('visit', 'privateVisit', 'services', 'totalAmount'));
    }

    private function buildFilterQuery(Request $request)
    {
        $currentUser = Auth::user();
        $query = PrivateVisit::with(['serviceSpecialization', 'doctor']);

        // تطبيق قيود الصلاحيات حسب نوع المستخدم مع دعم فلاتر البحث
        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            if ($request->filled('moh_id')) {
                $query->where('moh_id', $request->moh_id);
            }
            if ($request->filled('fck_id')) {
                $query->where('fck_id', $request->fck_id);
            }
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            $query->where('moh_id', $currentUser->mohcode);
            if ($request->filled('fck_id')) {
                $query->where('fck_id', $request->fck_id);
            }
        } else {
            $query->where('fck_id', $currentUser->fckid);
        }

        // إذا لم يتم تحديد حالة، أظهر فقط النشطة
        if (!$request->filled('status')) {
            $query->whereNull('cancelled_at');
        } elseif ($request->status === 'cancelled') {
            $query->whereNotNull('cancelled_at');
        }

        // فلترة حسب اسم الزائر / المريض
        if ($request->filled('patient_name')) {
            $name = $request->patient_name;
            $query->where(function($q) use ($name) {
                $q->where('patient_name', 'like', '%' . $name . '%')
                  ->orWhere('visitor_name', 'like', '%' . $name . '%');
            });
        }

        // فلترة حسب رقم الهاتف
        if ($request->filled('patient_phone')) {
            $phone = $request->patient_phone;
            $query->where(function($q) use ($phone) {
                $q->where('patient_phone', 'like', '%' . $phone . '%')
                  ->orWhere('visitor_phone', 'like', '%' . $phone . '%');
            });
        }

        // فلترة حسب الترقيم التسلسلي (باركود)
        if ($request->filled('sequential_number')) {
            $query->where('sequential_number', $request->sequential_number);
        }

        // فلترة حسب الترقيم السنوي
        if ($request->filled('annual_number')) {
            $query->where('annual_number', $request->annual_number);
        }

        // فلترة حسب رقم الوصل
        if ($request->filled('receipt_number')) {
            $query->where('receipt_number', 'like', '%' . $request->receipt_number . '%');
        }

        // فلترة حسب طريقة الدفع
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        // فلترة حسب المستشفى (نص)
        if ($request->filled('hospital')) {
            $query->where('hospital', 'like', '%' . $request->hospital . '%');
        }

        // فلترة حسب تاريخ تنظيم الاستمارة
        if ($request->filled('created_at_from')) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }
        if ($request->filled('created_at_to')) {
            $query->whereDate('created_at', '<=', $request->created_at_to);
        }

        // فلترة حسب تاريخ الزيارة
        if ($request->filled('visit_date_from')) {
            $query->whereDate('visit_date', '>=', $request->visit_date_from);
        }
        if ($request->filled('visit_date_to')) {
            $query->whereDate('visit_date', '<=', $request->visit_date_to);
        }

        // فلترة حسب حالة الدفع
        if ($request->filled('payment_status')) {
            switch ($request->payment_status) {
                case 'paid':
                    $query->where('deposit_amount', '>=', DB::raw('total_amount'));
                    break;
                case 'unpaid':
                    $query->where('deposit_amount', '<', DB::raw('total_amount'));
                    break;
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $query = $this->buildFilterQuery($request);

        $privateVisits = $query->orderBy('visit_date', 'desc')->paginate(10)->appends($request->query());
        $debtVisitsCount = PrivateVisit::whereNull('cancelled_at')->where('deposit_amount', '<', DB::raw('total_amount'))->count();

        // المؤسسات والدوائر المتاحة لمودال البحث المتقدم
        $userMohId = $currentUser->mohcode;
        $userFckId = $currentUser->fckid;
        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            $availableFcks = \App\Models\Fck::all();
            $availableMohs = \App\Models\mohs::all();
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            $availableFcks = \App\Models\Fck::where('moh_id', $userMohId)->get();
            $availableMohs = \App\Models\mohs::where('id', $userMohId)->get();
        } else {
            $availableFcks = \App\Models\Fck::where('id', $userFckId)->get();
            $availableMohs = \App\Models\mohs::where('id', $userMohId)->get();
        }

        $visits = $privateVisits;
        return view('private-visits.index', compact('privateVisits', 'visits', 'debtVisitsCount', 'availableMohs', 'availableFcks'));
    }

    public function create()
    {
        $currentUser = Auth::user();
        $typeSpecializations = TypeSpecialization::all();
        $doctors = \App\Models\Doctor::where('fck_id', $currentUser->fckid)->get();
        
        $userMohId = $currentUser->mohcode;
        $userFckId = $currentUser->fckid;
        $hospital = $currentUser->fck ? $currentUser->fck->Fckname : '';
        $healthDepartment = $currentUser->moh ? ($currentUser->moh->mohname ?? $currentUser->moh->Mohname) : '';
        
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
        
        $currentYear = date('Y');
        
        $lastAnnual = PrivateVisit::where('fck_id', $userFckId)
            ->where('fiscal_year', $currentYear)
            ->orderByRaw('CAST(annual_number AS UNSIGNED) DESC')
            ->first();
        $nextAnnualNumber = $lastAnnual ? ((int)$lastAnnual->annual_number + 1) : 1;
        
        $lastSequential = PrivateVisit::where('fck_id', $userFckId)
            ->orderByRaw('CAST(sequential_number AS UNSIGNED) DESC')
            ->first();
        $nextSequentialNumber = $lastSequential ? ((int)$lastSequential->sequential_number + 1) : 1;
        
        return view('private-visits.create', compact(
            'typeSpecializations', 
            'nextAnnualNumber', 
            'nextSequentialNumber', 
            'currentYear', 
            'doctors',
            'availableFcks',
            'availableMohs',
            'hospital',
            'healthDepartment',
            'userMohId',
            'userFckId'
        ));
    }

    public function getNextNumbers()
    {
        $currentUser = Auth::user();
        $fckId = $currentUser->fckid;
        $currentYear = date('Y');

        DB::beginTransaction();
        try {
            $lastAnnual = PrivateVisit::where('fck_id', $fckId)
                ->where('fiscal_year', $currentYear)
                ->orderByRaw('CAST(annual_number AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();
            $nextAnnualNumber = $lastAnnual ? ((int)$lastAnnual->annual_number + 1) : 1;

            $lastSequential = PrivateVisit::where('fck_id', $fckId)
                ->orderByRaw('CAST(sequential_number AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();
            $nextSequentialNumber = $lastSequential ? ((int)$lastSequential->sequential_number + 1) : 1;

            DB::commit();

            return response()->json([
                'nextAnnualNumber' => $nextAnnualNumber,
                'nextSequentialNumber' => $nextSequentialNumber,
                'currentYear' => $currentYear
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'فشل في توليد الأرقام'], 500);
        }
    }

    public function store(Request $request)
    {
        Log::info('Request data (Visits):', $request->all());

        // التحقق من صحة البيانات المرسلة من واجهة الزيارات والخدمات الخارجية
        $validated = $request->validate([
            'hospital' => 'nullable|string|max:255',
            'health_department' => 'nullable|string|max:255',
            'moh_id' => 'nullable|exists:mohs,id',
            'fck_id' => 'nullable|exists:fcks,id',
            'visit_date' => 'required|date',
            'service_id' => 'required|exists:service_specialization,id',
            'patients' => 'required|array|min:1',
            'patients.*.visitor_name' => 'required|string|max:255',
            'patients.*.visitor_nationality' => 'nullable|string|max:255',
            'patients.*.doctor_id' => 'nullable|exists:doctors,id',
            'patients.*.payment_type' => 'required|in:كاش,إلكتروني',
            'patients.*.receipt_number' => 'required|string|max:255',
            'patients.*.receipt_date' => 'required|date',
            'patients.*.fee' => 'required|numeric|min:0',
            'patients.*.interest_rate' => 'nullable|numeric|min:0|max:100',
        ], [
            'service_id.required' => 'يرجى تحديد الخدمة التفصيلية المطلوبة.',
            'service_id.exists' => 'الخدمة التفصيلية المختارة غير صالحة.',
            'visit_date.required' => 'تاريخ الزيارة مطلوب.',
            'patients.required' => 'يجب إضافة مريض واحد على الأقل في القائمة.',
            'patients.min' => 'يجب إضافة مريض واحد على الأقل في القائمة.',
            'patients.*.visitor_name.required' => 'اسم المريض / الزائر مطلوب في كل سطر.',
            'patients.*.receipt_number.required' => 'رقم الوصل مطلوب في كل سطر.',
            'patients.*.receipt_date.required' => 'تاريخ الوصل مطلوب في كل سطر.',
            'patients.*.fee.required' => 'المبلغ مطلوب في كل سطر.',
            'patients.*.fee.numeric' => 'المبلغ يجب أن يكون رقماً.',
        ]);

        $currentUser = Auth::user();
        $mohId = $validated['moh_id'] ?? ($currentUser->mohcode ?: 1);
        $fckId = $validated['fck_id'] ?? ($currentUser->fckid ?: 1);

        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                if ($mohId != $currentUser->mohcode) {
                    return redirect()->back()->withErrors(['moh_id' => 'غير مسموح لك بإضافة بيانات لدوائر أخرى'])->withInput();
                }
            } else {
                if ($fckId != $currentUser->fckid) {
                    return redirect()->back()->withErrors(['fck_id' => 'غير مسموح لك بإضافة بيانات لمؤسسات أخرى'])->withInput();
                }
            }
        }

        $hospital = !empty($validated['hospital']) ? $validated['hospital'] : ($currentUser->fck ? $currentUser->fck->Fckname : '');
        $healthDepartment = !empty($validated['health_department']) ? $validated['health_department'] : ($currentUser->moh ? ($currentUser->moh->mohname ?? $currentUser->moh->Mohname) : '');

        try {
            DB::beginTransaction();

            $currentYear = date('Y', strtotime($validated['visit_date']));

            $lastAnnual = PrivateVisit::where('fck_id', $fckId)
                ->where('fiscal_year', $currentYear)
                ->orderByRaw('CAST(annual_number AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();
            $nextAnnualNumber = $lastAnnual ? ((int)$lastAnnual->annual_number + 1) : 1;

            $lastSequential = PrivateVisit::where('fck_id', $fckId)
                ->orderByRaw('CAST(sequential_number AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();
            $nextSequentialNumber = $lastSequential ? ((int)$lastSequential->sequential_number + 1) : 1;

            foreach ($validated['patients'] as $patient) {
                $fee = floatval($patient['fee'] ?? 0);
                $paymentType = $patient['payment_type'] ?? 'كاش';
                $interestRate = ($paymentType === 'كاش') ? floatval($patient['interest_rate'] ?? 0) : 0;
                $interestAmount = round(($fee * ($interestRate / 100)), 2);
                $totalAmount = $fee + $interestAmount;
                $doctorId = !empty($patient['doctor_id']) && $patient['doctor_id'] !== 'null' ? $patient['doctor_id'] : null;

                $privateVisit = PrivateVisit::create([
                    'hospital' => $hospital,
                    'health_department' => $healthDepartment,
                    'moh_id' => $mohId,
                    'fck_id' => $fckId,
                    'visitor_name' => $patient['visitor_name'],
                    'patient_name' => $patient['visitor_name'],
                    'visitor_nationality' => $patient['visitor_nationality'] ?? 'عراقي',
                    'patient_nationality' => $patient['visitor_nationality'] ?? 'عراقي',
                    'annual_number' => $nextAnnualNumber++,
                    'fiscal_year' => $currentYear,
                    'sequential_number' => $nextSequentialNumber++,
                    'visit_date' => $validated['visit_date'],
                    'visit_time' => now()->format('H:i'),
                    'service_id' => $validated['service_id'],
                    'doctor_id' => $doctorId,
                    'service_fee' => $fee,
                    'payment_type' => $paymentType,
                    'interest_rate' => $interestRate,
                    'interest_amount' => $interestAmount,
                    'receipt_number' => $patient['receipt_number'],
                    'receipt_date' => $patient['receipt_date'],
                    'total_amount' => $totalAmount,
                ]);

                DB::table('private_visit_services')->insert([
                    'private_visit_id' => $privateVisit->id,
                    'service_id' => $validated['service_id'],
                    'service_fee' => $fee,
                    'patient_amount' => $totalAmount,
                    'insurance_amount' => 0,
                    'doctor_id' => $doctorId,
                    'total_amount' => $totalAmount,
                    'operation_degree' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('private-visits.index')
                ->with('success', 'تم حفظ وتأكيد زيارات المرضى بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving private visits: ' . $e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء حفظ الزيارات: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $visit = PrivateVisit::withTrashed()->with(['serviceSpecialization', 'doctor', 'services.serviceSpecialization', 'services.doctor'])->findOrFail($id);
        $privateVisit = $visit;
        $services = $visit->services;
        $totalAmount = $visit->total_amount;

        return view('private-visits.show', compact('visit', 'privateVisit', 'services', 'totalAmount'));
    }

    public function edit($id)
    {
        $visit = PrivateVisit::findOrFail($id);
        $privateVisit = $visit;

        $currentUser = Auth::user();
        $doctors = \App\Models\Doctor::where('fck_id', $visit->fck_id)->get();

        $subSpecializations = Service::where('type_specializations_id', 8)->orWhereNull('type_specializations_id')->orderBy('sername', 'asc')->get();

        $selectedService = Service_Specialization::find($visit->service_id);
        $selectedSubSpecId = null;
        $detailServices = collect();

        if ($selectedService) {
            $subService = Service::where('sercode', $selectedService->service_id)->orWhere('id', $selectedService->service_id)->first();
            $selectedSubSpecId = $subService ? $subService->id : null;
            if ($subService) {
                $detailServices = Service_Specialization::where('service_id', $subService->sercode)
                    ->orWhere('service_id', $subService->id)
                    ->orderBy('namesv', 'asc')
                    ->get();
            }
        }

        return view('private-visits.edit', compact('visit', 'privateVisit', 'subSpecializations', 'selectedSubSpecId', 'detailServices', 'doctors'));
    }

    public function update(Request $request, $id)
    {
        $privateVisit = PrivateVisit::findOrFail($id);

        $validated = $request->validate([
            'service_id' => 'required|exists:service_specialization,id',
            'visitor_name' => 'required|string|max:255',
            'visitor_nationality' => 'nullable|string|max:255',
            'doctor_id' => 'nullable|exists:doctors,id',
            'visit_date' => 'required|date',
            'payment_type' => 'required|in:كاش,إلكتروني',
            'receipt_number' => 'required|string|max:255',
            'receipt_date' => 'required|date',
            'service_fee' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
        ], [
            'service_id.required' => 'يرجى اختيار الخدمة التفصيلية.',
            'visitor_name.required' => 'اسم المريض / الزائر مطلوب.',
            'visit_date.required' => 'تاريخ الزيارة مطلوب.',
            'receipt_number.required' => 'رقم الوصل مطلوب.',
            'receipt_date.required' => 'تاريخ الوصل مطلوب.',
            'service_fee.required' => 'المبلغ الأساسي للخدمة مطلوب.',
        ]);

        try {
            DB::beginTransaction();

            $fee = floatval($validated['service_fee']);
            $paymentType = $validated['payment_type'];
            $interestRate = ($paymentType === 'كاش') ? floatval($validated['interest_rate'] ?? 0) : 0;
            $interestAmount = round(($fee * ($interestRate / 100)), 2);
            $totalAmount = $fee + $interestAmount;

            $privateVisit->update([
                'visitor_name' => $validated['visitor_name'],
                'patient_name' => $validated['visitor_name'],
                'visitor_nationality' => $validated['visitor_nationality'] ?? 'عراقي',
                'patient_nationality' => $validated['visitor_nationality'] ?? 'عراقي',
                'service_id' => $validated['service_id'],
                'doctor_id' => $validated['doctor_id'] ?: null,
                'visit_date' => $validated['visit_date'],
                'payment_type' => $paymentType,
                'receipt_number' => $validated['receipt_number'],
                'receipt_date' => $validated['receipt_date'],
                'service_fee' => $fee,
                'interest_rate' => $interestRate,
                'interest_amount' => $interestAmount,
                'total_amount' => $totalAmount,
            ]);

            // تحديث جدول الخدمات التابع للزيارة
            $privateVisit->services()->delete();
            $privateVisit->services()->create([
                'service_id' => $validated['service_id'],
                'service_fee' => $fee,
                'patient_amount' => $totalAmount,
                'insurance_amount' => 0,
                'doctor_id' => $validated['doctor_id'] ?: null,
                'total_amount' => $totalAmount,
                'operation_degree' => null,
            ]);

            DB::commit();

            return redirect()->route('private-visits.index')
                ->with('success', 'تم تحديث سجل الزيارة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating private visit: ' . $e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء التعديل: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $privateVisit = PrivateVisit::findOrFail($id);
        $privateVisit->delete();

        return redirect()->route('private-visits.index')
            ->with('success', 'تم حذف سجل الزيارة بنجاح');
    }

    public function getSpecializations(Request $request)
    {
        try {
            $section_id = $request->input('section_id');
            if ($section_id) {
                $results = Service::where('type_specializations_id', $section_id)->get();
            } else {
                $results = Service::all();
            }
            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Error fetching specializations (visits): ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getServiceSpecializations(Request $request)
    {
        try {
            $serviceId = $request->input('service_id');
            if (!$serviceId) {
                return response()->json([], 400);
            }
            $service = Service::where('id', $serviceId)->orWhere('sercode', $serviceId)->first();
            $sercode = $service ? $service->sercode : $serviceId;
            $serviceDbId = $service ? $service->id : $serviceId;

            $services = \App\Models\Service_Specialization::where('service_id', $sercode)
                ->orWhere('service_id', $serviceDbId)
                ->orderBy('namesv', 'asc')
                ->get();
            return response()->json($services);
        } catch (\Exception $e) {
            Log::error('Error fetching service specializations (visits): ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getInstitutions(Request $request)
    {
        try {
            $moh_id = $request->input('moh_id');
            $fckt = $request->input('fckt');
            if (!$moh_id) {
                return response()->json([], 400);
            }
            $query = \App\Models\Fck::where('moh_id', $moh_id);
            if ($fckt) {
                $query->where('fctypesid', $fckt);
            }
            $institutions = $query->get();
            return response()->json($institutions);
        } catch (\Exception $e) {
            Log::error('Error fetching institutions (visits): ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ في جلب المؤسسات: ' . $e->getMessage()], 500);
        }
    }

    public function getServicePrice(Request $request)
    {
        return response()->json(['price' => 0]);
    }

    public function trashed()
    {
        $privateVisits = PrivateVisit::withTrashed()
            ->where('cancelled_at', '!=', null)
            ->leftJoin('users', 'private_visits.cancelled_by', '=', 'users.id')
            ->select('private_visits.*', 'users.name as cancelled_by_name')
            ->orderBy('private_visits.cancelled_at', 'desc')
            ->paginate(10);
        return view('private-visits.trashed', compact('privateVisits'));
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000'
        ]);

        $privateVisit = PrivateVisit::findOrFail($id);

        if ($privateVisit->cancelled_at) {
            return back()->withErrors(['error' => 'هذه القائمة ملغاة مسبقاً']);
        }

        $privateVisit->update([
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id()
        ]);

        return back()->with('success', 'تم إبطال القائمة بنجاح');
    }

    public function addRefund(Request $request, $visitId)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'receipt_number' => 'required|string|max:255',
            'refund_date' => 'required|date',
            'receiver_name' => 'required|string|max:255',
            'receiver_id_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $privateVisit = PrivateVisit::findOrFail($visitId);

        $totalDepositsWithoutInterest = $privateVisit->receipts->sum('amount');
        $remaining = $totalDepositsWithoutInterest - $privateVisit->total_amount;
        $alreadyRefunded = $privateVisit->refund_amount;
        $availableForRefund = $remaining - $alreadyRefunded;

        if ($validated['amount'] > $availableForRefund) {
            return back()->withErrors(['amount' => 'المبلغ المطلوب استرداده يتجاوز المتاح (' . number_format($availableForRefund, 0) . ' د.ع)']);
        }

        try {
            DB::beginTransaction();

            PrivateVisitRefund::create([
                'private_visit_id' => $privateVisit->id,
                'amount' => $validated['amount'],
                'receipt_number' => $validated['receipt_number'],
                'refund_date' => $validated['refund_date'],
                'receiver_name' => $validated['receiver_name'],
                'receiver_id_number' => $validated['receiver_id_number'],
                'notes' => $validated['notes'],
            ]);

            $newRefundAmount = $alreadyRefunded + $validated['amount'];
            $refundStatus = ($newRefundAmount >= $remaining) ? 'completed' : 'partial';

            $privateVisit->update([
                'refund_amount' => $newRefundAmount,
                'refund_status' => $refundStatus,
            ]);

            DB::commit();
            return redirect()->route('private-visits.refund-receipt', $privateVisit->id)
                ->with('success', 'تم إضافة وصل الاسترداد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding refund (visits): ' . $e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة وصل الاسترداد']);
        }
    }

    public function deleteRefund($refundId)
    {
        try {
            DB::beginTransaction();

            $refund = PrivateVisitRefund::findOrFail($refundId);
            $privateVisit = $refund->privateVisit;

            $refundAmount = $refund->amount;
            $refund->delete();

            $newRefundAmount = $privateVisit->refund_amount - $refundAmount;
            
            $totalDepositsWithoutInterest = $privateVisit->receipts->sum('amount');
            $remaining = $totalDepositsWithoutInterest - $privateVisit->total_amount;
            
            $refundStatus = 'none';
            if ($newRefundAmount > 0 && $newRefundAmount < $remaining) {
                $refundStatus = 'partial';
            } elseif ($newRefundAmount >= $remaining) {
                $refundStatus = 'completed';
            }

            $privateVisit->update([
                'refund_amount' => max(0, $newRefundAmount),
                'refund_status' => $refundStatus,
            ]);

            DB::commit();
            return back()->with('success', 'تم حذف وصل الاسترداد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting refund (visits): ' . $e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف وصل الاسترداد']);
        }
    }

    public function refundReceipt($visitId)
    {
        $privateVisit = PrivateVisit::with(['refunds' => function($query) {
            $query->latest();
        }])->findOrFail($visitId);

        if ($privateVisit->refunds->isEmpty()) {
            return back()->withErrors(['error' => 'لا توجد وصولات استرداد لهذا السجل']);
        }

        $latestRefund = $privateVisit->refunds->first();
        return view('private-visits.refund-receipt', compact('privateVisit', 'latestRefund'));
    }

    public function export(Request $request)
    {
        $query = PrivateVisit::with(['serviceSpecialization', 'doctor']);

        if (!$request->filled('status')) {
            $query->whereNull('cancelled_at');
        }

        if ($request->filled('patient_name')) {
            $query->where('patient_name', 'like', '%' . $request->patient_name . '%');
        }

        if ($request->filled('hospital')) {
            $query->where('hospital', 'like', '%' . $request->hospital . '%');
        }

        if ($request->filled('sequential_number')) {
            $query->where('sequential_number', $request->sequential_number);
        }

        if ($request->filled('created_at_from')) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }
        if ($request->filled('created_at_to')) {
            $query->whereDate('created_at', '<=', $request->created_at_to);
        }

        if ($request->filled('payment_status')) {
            switch ($request->payment_status) {
                case 'paid':
                    $query->where('deposit_amount', '>=', DB::raw('total_amount'));
                    break;
                case 'unpaid':
                    $query->where('deposit_amount', '<', DB::raw('total_amount'));
                    break;
            }
        }

        $privateVisits = $query->orderBy('created_at', 'desc')->get();

        return \Excel::download(new \App\Exports\PrivateVisitDetailsExport($request), 'تقرير_الزيارات_الخارجية_' . date('Y-m-d') . '.xlsx');
    }
}
