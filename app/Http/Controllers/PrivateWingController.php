<?php

namespace App\Http\Controllers;

use App\Models\PrivateWing;
use Illuminate\Http\Request;

class PrivateWingController extends Controller
{    public function getSpecializations($serviceId)
    {
        try {
            // مباشرة من جدول service_specialization
            $specializations = \App\Models\TypeSpecialization::join('service_specialization', 'type_specializations.id', '=', 'service_specialization.type_specialization_id')
                ->where('service_specialization.service_id', $serviceId)
                ->select('type_specializations.id', 'type_specializations.tsname')
                ->distinct()
                ->get();

            return response()->json($specializations);
        } catch (\Exception $e) {
            \Log::error('Error in getSpecializations: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء جلب التخصصات'], 500);
        }
    }    public function getServiceSpecializations($serviceId, $specializationId)
    {
        try {
            $specializations = \App\Models\Service_Specialization::where('service_id', $serviceId)
                ->where('type_specialization_id', $specializationId)
                ->select('id', 'codesv', 'namesv', 'price')
                ->get();

            if ($specializations->isEmpty()) {
                \Log::info('No specializations found for service_id: ' . $serviceId . ' and specialization_id: ' . $specializationId);
            }
            
            return response()->json($specializations);
        } catch (\Exception $e) {
            \Log::error('Error in getServiceSpecializations: ' . $e->getMessage());
            return response()->json([
                'error' => 'حدث خطأ أثناء جلب الخدمات المتخصصة',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function index()
    {
        $privateWings = PrivateWing::latest()->paginate(10);
        return view('private-wings.index', compact('privateWings'));
    }    public function create()
    {
        $services = \App\Models\Service::all();
        return view('private-wings.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hospital' => 'required|string|max:255',
            'health_department' => 'required|string|max:255',
            'patient_name' => 'required|string|max:255',
            'file_number' => 'required|string|max:255',
            'statistical_number' => 'required|string|max:255',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date',
            'days_count' => 'required|integer',
            'patient_bed_fee' => 'required|numeric',
            'companion_bed_fee' => 'nullable|numeric',
            'nutrition_fee' => 'nullable|numeric',
            'medicine_supplies_fee' => 'nullable|numeric',
            'laboratory_tests_fee' => 'nullable|numeric',
            'xray_fees' => 'nullable|numeric',
            'sonar_fees' => 'nullable|numeric',
            'deposit_amount' => 'required|numeric',
            'receipt_number' => 'required|string|max:255',
            'receipt_date' => 'required|date',
        ]);

        PrivateWing::create($validated);

        return redirect()->route('private-wings.index')
            ->with('success', 'تم إنشاء سجل الجناح الخاص بنجاح');
    }

    public function show(PrivateWing $privateWing)
    {
        return view('private-wings.show', compact('privateWing'));
    }

    public function edit(PrivateWing $privateWing)
    {
        return view('private-wings.edit', compact('privateWing'));
    }

    public function update(Request $request, PrivateWing $privateWing)
    {
        $validated = $request->validate([
            'hospital' => 'required|string|max:255',
            'health_department' => 'required|string|max:255',
            'patient_name' => 'required|string|max:255',
            'file_number' => 'required|string|max:255',
            'statistical_number' => 'required|string|max:255',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date',
            'days_count' => 'required|integer',
            'patient_bed_fee' => 'required|numeric',
            'companion_bed_fee' => 'nullable|numeric',
            'nutrition_fee' => 'nullable|numeric',
            'medicine_supplies_fee' => 'nullable|numeric',
            'laboratory_tests_fee' => 'nullable|numeric',
            'xray_fees' => 'nullable|numeric',
            'sonar_fees' => 'nullable|numeric',
            'deposit_amount' => 'required|numeric',
            'receipt_number' => 'required|string|max:255',
            'receipt_date' => 'required|date',
        ]);

        $privateWing->update($validated);

        return redirect()->route('private-wings.index')
            ->with('success', 'تم تحديث سجل الجناح الخاص بنجاح');
    }

    public function destroy(PrivateWing $privateWing)
    {
        $privateWing->delete();

        return redirect()->route('private-wings.index')
            ->with('success', 'تم حذف سجل الجناح الخاص بنجاح');
    }
}
