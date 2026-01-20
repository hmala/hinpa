<?php

namespace App\Http\Controllers;

use App\Models\TypeSpecialization;
use Illuminate\Http\Request;

class TypeSpecializationController extends Controller
{
    public function index()
    {
        $specializations = TypeSpecialization::all();
        return view('type-specializations.index', compact('specializations'));
    }

    public function create()
    {
        return view('type-specializations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tscode' => 'required|integer|unique:type_specializations',
            'tsname' => 'required|string|max:255',
        ]);

        TypeSpecialization::create($validated);

        return redirect()->route('type-specializations.index')
            ->with('success', 'تم إضافة التخصص بنجاح');
    }

    public function show(TypeSpecialization $typeSpecialization)
    {
        return view('type-specializations.show', ['specialization' => $typeSpecialization]);
    }

    public function edit(TypeSpecialization $typeSpecialization)
    {
        return view('type-specializations.edit', ['specialization' => $typeSpecialization]);
    }

    public function update(Request $request, TypeSpecialization $typeSpecialization)
    {
        $validated = $request->validate([
            'tscode' => 'required|integer|unique:type_specializations,tscode,' . $typeSpecialization->id,
            'tsname' => 'required|string|max:255',
        ]);

        $typeSpecialization->update($validated);

        return redirect()->route('type-specializations.index')
            ->with('success', 'تم تحديث التخصص بنجاح');
    }

    public function destroy(TypeSpecialization $typeSpecialization)
    {
        $typeSpecialization->delete();

        return redirect()->route('type-specializations.index')
            ->with('success', 'تم حذف التخصص بنجاح');
    }
}
