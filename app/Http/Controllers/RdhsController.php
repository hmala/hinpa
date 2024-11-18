<?php

namespace App\Http\Controllers;

use App\Models\rdhs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class RdhsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rdhs= rdhs::all();
        return  view('rdhs.rdhs',compact('rdhs','rdhs'));
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
        $validatedData = $request->validate([
            'Spcuname' => 'required|unique:rdhs|max:255',
           
        ],[

            'Spcuno' =>'يرجي ادخال اسم القسم',
            'Spcuname' =>'اسم القسم مسجل مسبقا',


        ]);
        rdhs::create([
              'Spcuno'=>$request->Spcuno,
              'Spcuname'=>$request->Spcuname,
              'Created_by'=>(Auth::user()->email),
             
          ]);
          session()->flash('Add','تم اضافة الدائرة بنجاح');
          return redirect('/rdhs');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\rdhs  $rdhs
     * @return \Illuminate\Http\Response
     */
    public function show(rdhs $rdhs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\rdhs  $rdhs
     * @return \Illuminate\Http\Response
     */
    public function edit(rdhs $rdhs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\rdhs  $rdhs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, rdhs $rdhs)
    {
        $id = $request->id;

        $this->validate($request, [

            'Spcuno' => 'required|max:255|unique:rdhs,Spcuno,'.$id,
            'Spcuname' => 'required',
        ],[

            'Spcuno.required' =>'يرجي ادخال رمز المؤسسة',
            'Spcuno.unique' =>'رمز المؤسسة مسجل مسبقا',
            'Spcuname.required' =>'يرجي ادخال اسم المؤسسة',
            'Spcuname.unique' =>'اسم المؤسسة مسجل مسبقا',

        ]);

        $mohs = rdhs::find($id);
        $mohs->update([
            'Spcuno' => $request->Spcuno,
            'Spcuname' => $request->Spcuname,
        ]);

        session()->flash('edit','تم تعديل القسم بنجاج');
        return redirect('/rdhs');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\rdhs  $rdhs
     * @return \Illuminate\Http\Response
     */
    public function destroy(rdhs $rdhs)
    {
        //
    }
}
