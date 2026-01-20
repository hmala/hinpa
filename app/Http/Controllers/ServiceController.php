<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\TypeSpecialization;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::all();
        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function getNextServiceCode()
    {
        $lastService = Service::orderBy('sercode', 'desc')->first();
        return $lastService ? $lastService->sercode + 1 : 1;
    }

    public function create()
    {
        $typeSpecializations = TypeSpecialization::all();
        $nextCode = $this->getNextServiceCode();
        return view('services.create', compact('typeSpecializations', 'nextCode'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sercode' => 'required|integer|unique:services',
            'sername' => 'required|string|max:255',
            'type_specializations_id' => 'required|exists:type_specializations,id'
        ]);

        $service = Service::create([
            'sercode' => $validated['sercode'],
            'sername' => $validated['sername'],
            'type_specializations_id' => $validated['type_specializations_id']
        ]);

        return redirect()->route('services.index')
            ->with('success', 'تم إضافة الخدمة بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'sercode' => 'required|integer|unique:services,sercode,' . $service->id,
            'sername' => 'required|string|max:255'
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'تم حذف الخدمة بنجاح');
    }
}
