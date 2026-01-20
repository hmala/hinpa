<?php

namespace App\Http\Controllers;

use App\Models\mohs;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
class MohsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mohs= mohs::all();

        return view('mohs.mohs',compact('mohs'));
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
            'mohname' => 'required|unique:mohs|max:255',
           
        ],[

            'mohcode' =>'يرجي ادخال اسم القسم',
            'mohname' =>'اسم القسم مسجل مسبقا',


        ]);
          mohs::create([
              'mohcode'=>$request->mohcode,
              'mohname'=>$request->mohname,
              'Created_by'=>(Auth::user()->email),
             
          ]);
          session()->flash('Add','تم اضافة الدائرة بنجاح');
          return redirect('/mohs');
         
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\mohs  $mohs
     * @return \Illuminate\Http\Response
     */
    public function show(mohs $mohs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\mohs  $mohs
     * @return \Illuminate\Http\Response
     */
    public function edit(mohs $mohs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\mohs  $mohs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, mohs $mohs)
    {
        $id = $request->id;

        $this->validate($request, [

            'mohcode' => 'required|max:255|unique:mohs,mohcode,'.$id,
            'mohname' => 'required',
        ],[

            'mohcode.required' =>'يرجي ادخال رمز المؤسسة',
            'mohcode.unique' =>'رمز المؤسسة مسجل مسبقا',
            'mohname.required' =>'يرجي ادخال اسم المؤسسة',
            'mohname.unique' =>'اسم المؤسسة مسجل مسبقا',

        ]);

        $mohs = mohs::find($id);
        $mohs->update([
            'mohcode' => $request->mohcode,
            'mohname' => $request->mohname,
        ]);

        session()->flash('edit','تم تعديل القسم بنجاج');
        return redirect('/mohs');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\mohs  $mohs
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
           $mohs = mohs::findOrFail($request->id);
         $mohs->delete();
         session()->flash('delete', 'تم حذف المنتج بنجاح');
         return redirect('/mohs');
}
}