<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\cases;
use App\Models\mohs;
use Illuminate\Support\Facades\Auth;

use App\Models\fck;
use App\Models\pations;

class CaseController extends Controller
{
     function __construct()
    {
    $this->middleware('permission:استشارية', ['only' => ['index','store','create','edit','update','destroy']]);
  
    
    
    }
    // عرض جميع الحالات
    public function index()
    { $fck=fck::all();
        $mohs=mohs::all(); 
        if  (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
            $cases=cases::all(); 

        }elseif (Auth::user()->roles_name  === ["stat-doh"]){
                  $cases = cases::where('moh_id', Auth::user()->mohcode)->get();
  
        }else{
            $cases = cases::where('fck_id', Auth::user()->fckid)->get();

        }
        return view('cases.index', compact('cases','mohs','fck'));
    }

    // إنشاء حالة جديدة
    public function create(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
    
        // التحقق من توفر القيم قبل تنفيذ الاستعلام
            $calculatedTotal = pations::where('month', $month)
                ->where('year', $year)
                ->where('fck_id', Auth::user()->fckid)
                ->sum('bedm');
        
    
        return view('cases.create', compact('calculatedTotal', 'month', 'year'));
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'month'   => 'required|integer',
            'year'    => 'required|integer',
            'caseest' => 'required|integer|min:0',
            'casekhaf' => 'required|integer|min:0',
            'caseesemer' => 'required|integer|min:0',
            'cheoutpationsan' => 'required|integer|min:0',
            'cheoutpationlab' => 'required|integer|min:0',
            'cheinpationsan' => 'required|integer|min:0',
            'cheinpationlab' => 'required|integer|min:0',
            'bedssav' => 'required|integer|min:0',
            'otherbed' => 'required|integer|min:0',
            'bedest' => 'required|integer|min:0',
            'bedrdhclose' => 'required|integer|min:0',
            'totalbed' => 'required|integer|min:0',
            'tbedsf' => 'required|integer|min:0',
        ]);
    
        // حساب مجموع الأسرة قبل التخزين
        $tbedsmoh = Pations::where('month', $validatedData['month'])
            ->where('year', $validatedData['year'])
            ->where('fck_id', Auth::user()->fckid)
            ->sum('bedm');
    
        // حفظ السجل في قاعدة البيانات
        Cases::create([
            'month'          => $validatedData['month'],
            'year'           => $validatedData['year'],
            'fck_id'         => Auth::user()->fckid,
            'moh_id'         => Auth::user()->mohcode,
            'fctypesid'      => Auth::user()->fcktid,
            'caseest' => $validatedData['caseest'],
            'casekhaf' => $validatedData['casekhaf'],
            'caseesemer' => $validatedData['caseesemer'],
            'cheoutpationsan' => $validatedData['cheoutpationsan'],
            'cheoutpationlab' => $validatedData['cheoutpationlab'],
            'cheinpationsan' => $validatedData['cheinpationsan'],
            'cheinpationlab' => $validatedData['cheinpationlab'],
            'bedssav' => $validatedData['bedssav'],
            'otherbed' => $validatedData['otherbed'],
            'bedest' => $validatedData['bedest'],
            'bedrdhclose' => $validatedData['bedrdhclose'],
            'tbedsmoh' => $tbedsmoh, // القيمة المحسوبة تلقائيًا
            'totalbed' => $validatedData['totalbed'],
            'tbedsf' => $validatedData['tbedsf'],
            'creator' => Auth::user()->email,
            'Created_by' => Auth::user()->email,
        ]);
    
        session()->flash('success', 'تم حفظ البيانات بنجاح!');
        return redirect('/cases');    
    }
public function edit($id)
{
    $case = cases::findOrFail($id);
    return view('cases.edit', compact('case'));
}

 // تحديث بيانات الحالة
 public function update(Request $request, $id)
 {
     $case = cases::findOrFail($id);

     $validatedData = $request->validate([
        'month'   => 'required',
        'year'    => 'required',
        'caseest' => 'required|integer',
        'casekhaf' => 'required|integer',
        'caseesemer' => 'required|integer',
        'cheoutpationsan' => 'required|integer',
        'cheoutpationlab' => 'required|integer',
        'cheinpationsan' => 'required|integer',
        'cheinpationlab' => 'required|integer',
        'bedssav' => 'required|integer',
        'otherbed' => 'required|integer',
        'bedest' => 'required|integer',
        'bedrdhclose' => 'required|integer',
        'totalbed' => 'required|integer',
        'tbedsf' => 'required|integer',
     ]);

     $case->update($validatedData);

     return redirect()->route('cases.index')->with('success', 'تم تحديث الحالة بنجاح!');
 }

 // حذف الحالة من قاعدة البيانات
 public function destroy($id)
 {
     $case = cases::findOrFail($id);
     $case->delete();

     return redirect()->route('cases.index')->with('success', 'تم حذف الحالة بنجاح!');
 }
 public function getBedsTotal($month, $year)
{
    $fck_id = Auth::user()->fckid;

    // حساب مجموع الأسرة بناءً على الشهر والسنة
    $totalBeds = Pations::where('month', $month)
        ->where('year', $year)
        ->where('fck_id', $fck_id)
        ->sum('bedm');

    return response()->json(['totalBeds' => $totalBeds]);
}
}

