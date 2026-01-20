<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\TypeSpecialization;
use App\Models\Service_Specialization;
use App\Models\PrivateWing;
use App\Models\PrivateWingService;
use App\Models\PrivateWingReceipt;
use App\Models\PrivateWingRefund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class PrivateWingController extends Controller
{    function __construct()
    {
    $this->middleware('permission:اجنحة', ['only' => ['index','store','create','edit','update','destroy','invoice','getSpecializations','getInstitutions','getServicePrice','show','getSpecializations',
    'getInstitutions','getServicePrice','getSpecializations','getInstitutions','getServicePrice']]);
  
    
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
                'pws.operation_degree'
            )
            ->orderBy('ss.namesv', 'asc')
            ->get();

        // حساب المجموع الكلي للخدمات
        $totalAmount = $services->sum('total_amount');

        return view('private-wings.invoice', compact('privateWing', 'services', 'totalAmount'));
    }

    public function index(Request $request)
    {
        $query = PrivateWing::query();

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

        return view('private-wings.index', compact('privateWings', 'debtWingsCount'));
    }    public function create()
    {
    $typeSpecializations = TypeSpecialization::all();
    $doctors = \App\Models\Doctor::all();
    
    // الحصول على المؤسسة الحالية من المستخدم
    $hospital = Auth::user()->fck ? Auth::user()->fck->Fckname : '';
    
    // الحصول على السنة الحالية
    $currentYear = date('Y');
    
    // جلب آخر ترقيم سنوي حسب المؤسسة والسنة (على مستوى المؤسسة)
    $lastAnnual = PrivateWing::where('hospital', $hospital)
        ->where('fiscal_year', $currentYear)
        ->orderByDesc('annual_number')
        ->first();
    $nextAnnualNumber = $lastAnnual ? ((int)$lastAnnual->annual_number + 1) : 1;
    
    // جلب آخر ترقيم تسلسلي حسب المؤسسة (على مستوى المؤسسة)
    $lastSequential = PrivateWing::where('hospital', $hospital)
        ->orderByDesc('sequential_number')
        ->first();
    $nextSequentialNumber = $lastSequential ? ((int)$lastSequential->sequential_number + 1) : 1;
    
    return view('private-wings.create', compact('typeSpecializations', 'nextAnnualNumber', 'nextSequentialNumber', 'currentYear', 'doctors'));
    }

    public function getNextNumbers()
    {
        // الحصول على المؤسسة الحالية من المستخدم
        $hospital = Auth::user()->fck ? Auth::user()->fck->Fckname : '';
        
        // الحصول على السنة الحالية
        $currentYear = date('Y');
        
        // جلب آخر ترقيم سنوي حسب المؤسسة والسنة
        $lastAnnual = PrivateWing::where('hospital', $hospital)
            ->where('fiscal_year', $currentYear)
            ->orderByDesc('annual_number')
            ->first();
        $nextAnnualNumber = $lastAnnual ? ((int)$lastAnnual->annual_number + 1) : 1;
        
        // جلب آخر ترقيم تسلسلي حسب المؤسسة
        $lastSequential = PrivateWing::where('hospital', $hospital)
            ->orderByDesc('sequential_number')
            ->first();
        $nextSequentialNumber = $lastSequential ? ((int)$lastSequential->sequential_number + 1) : 1;
        
        return response()->json([
            'nextAnnualNumber' => $nextAnnualNumber,
            'nextSequentialNumber' => $nextSequentialNumber,
            'currentYear' => $currentYear
        ]);
    }

    public function store(Request $request)
    {
        // للتأكد من البيانات المرسلة
        Log::info('Request data:', $request->all());

        // التحقق من عدم تكرار الترقيم السنوي على مستوى المؤسسة
        $existingAnnual = PrivateWing::where('hospital', $request->hospital)
            ->where('annual_number', $request->annual_number)
            ->where('fiscal_year', $request->fiscal_year)
            ->first();
            
        if ($existingAnnual) {
            return redirect()->back()
                ->withErrors(['annual_number' => 'الترقيم السنوي ' . $request->annual_number . ' مستخدم بالفعل لهذه المؤسسة في السنة ' . $request->fiscal_year])
                ->withInput();
        }

        $validated = $request->validate([
            'hospital' => 'required|string|max:255',
            'health_department' => 'required|string|max:255',
            'patient_name' => 'required|string|max:255',
            'patient_nationality' => 'nullable|string|max:255',
            'sequential_number' => 'required|integer',
            'annual_number' => 'required|integer',
            'fiscal_year' => 'required|integer',
            'statistical_number' => 'required|string|max:255',
            'table_number' => 'nullable|string|max:255',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date',
            'days_count' => 'required|integer',
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
            'services.*.is_daily' => 'required|boolean',
            'services.*.is_insured' => 'required|boolean',
            'services.*.insurance_percentage' => 'required|numeric|min:0|max:100',
            'services.*.doctor_id' => 'nullable|exists:doctors,id',
            'services.*.operation_degree' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // إنشاء سجل الجناح الخاص
            $privateWing = PrivateWing::create([
                'hospital' => $validated['hospital'],
                'health_department' => $validated['health_department'],
                'patient_name' => $validated['patient_name'],
                'patient_nationality' => $validated['patient_nationality'] ?? null,
                'annual_number' => $validated['annual_number'],
                'fiscal_year' => $validated['fiscal_year'],
                'statistical_number' => $validated['statistical_number'],
                'table_number' => $validated['table_number'] ?? null,
                'entry_date' => $validated['entry_date'],
                'exit_date' => $validated['exit_date'],
                'days_count' => $validated['days_count'],
                'is_insured' => (int)($request->input('is_insured', 0))
            ]);

            // إضافة وصولات التأمين
            $totalDeposits = 0;
            foreach ($validated['deposits'] as $deposit) {
                $privateWing->receipts()->create([
                    'amount' => $deposit['amount'],
                    'receipt_number' => $deposit['number'],
                    'receipt_date' => $deposit['date'],
                    'payment_type' => $deposit['payment_type'],
                    'interest_rate' => $deposit['interest_rate'],
                    'interest_amount' => $deposit['interest_amount']
                ]);
                $totalDeposits += $deposit['amount'] + $deposit['interest_amount'];
            }
            
            // تحديث إجمالي التأمينات في الجناح
            $privateWing->update([
                'deposit_amount' => $totalDeposits
            ]);
            $totalAmount = 0;
            // إضافة الخدمات وتسجيل المعلومات للتأكد من البيانات
            Log::info('Adding services:', $validated['services']);
            
            foreach ($validated['services'] as $service) {
                Log::info('Processing service:', $service);
                Log::info('doctor_id value:', ['value' => $service['doctor_id']]);
                Log::info('doctor_id type:', ['type' => gettype($service['doctor_id'])]);
                
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
                    // تسجيل البيانات للتأكد من صحتها
                    Log::info('Creating service with data:', [
                        'private_wing_id' => $privateWing->id,
                        'service_id' => $service['id'],
                        'service_fee' => $service['fee'],
                        'is_daily' => $service['is_daily'],
                        'is_insured' => $service['is_insured'],
                        'total_amount' => $serviceAmount,
                        'patient_amount' => $patientAmount,
                        'insurance_amount' => $insuranceAmount
                    ]);                    
                    $doctorId = !empty($service['doctor_id']) && $service['doctor_id'] !== 'null' ? $service['doctor_id'] : null;
                    Log::info('doctor_id after processing:', ['processed' => $doctorId]);
                    
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
                    
                    Log::info('Service saved successfully:', ['result' => $result]);
                    
                    // إضافة مبلغ المريض فقط إلى المجموع الكلي
                    $totalAmount += $patientAmount;
                } catch (\Exception $e) {
                    Log::error('Error creating service: ' . $e->getMessage());
                    throw $e;
                }
            }

            // تحديث المبلغ الإجمالي (مبلغ المريض فقط)
            $privateWing->update(['total_amount' => $totalAmount]);

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
                'pws.operation_degree'
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
                'pws.operation_degree'
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
        
        // جلب الخدمات باستخدام العلاقة
        $services = $privateWing->privateWingServices()
            ->join('service_specialization as ss', 'private_wing_services.service_id', '=', 'ss.id')
            ->leftJoin('doctors as d', 'private_wing_services.doctor_id', '=', 'd.id')
            ->select(
                'private_wing_services.id',
                'ss.namesv as service_name',
                'private_wing_services.doctor_id',
                'd.name as doctor_name',
                'private_wing_services.service_fee',
                'private_wing_services.is_daily',
                'private_wing_services.total_amount',
                'private_wing_services.operation_degree'
            )
            ->orderBy('ss.namesv', 'asc')
            ->get();
        
        return view('private-wings.edit', compact('privateWing','typeSpecializations', 'services'));
    }    public function update(Request $request, PrivateWing $privateWing)
    {
        $validated = $request->validate([
            'hospital' => 'required|string|max:255',
            'health_department' => 'required|string|max:255',
            'patient_name' => 'required|string|max:255',
            'patient_nationality' => 'nullable|string|max:255',
            'annual_number' => 'required|integer',
            'fiscal_year' => 'required|integer',
            'statistical_number' => 'required|string|max:255',
            'table_number' => 'nullable|string|max:255',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date',
            'days_count' => 'required|integer',
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
            'services.*.is_daily' => 'required|boolean',
            'services.*.is_insured' => 'required|boolean',
            'services.*.doctor_id' => 'nullable|exists:doctors,id',
            'services.*.operation_degree' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // تحديث بيانات الجناح الخاص
            $privateWing->update([
                'hospital' => $validated['hospital'],
                'health_department' => $validated['health_department'],
                'patient_name' => $validated['patient_name'],
                'patient_nationality' => $validated['patient_nationality'] ?? null,
                'annual_number' => $validated['annual_number'],
                'fiscal_year' => $validated['fiscal_year'],
                'statistical_number' => $validated['statistical_number'],
                'table_number' => $validated['table_number'] ?? null,
                'entry_date' => $validated['entry_date'],
                'exit_date' => $validated['exit_date'],
                'days_count' => $validated['days_count'],
                'is_insured' => (int)($request->input('is_insured', 0))
            ]);

            // حساب عدد الأيام إذا لم يتم تحديثها
            if ($validated['exit_date'] && $validated['entry_date']) {
                $daysCount = \Carbon\Carbon::parse($validated['exit_date'])->diffInDays(\Carbon\Carbon::parse($validated['entry_date'])) + 1;
                $privateWing->update(['days_count' => $daysCount]);
            }

            // لا نحذف الوصولات المحذوفة - نحافظ على الوصولات الموجودة
            // if ($request->filled('deleted_receipts')) {
            //     $deletedReceiptIds = explode(',', $request->deleted_receipts);
            //     \App\Models\PrivateWingReceipt::whereIn('id', $deletedReceiptIds)->delete();
            // }

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

            // إضافة وصولات جديدة فقط
            if (isset($validated['deposits']) && is_array($validated['deposits'])) {
                foreach ($validated['deposits'] as $deposit) {
                    // جميع الوصولات المرسلة هي جديدة (لا يتم إرسال الوصولات القديمة)
                    $interestRate = $deposit['payment_type'] === 'كاش' ? 5.00 : 0.00;
                    $interestAmount = ($deposit['amount'] * $interestRate) / 100;

                    \App\Models\PrivateWingReceipt::create([
                        'private_wing_id' => $privateWing->id,
                        'receipt_number' => $deposit['number'],
                        'amount' => $deposit['amount'],
                        'receipt_date' => $deposit['date'],
                        'payment_type' => $deposit['payment_type'],
                        'interest_rate' => $interestRate,
                        'interest_amount' => $interestAmount,
                    ]);
                }
            }

            // تحديث المجاميع
            $totalDeposits = $privateWing->receipts->sum(function($receipt) {
                return $receipt->amount + $receipt->interest_amount;
            });

            $privateWing->update([
                'total_amount' => $totalAmount,
                'deposit_amount' => $totalDeposits
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
            // التحقق من وجود section_id و fckt
            $section_id = $request->input('section_id');
          

            if (!$section_id) {
                return response()->json([], 400); // Bad Request إذا كانت البيانات غير مكتملة
            }

            // جلب المؤسسات بناءً على section_id و
            $institutions = Service::where('type_specializations_id', $section_id)
                ->get();

            return response()->json($institutions);
        } catch (\Exception $e) {
            // تسجيل الخطأ في السجلات
            Log::error('Error fetching institutions: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
       public function getInstitutions(Request $request)
    {
        try {
            // التحقق من وجود section_id و fckt
            $section_id = $request->input('section_id');
            $fckn = $request->input('fckn');

            if (!$section_id || !$fckn) {
                return response()->json([], 400); // Bad Request إذا كانت البيانات غير مكتملة
            }

            // جلب المؤسسات بناءً على section_id و fckt
            $institution = service_specialization::where('type_specializations_id', $section_id)
                ->where('service_id', $fckn)
                ->get();

            return response()->json($institution);
        } catch (\Exception $e) {
            // تسجيل الخطأ في السجلات
            Log::error('Error fetching institutions: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
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

        // حساب المبلغ المتبقي للاسترداد
        $remaining = $privateWing->deposit_amount - $privateWing->total_amount;
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
            $remaining = $privateWing->deposit_amount - $privateWing->total_amount;
            
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
}
