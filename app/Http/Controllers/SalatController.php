<?php

namespace App\Http\Controllers;

use App\Models\salat;
use Illuminate\Http\Request;
use App\Models\mohs;
use App\Models\fck;

use App\Models\rdhs;
use App\Models\surg;
use App\Models\salsurs;
use Illuminate\Support\Facades\Auth;
class SalatController extends Controller
{function __construct()
    {
    $this->middleware('permission:الصالات', ['only' => ['index','store','pations_approve','create','edit','update','destroy']]);
    
    
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $fck=fck::all();
        $salsurs = salsurs::all(); 
        $mohs=mohs::all(); 
        if  (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
            $salat=salat::all(); 

        }elseif (Auth::user()->roles_name  === ["stat-doh"]){
                  $salat = salat::where('moh_id', Auth::user()->mohcode)->get();
  
        }else{
            $salat = salat::where('fck_id', Auth::user()->fckid)->get();

        }
        return view('salat.salat',compact('mohs','salsurs','fck','salat'));
    }
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $salsur = salsurs::all();
        $surg = surg::all(); 
        $rdhs=rdhs::all(); 
        $mohs= mohs::all();
        return view('salat.add_salat', compact('mohs','rdhs','surg','salsur'));
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
            'month'     => 'required',
            'year'      => 'required',
            'salid'     => 'required|array',
            'salnum'    => 'required|array',
            'bsalnum'   => 'required|array',
        ]);
    
        foreach ($validatedData['salid'] as $index => $salid) {
            // التحقق من وجود سجل مسبق بنفس الشهر والسنة وsalid
            $existingRecord = salat::where('month', $validatedData['month'])
                ->where('year', $validatedData['year'])
                ->where('salid', $salid)
                ->where('fck_id', Auth::user()->fckid)
                ->first();
    
            if ($existingRecord) {
                // إعادة المستخدم مع رسالة خطأ إذا كان الإدخال مكررًا
                return redirect()->back()->withErrors([
                    'error' => 'تم تسجيل هذه البيانات بالفعل لهذا الشهر والسنة: ' . $salid
                ]);
            }
    
            // إنشاء سجل جديد إذا لم يكن مكررًا
            salat::create([
                'month'     => $validatedData['month'],
                'year'      => $validatedData['year'],
                'salid'     => $salid,
                'salnum'    => $validatedData['salnum'][$index],
                'bsalnum'   => $validatedData['bsalnum'][$index],
                'fctypesid' => Auth::user()->fcktid,
                'fck_id'    => Auth::user()->fckid,
                'moh_id'    => Auth::user()->mohcode,
                'creator'   => Auth::user()->email,
                'Created_by'=> Auth::user()->email,
                'save'      => 0,
            ]);
        }
    
        // رسالة نجاح
        session()->flash('Add', 'تم إضافة البيانات بنجاح');
        return redirect('/salat');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\salat  $salat
     * @return \Illuminate\Http\Response
     */
    public function show(salat $salat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\salat  $salat
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {       

        $salat=salat::where('id',$id)->first();
        $fck=fck::all();
        $surg = surg::all(); 
        $rdhs=rdhs::all(); 
        $mohs=mohs::all(); 
        $salsur = salsurs::all();
        return view('salat.edit_salat', compact('mohs','surg','salsur','salat'));
 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\salat  $salat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $salat = salat::findOrFail($request->id);
        $salat->update([
           
            'month'     =>$request->month,
            'year'      => $request->year,
            'salid'    => $request->salid,
            'salnum'      => $request->salnum,
            'bsalnum'      => $request->bsalnum,
           
            'fck_id'   => Auth::user()->fckid,
            'moh_id'    => Auth::user()->mohcode,
            'creator'=> Auth::user()->email,
            'Created_by'=> Auth::user()->email,
            'save'=> 0,
           
        ]);
        session()->flash('edit', 'تم إضافة البيانات بنجاح');
        return redirect('/salat');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\salat  $salat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $salat = salat::where('id', $id)->first();
        $salat->Delete();
        session()->flash('delete_invoice');
        return redirect('/salat');
    }
}
