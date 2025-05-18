<?php

namespace App\Http\Controllers;

use App\Models\hwadth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HwadthController extends Controller
{
    function __construct()
    {
    $this->middleware('permission:حوادث', ['only' => ['index','store','create','edit','update','destroy']]);
  
    
    
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if  (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
            $hwadths = hwadth::orderBy('month', 'desc')->get(); 

        }elseif (Auth::user()->roles_name  === ["stat-doh"]){
                  $hwadths = hwadth::where('moh_id', Auth::user()->mohcode)->
                  orderBy('month', 'desc')->get();
  
        }else{
            $hwadths = hwadth::where('fck_id', Auth::user()->fckid)->get();

        }
        return view('hwadths.index', compact('hwadths'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $hwadths = hwadth::all();
   return view('hwadths.create',compact('hwadths')); // عرض صفحة الإنشاء
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
            // التحقق من صحة الإدخالات لجميع الحقول
            $validatedData = $request->validate([
                'month'   => 'required',
                'year'    => 'required',
                'livebnm'   => 'required|integer|min:0', // ذكور الولادات الحية الطبيعية
                'livebnf'   => 'required|integer|min:0', // إناث الولادات الحية الطبيعية
                'livebnkh'  => 'required|integer|min:0', // خنثى الولادات الحية الطبيعية
                'livebnt' => 'required|integer|min:0',
                'livebsm'   => 'required|integer|min:0', // ذكور الولادات الحية القيصرية
                'livebsf'   => 'required|integer|min:0', // إناث الولادات الحية القيصرية
                'livebskh'  => 'required|integer|min:0', // خنثى الولادات الحية القيصرية
                'bdeadnm'   => 'required|integer|min:0', // ذكور الولادات الميتة الطبيعية
                'bdeadnf'   => 'required|integer|min:0', // إناث الولادات الميتة الطبيعية
                'bdeadnkh'  => 'required|integer|min:0', // خنثى الولادات الميتة الطبيعية
                'bdeadsm'   => 'required|integer|min:0', // ذكور الولادات الميتة القيصرية
                'bdeadsf'   => 'required|integer|min:0', // إناث الولادات الميتة القيصرية
                'bdeadskh'  => 'required|integer|min:0', // خنثى الولادات الميتة القيصرية
                'deadlm'    => 'required|integer|min:0', // ذكور الوفيات أقل من سنة
                'deadlf'    => 'required|integer|min:0', // إناث الوفيات أقل من سنة
                'deadlkh'   => 'required|integer|min:0', // خنثى الوفيات أقل من سنة
                'deadmm'    => 'required|integer|min:0', // ذكور الوفيات أكثر من سنة
                'deadmf'    => 'required|integer|min:0', // إناث الوفيات أكثر من سنة
                'deadmkh'   => 'required|integer|min:0', // خنثى الوفيات أكثر من سنة
                'totaldm'   => 'required|integer|min:0', // الإجمالي الكلي للذكور
                'totaldf'   => 'required|integer|min:0', // الإجمالي الكلي للإناث
                'totaldkh'  => 'required|integer|min:0', // الإجمالي الكلي للخنثى
                'totaldt'   => 'required|integer|min:0', // الإجمالي الكلي
                'mdeadf'    => 'required|integer|min:0', // إناث وفيات الأمهات
                'mdeadt'    => 'required|integer|min:0',
                'deadtb'    => 'required|integer|min:0',
                 // إجمالي وفيات الأمهات
            ], [
                // رسائل تحقق مخصصة
               
                'integer'  => 'يجب أن تكون القيمة رقمًا صحيحًا.',
                'min'      => 'القيمة يجب أن تكون صفر أو أكثر.',
            ]);
    
            // التحقق من التكرار لنفس الشهر والسنة والمؤسسة
            $existingRecord = hwadth::where('month', $request->month)
                ->where('year', $request->year)
                ->where('fck_id', Auth::user()->fckid)
                ->first();
    
            if ($existingRecord) {
                return redirect()->back()->withErrors(['error' => 'تم تسجيل هذه البيانات مسبقًا لهذا الشهر والسنة.']);
            }
    
            // حفظ البيانات
            hwadth::create([
                'month'          => $validatedData['month'],
                'year'           => $validatedData['year'],
                'livebnm'   => $request->livebnm,
                'livebnf'   => $request->livebnf,
                'livebnkh'  => $request->livebnkh,
                'livebnt'   => $request->livebnt,
                'livebsm'   => $request->livebsm,               
                'livebsf'   => $request->livebsf,
                'livebskh'  => $request->livebskh,
                'livebst'   => $request->livebst,
                'totalbm'   => $request->totalbm,
                'totalbf'   => $request->totalbf,
                'totalbkh'  => $request->totalbkh,
                'totalbt'   => $request->totalbt,
                'bdeadnm'   => $request->bdeadnm,
                'bdeadnf'   => $request->bdeadnf,
                'bdeadnkh'  => $request->bdeadnkh,
                'bdeadnt'  => $request->bdeadnt,
                'bdeadsm'   => $request->bdeadsm,
                'bdeadsf'   => $request->bdeadsf,
                'bdeadskh'  => $request->bdeadskh,
                'bdeadst'  => $request->bdeadst,
                'deadlm'    => $request->deadlm,
                'deadlf'    => $request->deadlf,
                'deadlkh'   => $request->deadlkh,
                'deadlt'   => $request->deadlt,
                'deadmm'    => $request->deadmm,
                'deadmf'    => $request->deadmf,
                'deadmkh'   => $request->deadmkh,
                'deadmt'   => $request->deadmt,
                'totaldm'   => $request->totaldm,
                'totaldf'   => $request->totaldf,
                'totaldkh'  => $request->totaldkh,
                'totaldt'   => $request->totaldt,
                'mdeadm'    => 0,
                'mdeadf'    => $request->mdeadf,
                'mdeadkh'   => 0,
                'mdeadt'    => $request->mdeadt,
                'deadtb'    => $request->deadtb,
                'month'     => $request->month, // الشهر
                'year'      => $request->year,  // السنة
                'fck_id'    => Auth::user()->fckid,
                'fctypesid'      => Auth::user()->fcktid,
                'save'           => 0,
                'Created_by'     => Auth::user()->email,

                'moh_id'    => Auth::user()->mohcode,
                'creator'   => Auth::user()->email,
            ]);
    
            // إعادة توجيه مع رسالة نجاح
            session()->flash('Add', 'تم حفظ البيانات بنجاح!');
            return redirect('/hwadths');
        }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\hwadth  $hwadth
     * @return \Illuminate\Http\Response
     */
    public function show(hwadth $hwadth)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\hwadth  $hwadth
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $hwadths = hwadth::find($id);
    
        if (!$hwadths) {
            return redirect()->route('hwadths.index')->withErrors(['error' => 'السجل المطلوب غير موجود.']);
        }
    
        return view('hwadths.edit', compact('hwadths'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\hwadth  $hwadth
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // التحقق من صحة البيانات المُرسلة
        $validatedData = $request->validate([
            'livebnm' => 'nullable|integer',
            'livebnf' => 'nullable|integer',
            'livebnkh' => 'nullable|integer',
            'livebnt' => 'nullable|integer',
            'livebsm' => 'nullable|integer',
            'livebsf' => 'nullable|integer',
            'livebskh' => 'nullable|integer',
            'livebst' => 'nullable|integer',
            'totalbm' => 'nullable|integer',
            'totalbf' => 'nullable|integer',
            'totalbkh' => 'nullable|integer',
            'totalbt' => 'nullable|integer',
            'bdeadnm' => 'nullable|integer',
            'bdeadnf' => 'nullable|integer',
            'bdeadnkh' => 'nullable|integer',
            'bdeadnt' => 'nullable|integer',
            'bdeadsm' => 'nullable|integer',
            'bdeadsf' => 'nullable|integer',
            'bdeadskh' => 'nullable|integer',
            'bdeadst' => 'nullable|integer',
            'deadlm' => 'nullable|integer',
            'deadlf' => 'nullable|integer',
            'deadlkh' => 'nullable|integer',
            'deadlt' => 'nullable|integer',
            'deadmm' => 'nullable|integer',
            'deadmf' => 'nullable|integer',
            'deadmkh' => 'nullable|integer',
            'deadmt' => 'nullable|integer',
            'totaldm' => 'nullable|integer',
            'totaldf' => 'nullable|integer',
            'totaldkh' => 'nullable|integer',
            'totaldt' => 'nullable|integer',
            'mdeadm' => 'nullable|integer',
            'mdeadf' => 'nullable|integer',
            'mdeadkh' => 'nullable|integer',
            'mdeadt' => 'nullable|integer',
            'deadtb' => 'nullable|integer',
            'month' => 'nullable|integer',
            'year' => 'nullable|integer',
            'fck_id' => 'nullable|integer',
            'fctypesid' => 'nullable|integer',
            'save' => 'nullable|boolean',
            'Created_by' => 'nullable|string|max:255',
            'moh_id' => 'nullable|integer',
            'creator' => 'nullable|string|max:255',
        ]);

        // العثور على السجل وتحديثه
        $hwadth = hwadth::findOrFail($request->id);

        if (!$hwadth) {
            return redirect()->back()->withErrors(['msg' => 'السجل غير موجود']);
        }

        $hwadth->update([
            'livebnm' => $request->livebnm,
            'livebnf' => $request->livebnf,
            'livebnkh' => $request->livebnkh,
            'livebnt' => $request->livebnt,
            'livebsm' => $request->livebsm,
            'livebsf' => $request->livebsf,
            'livebskh' => $request->livebskh,
            'livebst' => $request->livebst,
            'totalbm' => $request->totalbm,
            'totalbf' => $request->totalbf,
            'totalbkh' => $request->totalbkh,
            'totalbt' => $request->totalbt,
            'bdeadnm' => $request->bdeadnm,
            'bdeadnf' => $request->bdeadnf,
            'bdeadnkh' => $request->bdeadnkh,
            'bdeadnt' => $request->bdeadnt,
            'bdeadsm' => $request->bdeadsm,
            'bdeadsf' => $request->bdeadsf,
            'bdeadskh' => $request->bdeadskh,
            'bdeadst' => $request->bdeadst,
            'deadlm' => $request->deadlm,
            'deadlf' => $request->deadlf,
            'deadlkh' => $request->deadlkh,
            'deadlt' => $request->deadlt,
            'deadmm' => $request->deadmm,
            'deadmf' => $request->deadmf,
            'deadmkh' => $request->deadmkh,
            'deadmt' => $request->deadmt,
            'totaldm' => $request->totaldm,
            'totaldf' => $request->totaldf,
            'totaldkh' => $request->totaldkh,
            'totaldt' => $request->totaldt,
            'mdeadm' => $request->mdeadm,
            'mdeadf' => $request->mdeadf,
            'mdeadkh' => $request->mdeadkh,
            'mdeadt' => $request->mdeadt,
            'deadtb' => $request->deadtb,
            'month' => $request->month,
            'year' => $request->year,
            'fck_id' => Auth::user()->fckid,
            'fctypesid' => Auth::user()->fcktid,
            'save' => 0,
            'Created_by' => Auth::user()->email,
            'moh_id' => Auth::user()->mohcode,
            'creator' => Auth::user()->email,
        ]);

        session()->flash('edit', 'تم تحديث البيانات بنجاح');
        return redirect('/hwadths');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\hwadth  $hwadth
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $record = hwadth::find($request->invoice_id);

        if (!$record) {
            return redirect()->back()->withErrors(['error' => 'السجل المطلوب غير موجود.']);
        }
    
        $record->delete(); // حذف السجل إذا كان موجودًا
        return redirect()->back()->with('success', 'تم حذف السجل بنجاح.');
    
      
    }
}
