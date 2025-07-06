<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\TypeSpecialization;
use App\Models\Service_Specialization;
use App\Models\PrivateWing;
use App\Models\PrivateWingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrivateWingController extends Controller
{   
    public function invoice(PrivateWing $privateWing)
    {
        return view('private-wings.invoice', compact('privateWing'));
    }

    public function index()
    {
        $privateWings = PrivateWing::latest()->paginate(10);
        return view('private-wings.index', compact('privateWings'));
    }    public function create()
    {
        $typeSpecializations = TypeSpecialization::all();
        return view('private-wings.create', compact('typeSpecializations'));
    }    public function store(Request $request)
    {
        // للتأكد من البيانات المرسلة
        \Log::info('Request data:', $request->all());

        $validated = $request->validate([
            'hospital' => 'required|string|max:255',
            'health_department' => 'required|string|max:255',
            'patient_name' => 'required|string|max:255',
            'file_number' => 'required|string|max:255',
            'statistical_number' => 'required|string|max:255',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date',
            'days_count' => 'required|integer',
            'deposit_amount' => 'required|numeric',
            'receipt_number' => 'required|string|max:255',
            'receipt_date' => 'required|date',
            'services' => 'required|array',
            'services.*.id' => 'required|exists:service_specialization,id',
            'services.*.fee' => 'required|numeric',
            'services.*.is_daily' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();

            // إنشاء سجل الجناح الخاص
            $privateWing = PrivateWing::create([
                'hospital' => $validated['hospital'],
                'health_department' => $validated['health_department'],
                'patient_name' => $validated['patient_name'],
                'file_number' => $validated['file_number'],
                'statistical_number' => $validated['statistical_number'],
                'entry_date' => $validated['entry_date'],
                'exit_date' => $validated['exit_date'],
                'days_count' => $validated['days_count'],
                'deposit_amount' => $validated['deposit_amount'],
                'receipt_number' => $validated['receipt_number'],
                'receipt_date' => $validated['receipt_date']
            ]);            $totalAmount = 0;
            // إضافة الخدمات وتسجيل المعلومات للتأكد من البيانات
            \Log::info('Adding services:', $validated['services']);
            
            foreach ($validated['services'] as $service) {
                $serviceAmount = $service['is_daily'] ? 
                    $service['fee'] * $validated['days_count'] : 
                    $service['fee'];
                  try {
                    // تسجيل البيانات للتأكد من صحتها
                    \Log::info('Creating service with data:', [
                        'private_wing_id' => $privateWing->id,
                        'service_id' => $service['id'],
                        'service_fee' => $service['fee'],
                        'is_daily' => $service['is_daily'],
                        'total_amount' => $serviceAmount
                    ]);                    // إنشاء سجل الخدمة باستخدام DB facade
                    $result = DB::table('private_wing_services')->insert([
                        'private_wing_id' => $privateWing->id,
                        'service_id' => $service['id'],
                        'service_fee' => $service['fee'],
                        'is_daily' => $service['is_daily'],
                        'total_amount' => $serviceAmount,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    \Log::info('Service saved successfully:', ['result' => $result]);
                    
                    
                    $totalAmount += $serviceAmount;
                } catch (\Exception $e) {
                    Log::error('Error creating service: ' . $e->getMessage());
                    throw $e;
                }
            }

            // تحديث المبلغ الإجمالي
            $privateWing->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('private-wings.index')
                ->with('success', 'تم إنشاء سجل الجناح الخاص بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving private wing: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }    public function show(PrivateWing $privateWing)
    {        // جلب الخدمات مباشرة من قاعدة البيانات باستخدام الاستعلام
        $services = DB::table('private_wing_services as pws')
            ->join('service_specialization as ss', 'pws.service_id', '=', 'ss.id')
            ->where('pws.private_wing_id', $privateWing->id)
            ->select(
                'ss.namesv as service_name',
                'pws.service_fee',
                'pws.is_daily',
                'pws.total_amount'
            )
            ->get();

        // حساب المجموع الكلي للخدمات
        $totalAmount = $services->sum('total_amount');

        return view('private-wings.show', compact('privateWing', 'services', 'totalAmount'));
    }    public function edit(PrivateWing $privateWing)
    {
        $privateWing->load('services');
        $typeSpecializations = TypeSpecialization::all();
        return view('private-wings.edit', compact('privateWing','typeSpecializations'));
    }    public function update(Request $request, PrivateWing $privateWing)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'hospital' => 'required|string|max:255',
                'health_department' => 'required|string|max:255',
                'patient_name' => 'required|string|max:255',
                'file_number' => 'required|string|max:255',
                'statistical_number' => 'required|string|max:255',
                'entry_date' => 'required|date',
                'exit_date' => 'nullable|date',
                'days_count' => 'required|integer',
                'companion_bed_fee' => 'nullable|numeric',
                'nutrition_fee' => 'nullable|numeric',
                'medicine_supplies_fee' => 'nullable|numeric',
                'laboratory_tests_fee' => 'nullable|numeric',
                'xray_fees' => 'nullable|numeric',
                'sonar_fees' => 'nullable|numeric',
                'deposit_amount' => 'required|numeric',
                'receipt_number' => 'required|string|max:255',
                'receipt_date' => 'required|date',
                'services' => 'required|array',
                'services.*.id' => 'required|exists:service_specialization,id',
                'services.*.fee' => 'required|numeric',
                'services.*.is_daily' => 'required|boolean'
            ]);

            // تحديث بيانات الجناح الخاص
            $privateWing->update($validated);

            // حذف الخدمات القديمة
            DB::table('private_wing_services')->where('private_wing_id', $privateWing->id)->delete();

            // إضافة الخدمات الجديدة وحساب المجموع
            $totalAmount = 0;
            foreach ($validated['services'] as $service) {
                $serviceAmount = $service['is_daily'] ? 
                    $service['fee'] * $validated['days_count'] : 
                    $service['fee'];

                DB::table('private_wing_services')->insert([
                    'private_wing_id' => $privateWing->id,
                    'service_id' => $service['id'],
                    'service_fee' => $service['fee'],
                    'is_daily' => $service['is_daily'],
                    'total_amount' => $serviceAmount,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $totalAmount += $serviceAmount;
            }

            // تحديث المبلغ الإجمالي
            $privateWing->update(['total_amount' => $totalAmount]);

            DB::commit();
            
            return redirect()->route('private-wings.index')
                ->with('success', 'تم تحديث سجل الجناح الخاص بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating private wing: ' . $e->getMessage());
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
            \Log::error('Error fetching institutions: ' . $e->getMessage());
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
            \Log::error('Error fetching institutions: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }    public function getServicePrice(Request $request)
    {
        try {
            if (!$request->service_id) {
                \Log::warning('getServicePrice called without service_id');
                return response()->json(['price' => 0]);
            }

            $service = Service_Specialization::find($request->service_id);
            
            if (!$service) {
                \Log::warning('Service not found with ID: ' . $request->service_id);
                return response()->json(['price' => 0]);
            }

            return response()->json(['price' => $service->price ?: 0]);
        } catch (\Exception $e) {
            \Log::error('Error in getServicePrice: ' . $e->getMessage());
            return response()->json(['price' => 0]);
        }
    }
}