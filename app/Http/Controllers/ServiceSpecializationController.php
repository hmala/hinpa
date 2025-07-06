<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\TypeSpecialization;
use App\Models\Service_Specialization;
use App\Imports\ServiceSpecializationImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ServiceSpecializationController extends Controller
{
  

    public function index()
    {
        $services = Service::all();
        return view('service-specializations.index', compact('services'));
    }

    public function create()
    {
        $services = Service::all();
        $typeSpecializations = TypeSpecialization::all();
        return view('service-specializations.create', compact('services', 'typeSpecializations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'type_specialization_id' => 'required|exists:type_specializations,id',
            'codesv' => 'required|string',
            'namesv' => 'required|string',
            'price' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        $service = Service::findOrFail($request->service_id);
        $service->typeSpecializations()->attach($request->type_specialization_id, [
            'codesv' => $request->codesv,
            'namesv' => $request->namesv,
            'price' => $request->price,
            'notes' => $request->notes
        ]);

        return redirect()->route('service-specializations.index')
            ->with('success', 'تم ربط الخدمة بالتخصص بنجاح');
    }    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            \DB::beginTransaction();
            Excel::import(new ServiceSpecializationImport, $request->file('file'));
            \DB::commit();
            return redirect()->back()->with('success', 'تم استيراد البيانات بنجاح');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء استيراد البيانات: ' . $e->getMessage());
        }
    }

    public function edit(Service $service, TypeSpecialization $specialization)
    {
        $pivotData = $service->typeSpecializations()
            ->where('type_specialization_id', $specialization->id)
            ->first()
            ->pivot;

        $services = Service::all();
        $typeSpecializations = TypeSpecialization::all();

        return view('services.specializations.edit', compact(
            'service',
            'specialization',
            'pivotData',
            'services',
            'typeSpecializations'
        ));
    }

    public function update(Request $request, Service $service, TypeSpecialization $specialization)
    {
        $validated = $request->validate([
            'codesv' => 'required|string',
            'namesv' => 'required|string',
            'price' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        $service->typeSpecializations()->updateExistingPivot($specialization->id, [
            'codesv' => $request->codesv,
            'namesv' => $request->namesv,
            'price' => $request->price,
            'notes' => $request->notes
        ]);

        return redirect()->route('service-specializations.index')
            ->with('success', 'تم تحديث ربط الخدمة بالتخصص بنجاح');
    }

    public function destroy(Service $service, TypeSpecialization $specialization)
    {
        $service->typeSpecializations()->detach($specialization->id);
        return redirect()->route('service-specializations.index')
            ->with('success', 'تم حذف ربط الخدمة بالتخصص بنجاح');
    }

    public function showImportForm()
    {
        return view('service-specializations.import');
    }

    public function getServicePrice(Request $request)
    {
        $service = Service_Specialization::find($request->service_id);
        if ($service) {
            return response()->json(['price' => $service->price]);
        }
        return response()->json(['price' => 0], 404);
    }
}