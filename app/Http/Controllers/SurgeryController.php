<?php

namespace App\Http\Controllers;

use App\Models\surgery;
use Illuminate\Http\Request;
use App\Models\mohs;
use App\Models\rdhs;
use App\Models\fck;

use App\Models\surg;
use App\Models\salsurs;
use Illuminate\Support\Facades\Auth;
class SurgeryController extends Controller
{
    function __construct()
    {
    $this->middleware('permission:العمليات الجراحية', ['only' => ['index','store','pations_approve','create','edit','update','destroy']]);
    
    
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fck=fck::all();

        $surgery = surgery::all(); 
        $rdhs=rdhs::all(); 
        $mohs=mohs::all(); 
        $surg=surg::all(); 
        if  (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
            $surgery = surgery::all(); 

        }elseif (Auth::user()->roles_name  === ["stat-doh"]){
                  $surgery = surgery::where('moh_id', Auth::user()->mohcode)->get();
  
        }else{
            $surgery = surgery::where('fck_id', Auth::user()->fckid)->get();

        }
        return view('surgery.surgery',compact('rdhs','mohs','surg','surgery'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $salsur = salsurs::all();
        $surgery = surgery::all(); 
        $surg = surg::all(); 
        $rdhs=rdhs::all(); 
        $mohs= mohs::all();
        return view('surgery.add_surgery', compact('mohs','rdhs','surg','salsur','surgery'));
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
            'month'   => 'required',
            'year'    => 'required',
            'surgtyp' => 'required|array',
            'khasa'   => 'required|array',
            'fkubra'  => 'required|array',
            'kubra'   => 'required|array',
            'mtws'    => 'required|array',
            'sugra'   => 'required|array',
        ]);
    
        // التحقق ومعالجة كل إدخال في المصفوفة
        foreach ($validatedData['surgtyp'] as $index => $surgtyp) {
            $existingRecord = Surgery::where('month', $validatedData['month'])
                ->where('year', $validatedData['year'])
                ->where('surgtyp', $surgtyp)
                ->where('fck_id', Auth::user()->fckid)
                ->first();
    
            if ($existingRecord) {
                return redirect()->back()->withErrors([
                    'error' => 'تم تسجيل هذه البيانات بالفعل لهذا الشهر والسنة والاختصاص: ' . $surgtyp
                ]);
            }
    
            // إنشاء السجل إذا لم يكن مكررًا
            Surgery::create([
                'month'    => $validatedData['month'],
                'year'     => $validatedData['year'],
                'surgtyp'  => $surgtyp,
                'khasa'    => $validatedData['khasa'][$index],
                'fkubra'   => $validatedData['fkubra'][$index],
                'kubra'    => $validatedData['kubra'][$index],
                'mtws'     => $validatedData['mtws'][$index],
                'sugra'    => $validatedData['sugra'][$index],
                'fck_id'   => Auth::user()->fckid,
                'fctypesid'      => Auth::user()->fcktid,
                'save'           => 0,
                'moh_id'   => Auth::user()->mohcode,
                'creator'  => Auth::user()->email,
                'Created_by'=> Auth::user()->email,
            ]);
        }
    
        session()->flash('success', 'تم حفظ البيانات بنجاح!');
        return redirect('/surgery');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\surgery  $surgery
     * @return \Illuminate\Http\Response
     */
    public function show(surgery $surgery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\surgery  $surgery
     * @return \Illuminate\Http\Response
     */
    public function edit( $id)
    {
        $surgery=surgery::where('id',$id)->first();
        $fck=fck::all();
        $surg = surg::all(); 
        $rdhs=rdhs::all(); 
        $mohs=mohs::all(); 
        $salsur = salsurs::all();
        return view('surgery.edit_surgery', compact('mohs','surg','salsur','fck','surgery'));
 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\surgery  $surgery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $surgery = surgery::findOrFail($request->id);
        $surgery->update([
           
            'month'     =>$request->month,
            'year'      => $request->year,
            'surgtyp'    => $request->salid,
            'khasa'      => $request->khasa,
            'fkubra'      => $request->fkubra,
            'kubra'      => $request->kubra,
            'mtws'      => $request->mtws,
            'sugra'      => $request->sugra,
            'fck_id'   => Auth::user()->fckid,
            'moh_id'    => Auth::user()->mohcode,
            'creator'=> Auth::user()->email,
            'Created_by'=> Auth::user()->email,
            'save'=> 0,
           
        ]);
        session()->flash('edit', 'تم إضافة البيانات بنجاح');
        return redirect('/surgery');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\surgery  $surgery
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
 {
     $surgery = surgery::findOrFail($id);
     $surgery->delete();

     session()->flash('delete_invoice');
     return redirect('/surgery'); }
}
