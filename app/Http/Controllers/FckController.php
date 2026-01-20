<?php

namespace App\Http\Controllers;

use App\Models\fck;
use App\Models\mohs;
use App\Models\Fctypes;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class FckController extends Controller
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
       $fck=fck::all();

        return view('fcks.Fcks',compact('fck','mohs','Fctypes'));
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
        fck::create([
            'Fckname'=> $request->Product_name,
            'moh_id'=> $request->Section,
            'fctypesid'=> $request->product,

            'Created_by'=>(Auth::user()->email),
        ]);
        session()->flash('Add', 'تم اضافة المنتج بنجاح ');
        return redirect('/Fcks');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\fck  $fck
     * @return \Illuminate\Http\Response
     */
    public function show(fck $fck)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\fck  $fck
     * @return \Illuminate\Http\Response
     */
    public function edit(fck $fck)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\fck  $fck
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, fck $fck)
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
     * @param  \App\Models\fck  $fck
     * @return \Illuminate\Http\Response
     */
    public function destroy(fck $fck)
    {
        //
    }
}
