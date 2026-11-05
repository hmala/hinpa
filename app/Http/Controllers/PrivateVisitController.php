<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\TypeSpecialization;
use App\Models\Service_Specialization;
use App\Models\PrivateVisit;
use App\Models\PrivateVisitService;
use App\Models\PrivateVisitReceipt;
use App\Models\PrivateVisitRefund;
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
        $privateVisit = PrivateVisit::withTrashed()->with(['receipts'])->findOrFail($visitId);

        $services = DB::table('private_visit_services as pvs')
            ->join('service_specialization as ss', 'pvs.service_id', '=', 'ss.id')
            ->leftJoin('doctors as d', 'pvs.doctor_id', '=', 'd.id')
            ->where('pvs.private_visit_id', $privateVisit->id)
            ->select(
                'ss.namesv as service_name',
                'd.name as doctor_name',
                'pvs.service_fee',
                'pvs.total_amount',
                'pvs.operation_degree',
                'pvs.is_insured',
                'pvs.insurance_percentage',
                'pvs.patient_amount',
                'pvs.insurance_amount'
            )
            ->orderBy('ss.namesv', 'asc')
            ->get();

        $totalAmount = $services->sum('total_amount');

        return view('private-visits.invoice', compact('privateVisit', 'services', 'totalAmount'));
    }

    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $query = PrivateVisit::with(['serviceSpecialization', 'doctor']);

        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            // Admin/mohst: عرض كل الزيارات
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            $query->where('moh_id', $currentUser->mohcode);
        } else {
            $query->where('fck_id', $currentUser->fckid);
        }

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

        $privateVisits = $query->orderBy('visit_date', 'desc')->paginate(10)->appends($request->query());
        $debtVisitsCount = PrivateVisit::whereNull('cancelled_at')->where('deposit_amount', '<', DB::raw('total_amount'))->count();

        $visits = $privateVisits;
        return view('private-visits.index', compact('privateVisits', 'visits', 'debtVisitsCount'));
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

        $validated = $request->validate([
            'hospital' => 'required|string|max:255',
            'health_department' => 'required|string|max:255',
            'moh_id' => 'required|exists:mohs,id',
            'fck_id' => 'required|exists:fcks,id',
            'patient_name' => 'required|string|max:255',
            'patient_nationality' => 'nullable|string|max:255',
            'is_insured' => 'nullable|boolean',
            'statistical_number' => 'nullable|string|max:255',
            'visit_date' => 'required|date',
            'visit_time' => 'nullable|date_format:H:i',
            'deposits' => 'required|array|min:1',
            'deposits.*.amount' => 'required|numeric|min:0',
            'deposits.*.number' => 'required|string|max:255',
            'deposits.*.date' => 'required|date',
            'deposits.*.payment_type' => 'required|in:كاش,إلكتروني',
            'deposits.*.interest_rate' => 'required|numeric|min:0|max:100',
            'deposits.*.interest_amount' => 'required|numeric|min:0',
            'services' => 'required|array',
            'services.*.id' => 'required|exists:service_specialization,id',
            'services.*.fee' => 'required|numeric',
            'services.*.is_insured' => 'required|boolean',
            'services.*.insurance_percentage' => 'required|numeric|min:0|max:100',
            'services.*.doctor_id' => 'nullable|exists:doctors,id',
            'services.*.operation_degree' => 'nullable|string|max:255'
        ]);
        
        $currentUser = Auth::user();
        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                if ($validated['moh_id'] != $currentUser->mohcode) {
                    return redirect()->back()->withErrors(['moh_id' => 'غير مسموح لك بإضافة بيانات لدوائر أخرى'])->withInput();
                }
            } else {
                if ($validated['fck_id'] != $currentUser->fckid) {
                    return redirect()->back()->withErrors(['fck_id' => 'غير مسموح لك بإضافة بيانات لمؤسسات أخرى'])->withInput();
                }
            }
        }

        try {
            DB::beginTransaction();

            $fckId = $validated['fck_id'];
            $currentYear = date('Y');

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

            $privateVisit = PrivateVisit::create([
                'hospital' => $validated['hospital'],
                'health_department' => $validated['health_department'],
                'moh_id' => $validated['moh_id'],
                'fck_id' => $validated['fck_id'],
                'patient_name' => $validated['patient_name'],
                'patient_nationality' => $validated['patient_nationality'] ?? null,
                'annual_number' => $nextAnnualNumber,
                'fiscal_year' => $currentYear,
                'sequential_number' => $nextSequentialNumber,
                'statistical_number' => $validated['statistical_number'] ?? null,
                'visit_date' => $validated['visit_date'],
                'visit_time' => $validated['visit_time'] ?? null,
                'is_insured' => $validated['is_insured'] ?? false
            ]);

            $totalDeposits = 0;
            foreach ($validated['deposits'] as $deposit) {
                $privateVisit->receipts()->create([
                    'amount' => $deposit['amount'],
                    'receipt_number' => $deposit['number'],
                    'receipt_date' => $deposit['date'],
                    'payment_type' => $deposit['payment_type'],
                    'interest_rate' => $deposit['interest_rate'],
                    'interest_amount' => $deposit['interest_amount']
                ]);
                $totalDeposits += $deposit['amount'] + $deposit['interest_amount'];
            }
            
            $privateVisit->update([
                'deposit_amount' => $totalDeposits
            ]);
            $totalAmount = 0;
            
            foreach ($validated['services'] as $service) {
                $serviceAmount = $service['fee'];
                $patientAmount = 0;
                $insuranceAmount = 0;
                $insurancePercentage = $service['insurance_percentage'] ?? 100;
                
                if ($service['is_insured']) {
                    $patientAmount = $serviceAmount * ($insurancePercentage / 100);
                    $insuranceAmount = $serviceAmount * ((100 - $insurancePercentage) / 100);
                } else {
                    $patientAmount = $serviceAmount;
                    $insuranceAmount = 0;
                }
                  
                $doctorId = !empty($service['doctor_id']) && $service['doctor_id'] !== 'null' ? $service['doctor_id'] : null;

                DB::table('private_visit_services')->insert([
                    'private_visit_id' => $privateVisit->id,
                    'service_id' => $service['id'],
                    'service_fee' => $service['fee'],
                    'is_insured' => $service['is_insured'],
                    'insurance_percentage' => $insurancePercentage,
                    'patient_amount' => $patientAmount,
                    'insurance_amount' => $insuranceAmount,
                    'doctor_id' => $doctorId,
                    'total_amount' => $serviceAmount,
                    'operation_degree' => $service['operation_degree'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $totalAmount += $patientAmount;
            }

            $privateVisit->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('private-visits.index')
                ->with('success', 'تم إنشاء سجل زيارة الجناح الخاص بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving private visit: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $privateVisit = PrivateVisit::withTrashed()->with(['receipts', 'refunds'])->findOrFail($id);
        
        $services = DB::table('private_visit_services as pvs')
            ->join('service_specialization as ss', 'pvs.service_id', '=', 'ss.id')
            ->leftJoin('doctors as d', 'pvs.doctor_id', '=', 'd.id')
            ->where('pvs.private_visit_id', $privateVisit->id)
            ->select(
                'ss.namesv as service_name',
                'd.name as doctor_name',
                'pvs.service_fee',
                'pvs.total_amount',
                'pvs.operation_degree',
                'pvs.is_insured',
                'pvs.insurance_percentage',
                'pvs.patient_amount',
                'pvs.insurance_amount'
            )
            ->orderBy('ss.namesv', 'asc')
            ->get();

        $totalAmount = $services->sum('total_amount');

        return view('private-visits.show', compact('privateVisit', 'services', 'totalAmount'));
    }

    public function edit($id)
    {
        $privateVisit = PrivateVisit::findOrFail($id);
        $privateVisit->load('services');
        $typeSpecializations = TypeSpecialization::all();

        $currentUser = Auth::user();
        $doctors = \App\Models\Doctor::where('fck_id', $currentUser->fckid)->get();
        
        $services = $privateVisit->privateVisitServices()
            ->join('service_specialization as ss', 'private_visit_services.service_id', '=', 'ss.id')
            ->leftJoin('doctors as d', 'private_visit_services.doctor_id', '=', 'd.id')
            ->select(
                'private_visit_services.id',
                'ss.namesv as service_name',
                'private_visit_services.doctor_id',
                'd.name as doctor_name',
                'private_visit_services.service_fee',
                'private_visit_services.is_insured',
                'private_visit_services.insurance_percentage',
                'private_visit_services.patient_amount',
                'private_visit_services.insurance_amount',
                'private_visit_services.total_amount',
                'private_visit_services.operation_degree'
            )
            ->orderBy('ss.namesv', 'asc')
            ->get();
        
        return view('private-visits.edit', compact('privateVisit', 'typeSpecializations', 'services', 'doctors'));
    }

    public function update(Request $request, $id)
    {
        $privateVisit = PrivateVisit::findOrFail($id);

        $validated = $request->validate([
            'hospital' => 'required|string|max:255',
            'health_department' => 'required|string|max:255',
            'patient_name' => 'required|string|max:255',
            'patient_nationality' => 'nullable|string|max:255',
            'is_insured' => 'nullable|boolean',
            'annual_number' => 'required|integer',
            'fiscal_year' => 'required|integer',
            'statistical_number' => 'nullable|string|max:255',
            'visit_date' => 'required|date',
            'visit_time' => 'nullable|date_format:H:i',
            'deposits' => 'nullable|array',
            'deposits.*.amount' => 'required|numeric|min:0',
            'deposits.*.number' => 'required|string|max:255',
            'deposits.*.date' => 'required|date',
            'deposits.*.payment_type' => 'required|in:كاش,إلكتروني',
            'deposits.*.interest_rate' => 'required|numeric|min:0|max:100',
            'deposits.*.interest_amount' => 'required|numeric|min:0',
            'services' => 'required|array',
            'services.*.id' => 'required|exists:service_specialization,id',
            'services.*.fee' => 'required|numeric',
            'services.*.is_insured' => 'required|boolean',
            'services.*.insurance_percentage' => 'required|numeric|min:0|max:100',
            'services.*.doctor_id' => 'nullable|exists:doctors,id',
            'services.*.operation_degree' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $privateVisit->update([
                'hospital' => $validated['hospital'],
                'health_department' => $validated['health_department'],
                'patient_name' => $validated['patient_name'],
                'patient_nationality' => $validated['patient_nationality'] ?? null,
                'annual_number' => $validated['annual_number'],
                'fiscal_year' => $validated['fiscal_year'],
                'statistical_number' => $validated['statistical_number'] ?? null,
                'visit_date' => $validated['visit_date'],
                'visit_time' => $validated['visit_time'] ?? null,
                'is_insured' => $validated['is_insured'] ?? false
            ]);

            $privateVisit->privateVisitServices()->delete();

            $totalAmount = 0;
            foreach ($validated['services'] as $service) {
                $serviceAmount = $service['fee'];
                $patientAmount = 0;
                $insuranceAmount = 0;
                $insurancePercentage = $service['insurance_percentage'] ?? 100;
                
                if ($service['is_insured']) {
                    $patientAmount = $serviceAmount * ($insurancePercentage / 100);
                    $insuranceAmount = $serviceAmount * ((100 - $insurancePercentage) / 100);
                } else {
                    $patientAmount = $serviceAmount;
                    $insuranceAmount = 0;
                }

                $doctorId = !empty($service['doctor_id']) && $service['doctor_id'] !== 'null' ? $service['doctor_id'] : null;

                DB::table('private_visit_services')->insert([
                    'private_visit_id' => $privateVisit->id,
                    'service_id' => $service['id'],
                    'service_fee' => $service['fee'],
                    'is_insured' => $service['is_insured'],
                    'insurance_percentage' => $insurancePercentage,
                    'patient_amount' => $patientAmount,
                    'insurance_amount' => $insuranceAmount,
                    'doctor_id' => $doctorId,
                    'total_amount' => $serviceAmount,
                    'operation_degree' => $service['operation_degree'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $totalAmount += $patientAmount;
            }

            if (isset($validated['deposits']) && is_array($validated['deposits'])) {
                foreach ($validated['deposits'] as $deposit) {
                    $interestRate = $deposit['payment_type'] === 'كاش' ? 5.00 : 0.00;
                    $interestAmount = ($deposit['amount'] * $interestRate) / 100;

                    PrivateVisitReceipt::create([
                        'private_visit_id' => $privateVisit->id,
                        'receipt_number' => $deposit['number'],
                        'amount' => $deposit['amount'],
                        'receipt_date' => $deposit['date'],
                        'payment_type' => $deposit['payment_type'],
                        'interest_rate' => $interestRate,
                        'interest_amount' => $interestAmount,
                    ]);
                }
            }

            $totalDeposits = $privateVisit->receipts->sum(function($receipt) {
                return $receipt->amount + $receipt->interest_amount;
            });

            $privateVisit->update([
                'total_amount' => $totalAmount,
                'deposit_amount' => $totalDeposits
            ]);

            DB::commit();

            return redirect()->route('private-visits.index')
                ->with('success', 'تم تحديث سجل الزيارة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating private visit: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
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
