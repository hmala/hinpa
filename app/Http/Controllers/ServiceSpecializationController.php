<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\TypeSpecialization;
use Illuminate\Http\Request;

class ServiceSpecializationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ربط الخدمات بالتخصصات.عرض')->only(['index']);
        $this->middleware('permission:ربط الخدمات بالتخصصات.اضافة')->only(['create', 'store']);
        $this->middleware('permission:ربط الخدمات بالتخصصات.تعديل')->only(['edit', 'update']);
        $this->middleware('permission:ربط الخدمات بالتخصصات.حذف')->only('destroy');
    }

    public function index()
    {
        $services = Service::with('typeSpecializations')->get();
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
}