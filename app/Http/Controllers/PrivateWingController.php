<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\TypeSpecialization;
use App\Models\Service_Specialization;
use App\Models\PrivateWing;

use App\Models\PrivateWingService;
use App\Models\PrivateWingReceipt;
use App\Models\PrivateWingRefund;
use App\Helpers\NumberToArabicWords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class PrivateWingController extends Controller
{    
    function __construct()
    {
        // استخدام الصلاحية الرئيسية للأجنحة الخاصة - منفصلة تماماً
        $this->middleware('permission:الأجنحة الخاصة', ['only' => [
            'index', 'create', 'store', 'edit', 'update', 'destroy', 'show',
            'invoice', 'getSpecializations', 'getInstitutions', 'getServicePrice',
            'trashed', 'export', 'qrView', 'getNextNumbers', 'addRefund', 'deleteRefund'
        ]]);
        
        // صلاحيات فرعية
        $this->middleware('permission:الأجنحة الخاصة.اضافة', ['only' => ['create', 'store']]);
        $this->middleware('permission:الأجنحة الخاصة.تعديل', ['only' => ['edit', 'update']]);
        $this->middleware('permission:الأجنحة الخاصة.حذف', ['only' => ['destroy']]);
        $this->middleware('permission:الأجنحة الخاصة.فاتورة', ['only' => ['invoice', 'show']]);
        $this->middleware('permission:الأجنحة الخاصة.تصدير', ['only' => ['export']]);
        $this->middleware('permission:الأجنحة الخاصة.ارشيف', ['only' => ['trashed']]);
        $this->middleware('permission:الأجنحة الخاصة.استرداد', ['only' => ['addRefund', 'deleteRefund']]);
    }

    private function isDoctorRequiredService($serviceName)
    {
        $serviceName = trim((string) ($serviceName ?? ''));

        if ($serviceName === '') {
            return false;
        }

        // قائمة الخدمات التي يجب اختيار طبيب لها
        $doctorServices = [
            'اجور عمليات',
            'اجور زيارات علاجية',
            'اجورالخدمات الطبية المقدمة للعرب والأجانب غير المقيمين',
            'العيون',
            'تمريض وتداوي'
        ];

        if (in_array($serviceName, $doctorServices, true)) {
            return true;
        }

        // النصوص التي تحتوي على طبيب/مساعد جراح
        if (preg_match('/اجور.*(الطبيب|المساعد|الجراح|طبيبة|الطبيبة)/iu', $serviceName)) {
            return true;
        }

        return false;
    }

    public function invoice($wing)
    {
        $privateWing = PrivateWing::withTrashed()->with(['receipts'])->findOrFail($wing);

        // جلب الخدمات مباشرة من قاعدة البيانات باستخدام الاستعلام
        $services = DB::table('private_wing_services as pws')
            ->join('service_specialization as ss', 'pws.service_id', '=', 'ss.id')
            ->leftJoin('doctors as d', 'pws.doctor_id', '=', 'd.id')
            ->where('pws.private_wing_id', $privateWing->id)
            ->select(
                'ss.namesv as service_name',
                'd.name as doctor_name',
                'pws.service_fee',
                'pws.is_daily',
                'pws.total_amount',
                'pws.operation_degree',
                'pws.is_insured',
                'pws.insurance_percentage',
                'pws.patient_amount',
                'pws.insurance_amount'
            )
            ->orderBy('ss.namesv', 'asc')
            ->get();

        // حساب المجموع الكلي للخدمات
        $totalAmount = $services->sum('total_amount');

        return view('private-wings.invoice', compact('privateWing', 'services', 'totalAmount'));
    }

    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $query = PrivateWing::query();

        // تطبيق قيود الصلاحيات حسب نوع المستخدم
        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            // Admin/mohst: عرض كل الأجنحة
            // لا حاجة لفلترة
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            // stat-doh: عرض أجنحة الدائرة فقط
            $query->where('moh_id', $currentUser->mohcode);
        } else {
            // مستخدمو المؤسسات: عرض أجنحة المؤسسة فقط
            $query->where('fck_id', $currentUser->fckid);
        }

        // إذا لم يتم تحديد حالة، أظهر فقط النشطة
        if (!$request->filled('status')) {
            $query->whereNull('cancelled_at');
        }

        // فلترة حسب الاسم
        if ($request->filled('patient_name')) {
            $query->where('patient_name', 'like', '%' . $request->patient_name . '%');
        }

        // فلترة حسب المستشفى
        if ($request->filled('hospital')) {
            $query->where('hospital', 'like', '%' . $request->hospital . '%');
        }

        // فلترة حسب الترقيم التسلسلي (باركود)
        if ($request->filled('sequential_number')) {
            $query->where('sequential_number', $request->sequential_number);
        }

        // فلترة حسب تاريخ تنظيم الاستمارة
        if ($request->filled('created_at_from')) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }
        if ($request->filled('created_at_to')) {
            $query->whereDate('created_at', '<=', $request->created_at_to);
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

        $privateWings = $query->orderBy('entry_date', 'desc')->paginate(10)->appends($request->query());
        $debtWingsCount = PrivateWing::whereNull('cancelled_at')->where('deposit_amount', '<', DB::raw('total_amount'))->count();

        // المؤسسات والدوائر المتاحة لمودال البحث المتقدم
        $userMohId = $currentUser->mohcode;
        $userFckId = $currentUser->fckid;
        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            $availableFcks = \App\Models\Fck::all();
            $availableMohs = \App\Models\Mohs::all();
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            $availableFcks = \App\Models\Fck::where('moh_id', $userMohId)->get();
            $availableMohs = \App\Models\Mohs::where('id', $userMohId)->get();
        } else {
            $availableFcks = \App\Models\Fck::where('id', $userFckId)->get();
            $availableMohs = \App\Models\Mohs::where('id', $userMohId)->get();
        }

        return view('private-wings.index', compact('privateWings', 'debtWingsCount', 'availableMohs', 'availableFcks'));
    }

    public function create()
    {
    $currentUser = Auth::user();
    $typeSpecializations = TypeSpecialization::all();

    // جلب الأطباء من نفس مؤسسة المستخدم فقط
    $doctors = \App\Models\Doctor::where('fck_id', $currentUser->fckid)->get();
    
    // الحصول على بيانات المستخدم الحالي
    $userMohId = $currentUser->mohcode;
    $userFckId = $currentUser->fckid;
    $hospital = $currentUser->fck ? $currentUser->fck->Fckname : '';
    $healthDepartment = $currentUser->moh ? ($currentUser->moh->mohname ?? $currentUser->moh->Mohname) : '';
    
    // جلب المؤسسات المتاحة حسب الصلاحيات
    if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
        // Admin/mohst: كل المؤسسات
        $availableFcks = \App\Models\Fck::all();
        $availableMohs = \App\Models\Mohs::all();
    } elseif ($currentUser->roles_name === ["stat-doh"]) {
        // stat-doh: مؤسسات الدائرة فقط
        $availableFcks = \App\Models\Fck::where('moh_id', $userMohId)->get();
        $availableMohs = \App\Models\Mohs::where('id', $userMohId)->get();
    } else {
        // مستخدمو المؤسسات: مؤسستهم فقط
        $availableFcks = \App\Models\Fck::where('id', $userFckId)->get();
        $availableMohs = \App\Models\Mohs::where('id', $userMohId)->get();
    }
    
    // الحصول على السنة الحالية
    $currentYear = date('Y');
    
    // جلب آخر ترقيم سنوي حسب المؤسسة والسنة
    $lastAnnual = PrivateWing::where('fck_id', $userFckId)
        ->where('fiscal_year', $currentYear)
        ->orderByRaw('CAST(annual_number AS UNSIGNED) DESC')
        ->first();
    $nextAnnualNumber = $lastAnnual ? ((int)$lastAnnual->annual_number + 1) : 1;
    
    // جلب آخر ترقيم تسلسلي حسب المؤسسة
    $lastSequential = PrivateWing::where('fck_id', $userFckId)
        ->orderByRaw('CAST(sequential_number AS UNSIGNED) DESC')
        ->first();
    $nextSequentialNumber = $lastSequential ? ((int)$lastSequential->sequential_number + 1) : 1;
    
    return view('private-wings.create', compact(
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
        
        // الحصول على المؤسسة الحالية من المستخدم
        $fckId = $currentUser->fckid;

        // الحصول على السنة الحالية
        $currentYear = date('Y');

        // استخدام قفل لضمان عدم التكرار
        DB::beginTransaction();
        try {
            // جلب آخر ترقيم سنوي حسب المؤسسة والسنة
            $lastAnnual = PrivateWing::where('fck_id', $fckId)
                ->where('fiscal_year', $currentYear)
                ->orderByRaw('CAST(annual_number AS UNSIGNED) DESC')
                ->lockForUpdate() // قفل الصفوف لمنع التكرار
                ->first();
            $nextAnnualNumber = $lastAnnual ? ((int)$lastAnnual->annual_number + 1) : 1;

            // جلب آخر ترقيم تسلسلي حسب المؤسسة
            $lastSequential = PrivateWing::where('fck_id', $fckId)
                ->orderByRaw('CAST(sequential_number AS UNSIGNED) DESC')
                ->lockForUpdate() // قفل الصفوف لمنع التكرار
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
        // للتأكد من البيانات المرسلة
        Log::info('Request data:', $request->all());

        $exceptionPercentage = floatval($request->input('exception_percentage', 0));

        $rules = [
            'hospital' => 'required|string|max:255',
            'health_department' => 'required|string|max:255',
            'moh_id' => 'required|exists:mohs,id',
            'fck_id' => 'required|exists:fcks,id',
            'patient_name' => 'required|string|max:255',
            'patient_nationality' => 'nullable|string|max:255',
            'is_insured' => 'nullable|boolean',
            'exception_from' => 'nullable|string|max:255',
            'exception_percentage' => 'nullable|numeric|min:0|max:100',
            'exception_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'statistical_number' => 'required|string|max:255',
            'entry_date' => 'required|date',
            'entry_time' => 'nullable|date_format:H:i',
            'exit_date' => 'nullable|date',
            'exit_time' => 'nullable|date_format:H:i',
            'days_count' => 'required|integer',
            'interest_amount' => 'nullable|numeric|min:0',
            'deposits' => $exceptionPercentage >= 100 ? 'nullable|array' : 'nullable|array',
            'deposits.*.amount' => 'required|numeric|min:0',
            'deposits.*.number' => 'required|string|max:255',
            'deposits.*.date' => 'required|date',
            'deposits.*.payment_type' => 'required|in:كاش,إلكتروني',
            'deposits.*.interest_rate' => 'nullable|numeric|min:0|max:100',
            'deposits.*.interest_amount' => 'nullable|numeric|min:0',
            'services' => 'required|array',
            'services.*.id' => 'required|exists:service_specialization,id',
            'services.*.fee' => 'required|numeric',
            'services.*.is_daily' => 'required|boolean',
            'services.*.is_insured' => 'required|boolean',
            'services.*.insurance_percentage' => 'required|numeric|min:0|max:100',
            'services.*.doctor_id' => 'nullable|exists:doctors,id',
            'services.*.operation_degree' => 'nullable|string|max:255'
        ];

        $validated = $request->validate($rules);
        
        // التحقق من صلاحيات المستخدم
        $currentUser = Auth::user();
        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                // stat-doh: يجب أن تكون الدائرة ضمن صلاحياته
                if ($validated['moh_id'] != $currentUser->mohcode) {
                    return redirect()->back()->withErrors(['moh_id' => 'غير مسموح لك بإضافة بيانات لدوائر أخرى'])->withInput();
                }
            } else {
                // مستخدمو المؤسسات: يجب أن تكون المؤسسة ضمن صلاحياتهم
                if ($validated['fck_id'] != $currentUser->fckid) {
                    return redirect()->back()->withErrors(['fck_id' => 'غير مسموح لك بإضافة بيانات لمؤسسات أخرى'])->withInput();
                }
            }
        }

        try {
            DB::beginTransaction();

            // معالجة رفع كتاب الاستثناء
            $attachmentPath = null;
            if ($request->hasFile('exception_attachment')) {
                $file = $request->file('exception_attachment');
                $fileName = 'exception_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('attachments/exceptions');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $fileName);
                $attachmentPath = 'attachments/exceptions/' . $fileName;
            }

            // توليد الترقيم السنوي والتسلسلي تلقائياً داخل الـ transaction مع قفل
            $fckId = $validated['fck_id'];
            $currentYear = date('Y');

            // جلب آخر ترقيم سنوي مع قفل لمنع التكرار
            $lastAnnual = PrivateWing::where('fck_id', $fckId)
                ->where('fiscal_year', $currentYear)
                ->orderByRaw('CAST(annual_number AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();
            $nextAnnualNumber = $lastAnnual ? ((int)$lastAnnual->annual_number + 1) : 1;

            // جلب آخر ترقيم تسلسلي مع قفل لمنع التكرار
            $lastSequential = PrivateWing::where('fck_id', $fckId)
                ->orderByRaw('CAST(sequential_number AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();

            $nextSequentialNumber = $lastSequential ? ((int)$lastSequential->sequential_number + 1) : 1;

            $interestAmount = floatval($validated['interest_amount'] ?? 0);

            // إنشاء سجل الجناح الخاص
            $privateWing = PrivateWing::create([
                'hospital' => $validated['hospital'],
                'health_department' => $validated['health_department'],
                'moh_id' => $validated['moh_id'],
                'fck_id' => $validated['fck_id'],
                'patient_name' => $validated['patient_name'],
                'patient_nationality' => $validated['patient_nationality'] ?? null,
                'annual_number' => $nextAnnualNumber,
                'fiscal_year' => $currentYear,
                'sequential_number' => $nextSequentialNumber,
                'statistical_number' => $validated['statistical_number'],
                'entry_date' => $validated['entry_date'],
                'entry_time' => $validated['entry_time'] ?? null,
                'exit_date' => $validated['exit_date'],
                'exit_time' => $validated['exit_time'] ?? null,
                'days_count' => $this->calculateHotelDays(
                                    $validated['entry_date'],
                                    $validated['entry_time'] ?? null,
                                    $validated['exit_date'] ?? null,
                                    $validated['exit_time'] ?? null
                                 ),
                'is_insured' => $validated['is_insured'] ?? false,
                'exception_from' => $validated['exception_from'] ?? null,
                'exception_percentage' => $exceptionPercentage,
                'exception_attachment' => $attachmentPath,
                'interest_amount' => $interestAmount,
                'interest_rate' => 0
            ]);

            // إضافة وصولات التأمين إن وجدت
            $totalDeposits = 0;
            if (!empty($validated['deposits'])) {
                foreach ($validated['deposits'] as $deposit) {
                    $depositAmount = floatval($deposit['amount']);
                    $privateWing->receipts()->create([
                        'amount' => $depositAmount,
                        'receipt_number' => $deposit['number'],
                        'receipt_date' => $deposit['date'],
                        'payment_type' => $deposit['payment_type'],
                        'interest_rate' => 0,
                        'interest_amount' => 0
                    ]);
                    $totalDeposits += $depositAmount;
                }
            }
            
            $totalAmount = 0;
            // إضافة الخدمات وتسجيل المعلومات للتأكد من البيانات
            Log::info('Adding services:', $validated['services']);
            
            foreach ($validated['services'] as $service) {
                $serviceAmount = $service['is_daily'] ? 
                    $service['fee'] * $validated['days_count'] : 
                    $service['fee'];
                
                // حساب مبالغ الضمان بناءً على النسبة المحددة
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
                  
                try {
                    $serviceSpecialization = Service_Specialization::find($service['id']);
                    $serviceName = $serviceSpecialization ? ($serviceSpecialization->namesv ?? '') : '';

                    // معالجة doctor_id - السماح بقيمة null للطبيب لجميع الخدمات
                    $doctorId = !empty($service['doctor_id']) && $service['doctor_id'] !== 'null' ? $service['doctor_id'] : null;

                    // إنشاء سجل الخدمة باستخدام DB facade
                    $result = DB::table('private_wing_services')->insert([
                        'private_wing_id' => $privateWing->id,
                        'service_id' => $service['id'],
                        'service_fee' => $service['fee'],
                        'is_daily' => $service['is_daily'],
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
                    
                    // إضافة مبلغ المريض فقط إلى المجموع الكلي
                    $totalAmount += $patientAmount;
                } catch (\Exception $e) {
                    Log::error('Error creating service: ' . $e->getMessage());
                    throw $e;
                }
            }

            // تطبيق نسبة الاستثناء على إجمالي خدمات المريض
            if ($exceptionPercentage > 0) {
                $totalAmount = $totalAmount * (1 - $exceptionPercentage / 100);
            }

            // تحديث المبلغ الإجمالي المطلوب (مبلغ المريض بعد الاستثناء + مبلغ الفائدة اليدوي) وإجمالي التأمينات
            $grandTotal = $totalAmount + $interestAmount;
            $privateWing->update([
                'total_amount' => $grandTotal,
                'deposit_amount' => $totalDeposits,
                'interest_amount' => $interestAmount
            ]);

            DB::commit();

            return redirect()->route('private-wings.index')
                ->with('success', 'تم إنشاء سجل الجناح الخاص بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving private wing: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function qrView($id)
    {
        // تحميل الفاتورة مع العلاقات اللازمة
        $privateWing = PrivateWing::withTrashed()
            ->with('receipts')
            ->findOrFail($id);
        
        // تحميل cancelledByUser إذا كانت الفاتورة ملغاة
        if ($privateWing->cancelled_by) {
            $privateWing->load('cancelledByUser');
        }
        
        $services = DB::table('private_wing_services as pws')
            ->join('service_specialization as ss', 'pws.service_id', '=', 'ss.id')
            ->leftJoin('doctors as d', 'pws.doctor_id', '=', 'd.id')
            ->where('pws.private_wing_id', $privateWing->id)
            ->select(
                'ss.namesv as service_name',
                'd.name as doctor_name',
                'pws.service_fee',
                'pws.is_daily',
                'pws.total_amount',
                'pws.operation_degree',
                'pws.is_insured',
                'pws.insurance_percentage',
                'pws.patient_amount',
                'pws.insurance_amount'
            )
            ->orderBy('ss.namesv', 'asc')
            ->get();

        $totalAmount = $services->sum('total_amount');

        // عرض الصفحة العادية
        return view('private-wings.show', compact('privateWing', 'services', 'totalAmount'));
    }

    public function show($id)
    {
        $privateWing = PrivateWing::withTrashed()->with(['receipts', 'refunds'])->findOrFail($id);
        
        $services = DB::table('private_wing_services as pws')
            ->join('service_specialization as ss', 'pws.service_id', '=', 'ss.id')
            ->leftJoin('doctors as d', 'pws.doctor_id', '=', 'd.id')
            ->where('pws.private_wing_id', $privateWing->id)
            ->select(
                'ss.namesv as service_name',
                'd.name as doctor_name',
                'pws.service_fee',
                'pws.is_daily',
                'pws.total_amount',
                'pws.operation_degree',
                'pws.is_insured',
                'pws.insurance_percentage',
                'pws.patient_amount',
                'pws.insurance_amount'
            )
            ->orderBy('ss.namesv', 'asc')
            ->get();

        // حساب المجموع الكلي للخدمات
        $totalAmount = $services->sum('total_amount');

        return view('private-wings.show', compact('privateWing', 'services', 'totalAmount'));
    }    public function edit(PrivateWing $privateWing)
    {
        $privateWing->load('services');
        $typeSpecializations = TypeSpecialization::all();

        $currentUser = Auth::user();
        // جلب الأطباء من نفس مؤسسة المستخدم فقط
        $doctors = \App\Models\Doctor::where('fck_id', $currentUser->fckid)->get();
        
        // جلب الخدمات باستخدام العلاقة
         $services = $privateWing->privateWingServices()
            ->join('service_specialization as ss', 'private_wing_services.service_id', '=', 'ss.id')
            ->leftJoin('doctors as d', 'private_wing_services.doctor_id', '=', 'd.id')
            ->select(
                'private_wing_services.id',
                'private_wing_services.service_id',
                'ss.namesv as service_name',
                'private_wing_services.doctor_id',
                'd.name as doctor_name',
                'private_wing_services.service_fee',
                'private_wing_services.is_daily',
                'private_wing_services.is_insured',
                'private_wing_services.insurance_percentage',
                'private_wing_services.patient_amount',
                'private_wing_services.insurance_amount',
                'private_wing_services.total_amount',
                'private_wing_services.operation_degree'
            )
            ->orderBy('ss.namesv', 'asc')
            ->get();
        
        return view('private-wings.edit', compact('privateWing','typeSpecializations', 'services', 'doctors'));
    }    public function update(Request $request, PrivateWing $privateWing)
    {
        $exceptionPercentage = floatval($request->input('exception_percentage', 0));

        $validated = $request->validate([
            'hospital' => 'required|string|max:255',
            'health_department' => 'required|string|max:255',
            'patient_name' => 'required|string|max:255',
            'patient_nationality' => 'nullable|string|max:255',
            'is_insured' => 'nullable|boolean',
            'exception_from' => 'nullable|string|max:255',
            'exception_percentage' => 'nullable|numeric|min:0|max:100',
            'exception_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'annual_number' => 'required|integer',
            'fiscal_year' => 'required|integer',
            'statistical_number' => 'required|string|max:255',
            'entry_date' => 'required|date',
            'entry_time' => 'nullable|date_format:H:i',
            'exit_date' => 'nullable|date',
            'exit_time' => 'nullable|date_format:H:i',
            'days_count' => 'required|integer',
            'interest_amount' => 'nullable|numeric|min:0',
            'deposits' => 'nullable|array',
            'deposits.*.amount' => 'required|numeric|min:0',
            'deposits.*.number' => 'required|string|max:255',
            'deposits.*.date' => 'required|date',
            'deposits.*.payment_type' => 'required|in:كاش,إلكتروني',
            'deposits.*.interest_rate' => 'nullable|numeric|min:0|max:100',
            'deposits.*.interest_amount' => 'nullable|numeric|min:0',
            'services' => 'required|array',
            'services.*.id' => 'required|exists:service_specialization,id',
            'services.*.fee' => 'required|numeric',
            'services.*.is_daily' => 'required|boolean',
            'services.*.is_insured' => 'required|boolean',
            'services.*.insurance_percentage' => 'required|numeric|min:0|max:100',
            'services.*.doctor_id' => 'nullable|exists:doctors,id',
            'services.*.operation_degree' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $attachmentPath = $privateWing->exception_attachment;
            if ($request->hasFile('exception_attachment')) {
                $file = $request->file('exception_attachment');
                $fileName = 'exception_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('attachments/exceptions');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $fileName);
                $attachmentPath = 'attachments/exceptions/' . $fileName;
            }

            $interestAmount = floatval($validated['interest_amount'] ?? $privateWing->interest_amount ?? 0);

            // تحديث بيانات الجناح الخاص
            $privateWing->update([
                'hospital' => $validated['hospital'],
                'health_department' => $validated['health_department'],
                'patient_name' => $validated['patient_name'],
                'patient_nationality' => $validated['patient_nationality'] ?? null,
                'annual_number' => $validated['annual_number'],
                'fiscal_year' => $validated['fiscal_year'],
                'statistical_number' => $validated['statistical_number'],
                'entry_date' => $validated['entry_date'],
                'entry_time' => $validated['entry_time'] ?? null,
                'exit_date' => $validated['exit_date'],
                'exit_time' => $validated['exit_time'] ?? null,
                'days_count' => $this->calculateHotelDays(
                                    $validated['entry_date'],
                                    $validated['entry_time'] ?? null,
                                    $validated['exit_date'] ?? null,
                                    $validated['exit_time'] ?? null
                                 ),
                'is_insured' => $validated['is_insured'] ?? false,
                'exception_from' => $validated['exception_from'] ?? null,
                'exception_percentage' => $exceptionPercentage,
                'exception_attachment' => $attachmentPath,
                'interest_amount' => $interestAmount,
                'interest_rate' => 0
            ]);

            // حساب عدد الأيام إذا لم يتم تحديثها
            if ($validated['exit_date'] && $validated['entry_date']) {
                    $daysCount = $this->calculateHotelDays(
                        $validated['entry_date'],
                        $validated['entry_time'] ?? null,
                        $validated['exit_date'],
                        $validated['exit_time'] ?? null
                    );
                    $privateWing->update(['days_count' => $daysCount]);
                }

            // حذف الخدمات القديمة وإعادة إنشائها
            $privateWing->privateWingServices()->delete();

            $totalAmount = 0;
            foreach ($validated['services'] as $service) {
                $serviceAmount = $service['is_daily'] ?
                    $service['fee'] * ($privateWing->days_count ?: 1) :
                    $service['fee'];

                // حساب مبالغ الضمان بناءً على النسبة
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

                $serviceSpecialization = Service_Specialization::find($service['id']);
                $serviceName = $serviceSpecialization ? ($serviceSpecialization->namesv ?? '') : '';

                // معالجة doctor_id - السماح بقيمة null للطبيب لجميع الخدمات
                $doctorId = !empty($service['doctor_id']) && $service['doctor_id'] !== 'null' ? $service['doctor_id'] : null;

                DB::table('private_wing_services')->insert([
                    'private_wing_id' => $privateWing->id,
                    'service_id' => $service['id'],
                    'service_fee' => $service['fee'],
                    'is_daily' => $service['is_daily'],
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

                // إضافة مبلغ المريض فقط إلى المجموع الكلي
                $totalAmount += $patientAmount;
            }

            // تطبيق نسبة الاستثناء
            if ($exceptionPercentage > 0) {
                $totalAmount = $totalAmount * (1 - $exceptionPercentage / 100);
            }

            // إضافة وصولات جديدة فقط
            if (isset($validated['deposits']) && is_array($validated['deposits'])) {
                foreach ($validated['deposits'] as $deposit) {
                    \App\Models\PrivateWingReceipt::create([
                        'private_wing_id' => $privateWing->id,
                        'receipt_number' => $deposit['number'],
                        'amount' => $deposit['amount'],
                        'receipt_date' => $deposit['date'],
                        'payment_type' => $deposit['payment_type'],
                        'interest_rate' => 0,
                        'interest_amount' => 0,
                    ]);
                }
            }

            // تحديث المجاميع
            $totalDeposits = $privateWing->receipts()->sum('amount');
            $grandTotal = $totalAmount + $interestAmount;

            $privateWing->update([
                'total_amount' => $grandTotal,
                'deposit_amount' => $totalDeposits,
                'interest_amount' => $interestAmount
            ]);

            DB::commit();

            return redirect()->route('private-wings.index')
                ->with('success', 'تم تحديث سجل الجناح الخاص بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating private wing: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(PrivateWing $privateWing)
    {
        $privateWing->delete();

        return redirect()->route('private-wings.index')
            ->with('success', 'تم حذف سجل الجناح الخاص بنجاح');
    } 
    public function getSpecializations(Request $request)
    {
        try {
            // جلب التخصصات الفرعية (الخدمات العامة) بشكل مستقل من القسم إذا تم توفيره
            $section_id = $request->input('section_id');

            if ($section_id) {
                $results = Service::where('type_specializations_id', $section_id)->get();
            } else {
                $results = Service::all();
            }

            return response()->json($results);
        } catch (\Exception $e) {
            // تسجيل الخطأ في السجلات
            Log::error('Error fetching specializations: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    // إرجاع الخدمات (service_specialization) بناءً على الخدمة العامة المحددة
    public function getServiceSpecializations(Request $request)
    {
        try {
            $serviceId = $request->input('service_id');

            if (!$serviceId) {
                return response()->json([], 400);
            }

            $service = Service::find($serviceId);
            $sercode = $service ? $service->sercode : $serviceId;

            $services = \App\Models\Service_Specialization::where('service_id', $sercode)
                ->orWhere('service_id', $serviceId)
                ->get();

            return response()->json($services);
        } catch (\Exception $e) {
            Log::error('Error fetching service specializations: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
       public function getInstitutions(Request $request)
    {
        try {
            // التحقق من وجود moh_id و fckt
            $moh_id = $request->input('moh_id');
            $fckt = $request->input('fckt');

            if (!$moh_id) {
                return response()->json([], 400); // Bad Request إذا كانت البيانات غير مكتملة
            }

            // جلب المؤسسات بناءً على moh_id و fckt (اختياري)
            $query = \App\Models\Fck::where('moh_id', $moh_id);
            
            if ($fckt) {
                $query->where('fctypesid', $fckt);
            }
            
            $institutions = $query->get();

            return response()->json($institutions);
        } catch (\Exception $e) {
            // تسجيل الخطأ في السجلات
            \Log::error('Error fetching institutions: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ في جلب المؤسسات: ' . $e->getMessage()], 500);
        }
    }    public function getServicePrice(Request $request)
    {
        try {
            // لا نحتاج لجلب السعر من قاعدة البيانات - السعر سيكون دائماً صفر للسماح بالإدخال اليدوي
            return response()->json(['price' => 0]);
        } catch (\Exception $e) {
            Log::error('Error in getServicePrice: ' . $e->getMessage());
            return response()->json(['price' => 0]);
        }
    }

    // عرض السجلات المبطلات
    public function trashed()
    {
        $privateWings = PrivateWing::withTrashed()
            ->where('cancelled_at', '!=', null)
            ->leftJoin('users', 'private_wings.cancelled_by', '=', 'users.id')
            ->select('private_wings.*', 'users.name as cancelled_by_name')
            ->orderBy('private_wings.cancelled_at', 'desc')
            ->paginate(10);
        return view('private-wings.trashed', compact('privateWings'));
    }





    // إبطال قائمة
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000'
        ]);

        $privateWing = PrivateWing::findOrFail($id);

        // التحقق من أن القائمة غير ملغاة مسبقاً
        if ($privateWing->cancelled_at) {
            return back()->withErrors(['error' => 'هذه القائمة ملغاة مسبقاً']);
        }

        $privateWing->update([
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id()
        ]);

        return back()->with('success', 'تم إبطال القائمة بنجاح');
    }

    /**
     * إضافة وصل استرداد
     */
    public function addRefund(Request $request, $wing)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'receipt_number' => 'required|string|max:255',
            'refund_date' => 'required|date',
            'receiver_name' => 'required|string|max:255',
            'receiver_id_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $privateWing = PrivateWing::findOrFail($wing);

        // حساب المبلغ المتبقي للاسترداد (بدون فائدة - مطابق للطباعة)
        $totalDepositsWithoutInterest = $privateWing->receipts->sum('amount');
        $remaining = $totalDepositsWithoutInterest - $privateWing->total_amount;
        $alreadyRefunded = $privateWing->refund_amount;
        $availableForRefund = $remaining - $alreadyRefunded;

        // التحقق من أن المبلغ المطلوب استرداده لا يتجاوز المتاح
        if ($validated['amount'] > $availableForRefund) {
            return back()->withErrors(['amount' => 'المبلغ المطلوب استرداده يتجاوز المتاح (' . number_format($availableForRefund, 0) . ' د.ع)']);
        }

        try {
            DB::beginTransaction();

            // إضافة وصل الاسترداد
            PrivateWingRefund::create([
                'private_wing_id' => $privateWing->id,
                'amount' => $validated['amount'],
                'receipt_number' => $validated['receipt_number'],
                'refund_date' => $validated['refund_date'],
                'receiver_name' => $validated['receiver_name'],
                'receiver_id_number' => $validated['receiver_id_number'],
                'notes' => $validated['notes'],
            ]);

            // تحديث المبلغ المسترد وحالة الاسترداد
            $newRefundAmount = $alreadyRefunded + $validated['amount'];
            $refundStatus = ($newRefundAmount >= $remaining) ? 'completed' : 'partial';

            $privateWing->update([
                'refund_amount' => $newRefundAmount,
                'refund_status' => $refundStatus,
            ]);

            DB::commit();
            return redirect()->route('private-wings.refund-receipt', $privateWing->id)
                ->with('success', 'تم إضافة وصل الاسترداد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding refund: ' . $e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة وصل الاسترداد']);
        }
    }

    /**
     * حذف وصل استرداد
     */
    public function deleteRefund($refundId)
    {
        try {
            DB::beginTransaction();

            $refund = PrivateWingRefund::findOrFail($refundId);
            $privateWing = $refund->privateWing;

            // حذف الوصل
            $refundAmount = $refund->amount;
            $refund->delete();

            // تحديث المبلغ المسترد وحالة الاسترداد
            $newRefundAmount = $privateWing->refund_amount - $refundAmount;
            
            // حساب المتبقي بدون فائدة (مطابق للطباعة)
            $totalDepositsWithoutInterest = $privateWing->receipts->sum('amount');
            $remaining = $totalDepositsWithoutInterest - $privateWing->total_amount;
            
            $refundStatus = 'none';
            if ($newRefundAmount > 0 && $newRefundAmount < $remaining) {
                $refundStatus = 'partial';
            } elseif ($newRefundAmount >= $remaining) {
                $refundStatus = 'completed';
            }

            $privateWing->update([
                'refund_amount' => max(0, $newRefundAmount),
                'refund_status' => $refundStatus,
            ]);

            DB::commit();
            return back()->with('success', 'تم حذف وصل الاسترداد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting refund: ' . $e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف وصل الاسترداد']);
        }
    }

    /**
     * عرض وصل الاسترداد للطباعة
     */
    /**
     * حساب عدد الأيام بنظام فنادق (قبل 12 ظهر يعتبر اليوم نفسه، بعد 12 يضاف يوم)
     */
    private function calculateHotelDays($entryDate, $entryTime, $exitDate, $exitTime)
    {
        if (!$entryDate || !$exitDate) {
            return 1;
        }

        $entry = \Carbon\Carbon::parse($entryDate . ' ' . ($entryTime ?: '00:00'));
        $exit = \Carbon\Carbon::parse($exitDate . ' ' . ($exitTime ?: '00:00'));

        // لا نعدل التواريخ، نحسب الفرق فقط
        $entry->startOfDay();
        $exit->startOfDay();

        if ($exit->lt($entry)) {
            return 1;
        }

        $diff = $exit->diffInDays($entry);
        // إذا خرج بعد الظهر أضف يوم واحد
        if ($exit->format('H:i') >= '12:00') {
            $diff += 1;
        }
        return $diff < 1 ? 1 : $diff;
    }

    public function refundReceipt($wing)
    {
        $privateWing = PrivateWing::with(['refunds' => function($query) {
            $query->latest();
        }])->findOrFail($wing);

        if ($privateWing->refunds->isEmpty()) {
            return back()->withErrors(['error' => 'لا توجد وصولات استرداد لهذا السجل']);
        }

        $latestRefund = $privateWing->refunds->first();
        return view('private-wings.refund-receipt', compact('privateWing', 'latestRefund'));
    }


    public function export(Request $request)
    {
        $query = PrivateWing::query();

        // نفس الفلاتر من index
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

        $privateWings = $query->orderBy('created_at', 'desc')->get();

        return \Excel::download(new \App\Exports\PrivateWingsExport($privateWings), 'private-wings-' . date('Y-m-d') . '.xlsx');
    }
}
