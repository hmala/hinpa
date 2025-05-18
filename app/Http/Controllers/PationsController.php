<?php

namespace App\Http\Controllers;

use App\Models\pations;
use App\Models\mohs;
use App\Models\rdhs;
use App\Models\surg;
use App\Models\fck;
use App\Models\salsurs;
use App\Models\surgery;
use App\Models\salat;
use App\Models\hwadth;


use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;

class PationsController extends Controller
{
    function __construct()
{
$this->middleware('permission:الاسرة', ['only' => ['index','store','create','edit','update','destroy']]);
$this->middleware('permission:الاسرة موثقة', ['only' => ['pations_approve']]);

$this->middleware('permission:الاسرة غير الموثقة', ['only' => ['pations_nonapprove','show1']]);
$this->middleware('permission:عدد الاسرة حسب كل مؤسسة', ['only' => ['print_pations']]);


}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fck=fck::all();
        $surg = surg::all(); 
        $rdhs=rdhs::all(); 
        $mohs=mohs::all(); 
        if  (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
            $pations = pations::orderBy('month', 'desc')->get(); 

        }elseif (Auth::user()->roles_name  === ["stat-doh"]){
                  $pations = Pations::where('moh_id', Auth::user()->mohcode)->
                  orderBy('month', 'desc')->get();
  
        }else{
            $pations = Pations::where('fck_id', Auth::user()->fckid)->orderBy('month', 'desc')->get();

        }
        return view('pations.pations',compact('rdhs','pations','mohs','surg','fck'));
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
        return view('pations.add_pations', compact('mohs','rdhs','surg','salsur'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
        // التحقق من صحة الإدخالات
        $validatedData = $request->validate([
            'month'   => 'required',
            'year'    => 'required',
            'rdhs.*'  => 'required|string', // أسماء الاختصاصات
            'units.*' => 'required',
            'beds.*'  => 'required',
            'paout.*' => 'required',
            'past.*'  => 'required',
            'mkoth.*' => 'required',
            'bde.*'   => 'required',
        ], [
            'rdhs.*.required'  => 'يرجى عدم ترك أي خلية فارغة في حقل "rdhs".',
            'units.*.required' => 'يرجى عدم ترك أي خلية فارغة في حقل "units".',
            'beds.*.required'  => 'يرجى عدم ترك أي خلية فارغة في حقل "beds".',
            'paout.*.required' => 'يرجى عدم ترك أي خلية فارغة في حقل "paout".',
            'past.*.required'  => 'يرجى عدم ترك أي خلية فارغة في حقل "past".',
            'mkoth.*.required' => 'يرجى عدم ترك أي خلية فارغة في حقل "mkoth".',
            'bde.*.required'   => 'يرجى عدم ترك أي خلية فارغة في حقل "bde".',
            'month.required'   => 'حقل الشهر مطلوب.',
            'year.required'    => 'حقل السنة مطلوب.',
        ]);
    
        // جمع أسماء الاختصاصات من الإدخالات
        $rdhNames = $validatedData['rdhs'];
    
        // جلب أرقام الاختصاصات (IDs) بناءً على الأسماء
        $rdhIds = rdhs::whereIn('Spcuname', $rdhNames)->pluck('id', 'Spcuname');
    
        // التحقق من وجود أسماء غير موجودة في قاعدة البيانات
        foreach ($rdhNames as $rdhName) {
            if (!isset($rdhIds[$rdhName])) {
                return redirect()->back()->withErrors(['error' => 'الاختصاص "' . $rdhName . '" غير موجود في قاعدة البيانات.']);
            }
        }
    
        // التحقق من وجود بيانات مسبقة لنفس الشهر والمؤسسة
        $existingRecords = pations::where('month', $validatedData['month'])
            ->where('year', $validatedData['year'])
            ->where('fck_id', Auth::user()->fckid)
            ->whereIn('spebed', $rdhIds->values())
            ->get();
    
        if ($existingRecords->isNotEmpty()) {
            $existingBeds = $existingRecords->pluck('spebed')->implode(', ');
            return redirect()->back()->withErrors(['error' => 'تم تسجيل البيانات بالفعل لهذا الشهر للاختصاصات التالية: ' . $existingBeds]);
        }
    
        // جلب معلومات المؤسسة
        $fck = fck::where('moh_id', Auth::user()->mohcode)
            ->where('id', Auth::user()->fckid)
            ->first();
        $hfcode = $fck ? $fck->hfcode : null;
    
        // حفظ البيانات المدخلة
        $dataToSave = [];
        foreach ($validatedData['rdhs'] as $index => $rdhName) {
            $dataToSave[] = [
                'month'          => $validatedData['month'],
                'year'           => $validatedData['year'],
                'spebed'         => $rdhIds[$rdhName], // حفظ رقم الاختصاص
                'unitnum'        => $validatedData['units'][$index],
                'bedm'           => $validatedData['beds'][$index],
                'outpationmon'   => $validatedData['paout'][$index],
                'stayoutpation'  => $validatedData['past'][$index],
                'mkoth'          => $validatedData['mkoth'][$index],
                'death'          => $validatedData['bde'][$index],
                'fck_id'         => Auth::user()->fckid,
                'moh_id'         => Auth::user()->mohcode,
                'fctypesid'      => Auth::user()->fcktid,
                'creator'        => Auth::user()->email,
                'Created_by'     => Auth::user()->email,
                'save'           => 0,
                'status_value'   => 2,
                'f_approve'      => 2,
                'status'         => 'غير موثقة',
                'hfcode'         => $hfcode,
                'Approv_user'    => Auth::user()->email,
                'fapprove'       => 'غير موثقة',
            ];
        }
    
        pations::insert($dataToSave);
    
        // إعداد رسالة النجاح
        session()->flash('success', 'تم حفظ البيانات بنجاح!');
        return redirect('/pations');
    }
    
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pations  $pations
     * @return \Illuminate\Http\Response
     */
    public function show(pations $pations)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pations  $pations
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $pations=pations::where('id',$id)->first();
       $fck=fck::all();
       $surg = surg::all(); 
       $rdhs=rdhs::all(); 
       $mohs=mohs::all(); 
       $salsur = salsurs::all();
       return view('pations.edit_pations', compact('mohs','rdhs','surg','salsur','pations'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\pations  $pations
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) 
{
    // البحث عن السجل المطلوب باستخدام `id`
    $pations = pations::findOrFail($request->id);

    // البحث عن `id` الاختصاص بناءً على الاسم المرسل
    $rdh = rdhs::where('Spcuname', $request->rdhs)->first();

    if (!$rdh) {
        return redirect()->back()->withErrors(['error' => 'الاختصاص المرسل غير موجود في قاعدة البيانات.']);
    }

    // التحديث بالسجل الجديد
    $pations->update([
        'month'          => $request->month,
        'year'           => $request->year,
        'spebed'         => $rdh->id, // حفظ رقم الاختصاص بدلاً من الاسم
        'unitnum'        => $request->unit,
        'bedm'           => $request->beds,
        'outpationmon'   => $request->paout,
        'stayoutpation'  => $request->past,
        'mkoth'          => $request->mkoth,
        'death'          => $request->bde,
        'Approv_user'    => Auth::user()->email,
        'note'           => $request->note,
        'save'           => 0,
        'status_value'   => 2,
        'status'         => 'غير موثقة',
        'fapprove'       => 'غير موثقة',
    ]);

    // رسالة نجاح
    session()->flash('edit', 'تم تعديل البيانات بنجاح!');
    return redirect('/pations');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\pations  $pations
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $pations = pations::where('id', $id)->first();
        $pations->Delete();
        session()->flash('delete_invoice');
        return redirect('/pations');
    }

public function pations_approve()
{

$fck = fck::all();
$surg = surg::all();
$rdhs = rdhs::all();
$mohs = mohs::all();

if  (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
    // جلب كل المرضى بشرط أن تكون القيمة status_value = 1
    $pations = pations::where('status_value', 1)->get();

} elseif (Auth::user()->roles_name === ["stat-doh"]) {
    // جلب المرضى بناءً على moh_id والقيمة status_value = 1
    $pations = Pations::where('moh_id', Auth::user()->mohcode)
                      ->where('status_value', 1)
                      ->get();

} else {
    // جلب المرضى بناءً على fck_id والقيمة status_value = 1
    $pations = Pations::where('fck_id', Auth::user()->fckid)
                      ->where('status_value', 1)
                      ->get();
}

return view('pations.pations', compact('rdhs', 'pations', 'mohs', 'surg', 'fck'));
    return view('pations.pations_approve',compact('pations'));
}

public function pations_nonapprove()
{
    $fck = fck::all();
    $surg = surg::all();
    $rdhs = rdhs::all();
    $mohs = mohs::all();
    
    if  (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
        // جلب كل المرضى بشرط أن تكون القيمة status_value = 1
        $pations = pations::where('status_value', 2)->get();
    
    } elseif (Auth::user()->roles_name === ["stat-doh"]) {
        // جلب المرضى بناءً على moh_id والقيمة status_value = 1
        $pations = Pations::where('moh_id', Auth::user()->mohcode)
                          ->where('status_value', 2)
                          ->get();
    
    } else {
        // جلب المرضى بناءً على fck_id والقيمة status_value = 1
        $pations = Pations::where('fck_id', Auth::user()->fckid)
                          ->where('status_value', 2)
                          ->get();
    }
    
        return view('pations.pations_nonapprove',compact('pations'));
}

public function show1($id)
{
    $fck=fck::all();
    $surg = surg::all(); 
    $rdhs=rdhs::all(); 
    $mohs=mohs::all(); 
    $salsur = salsurs::all();
    $pations = pations::where('id', $id)->first();
    return view('pations.status_update',compact('mohs','rdhs','surg','salsur','pations'));

}
public function status_update($id, Request $request)
{
    $fck=fck::all();
    $surg = surg::all(); 
    $rdhs=rdhs::all(); 
    $mohs=mohs::all(); 
    $salsur = salsurs::all();
    $pations = pations::findOrFail($id);

    if ($request->Status === 'موثق') {

        $pations->update([
            'status_value' => 1,
            'Status' => $request->Status,
            'Approv_dt' =>now(),
            'note'=> $request->note,
            'Approv_user'=>Auth::user()->email,

        ]);

      
    }

    else {
        $pations->update([
            'status_value' => 2,
            'Status' => $request->Status,
            'Approv_dt' =>now(),
            'note'=> $request->note,
            'Approv_user'=>Auth::user()->email,
        ]);
      
    }
    session()->flash('status_update');
    return redirect('/pations');

}
public function print_pations(Request $request)
{
    $fck=fck::all();
    $surg = surg::all(); 
    $rdhs=rdhs::all(); 
    $mohs=mohs::all(); 

    $surgery=surgery::where('fck_id', Auth::user()->fckid)
    ->where('month', request('month'))
    ->where('year', $request->input('year'))
    ->get();
    $salat=salat::where('fck_id', Auth::user()->fckid)
    ->where('month', request('month'))
    ->where('year', $request->input('year'))
    ->get();
    $pations = pations::where('fck_id', Auth::user()->fckid)
    ->where('month', request('month'))
    ->where('year', $request->input('year'))
    ->get();
    $hwadth=hwadth::where('fck_id', Auth::user()->fckid)
    ->where('month', request('month'))
    ->where('year', $request->input('year'))
    ->get();
    return view('report',compact('rdhs','pations','mohs','surg','fck','surgery','salat','hwadth'));
}
public function getHallsByRdh($rdhId) {
    $halls = rdhs::where('rdh_id', $rdhId)->get();
    return response()->json($halls);
}
}