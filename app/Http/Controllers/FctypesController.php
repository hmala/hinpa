<?php

namespace App\Http\Controllers;

use App\Models\Fctypes;
use App\Models\mohs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FctypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mohs=mohs::all();
        $Fctypes=Fctypes::all();
        return  view('Fctypes.Fctypes',compact('mohs','Fctypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Fctypes::create([
            'Fname'     => $request->Product_name,
            'Created_by'=>(Auth::user()->email),
        ]);
        session()->flash('Add', 'تم اضافة المنتج بنجاح ');
        return redirect('/Fctypes');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Fctypes  $fctypes
     * @return \Illuminate\Http\Response
     */
    public function show(Fctypes $fctypes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Fctypes  $fctypes
     * @return \Illuminate\Http\Response
     */
    public function edit(Fctypes $fctypes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fctypes  $fctypes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fctypes $fctypes)
    {

        $id = mohs::where('mohname', $request->section_name)->first()->id;

       $Fctypes = Fctypes::findOrFail($request->pro_id);

       $Fctypes->update([
       'fname' => $request->Product_name,
       ]);

       session()->flash('Edit', 'تم تعديل المنتج بنجاح');
       return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Fctypes  $fctypes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $Products = Fctypes::findOrFail($request->pro_id);
        $Products->delete();
        session()->flash('delete', 'تم حذف المنتج بنجاح');
        return back();
    }
}
