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
use Yajra\DataTables\Facades\DataTables;
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
        $fck = fck::all();
        $surg = surg::all();
        $rdhs = rdhs::all();
        $mohs = mohs::all();
        return view('pations.pations', compact('rdhs', 'mohs', 'surg', 'fck'));
 
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
    public function store(Request $request) {
        // التحقق من صحة الإدخالات
        $validatedData = $request->validate([
            'month'   => 'required',
            'year'    => 'required',
            'rdhs.*'  => 'required',
            'units.*' => 'required',
            'beds.*'  => 'required',
            'paout.*' => 'required',
            'past.*'  => 'required',
            'mkoth.*' => 'required',
            'bde.*'   => 'required',
        ] , [
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
    
        // التحقق من وجود بيانات مسبقة لنفس الشهر والمؤسسة لكل إدخال
        foreach ($validatedData['rdhs'] as $index => $rdhId) {
            $existingRecord = pations::where('month', $validatedData['month'])
                ->where('year', $validatedData['year'])
                ->where('spebed', $rdhId)
                ->where('fck_id', Auth::user()->fckid)
                ->first();
    
            if ($existingRecord) {
                return redirect()->back()->withErrors(['error' => 'تم تسجيل هذه البيانات بالفعل لهذا الشهر ونوع السرير: ' . $rdhId]);
            }
        }
    
        // جلب معلومات المؤسسة
        $fck = fck::where('moh_id', Auth::user()->mohcode)
            ->where('id', Auth::user()->fckid)
            ->first();
        $hfcode = $fck ? $fck->hfcode : null;
    
        // حفظ البيانات المدخلة
        foreach ($validatedData['rdhs'] as $index => $rdhId) {
            pations::create([
                'month'          => $validatedData['month'],
                'year'           => $validatedData['year'],
                'spebed'         => $rdhId,
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
            ]);
        }
    
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
        $pations = pations::findOrFail($request->id);
        $pations->update([
           
            'month'     =>$request->month,
            'year'      => $request->year,
            'spebed'      => $request->rdhs,
            'unitnum'      => $request->unit,
            'bedm'      => $request->beds,
            'outpationmon'     => $request->paout,
            'stayoutpation'      => $request->past,
            'mkoth'     =>$request->mkoth,
            'death'       => $request->bde, 
           
           
            'Approv_user'=> Auth::user()->email,
            'note'=> $request->note ,
            'save'=> 0,
            'status_value'     => 2,
            'status'     => 'غير موثقة',
            'fapprove'     => 'غير موثقة',
        ]);
        session()->flash('edit', 'تم إضافة البيانات بنجاح');
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
        if ($pations) {
            $pations->delete();
            session()->flash('delete_invoice', 'تم حذف السجل بنجاح');
        } else {
            session()->flash('error', 'السجل غير موجود أو تم حذفه مسبقاً');
        }
        return redirect('/pations');
    }

public function pations_approve()
{
    $fck = fck::all();
    $surg = surg::all();
    $rdhs = rdhs::all();
    $mohs = mohs::all();

    // Query approved pations based on user role
    if (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
        $pations = pations::with(['moh', 'fck', 'rdhs'])->where('status_value', 1)->orderBy('month', 'desc')->get();
    } elseif (Auth::user()->roles_name === ["stat-doh"]) {
        $pations = pations::with(['moh', 'fck', 'rdhs'])->where('moh_id', Auth::user()->mohcode)->where('status_value', 1)->orderBy('month', 'desc')->get();
    } else {
        $pations = pations::with(['moh', 'fck', 'rdhs'])->where('fck_id', Auth::user()->fckid)->where('status_value', 1)->orderBy('month', 'desc')->get();
    }

    return view('pations.pations_approve', compact('rdhs', 'mohs', 'surg', 'fck', 'pations'));
}

public function pations_nonapprove()
{
    $fck = fck::all();
    $surg = surg::all();
    $rdhs = rdhs::all();
    $mohs = mohs::all();

    // Query non-approved pations based on user role
    if (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
        $pations = pations::with(['moh', 'fck', 'rdhs'])->where('status_value', 2)->orderBy('month', 'desc')->get();
    } elseif (Auth::user()->roles_name === ["stat-doh"]) {
        $pations = pations::with(['moh', 'fck', 'rdhs'])->where('moh_id', Auth::user()->mohcode)->where('status_value', 2)->orderBy('month', 'desc')->get();
    } else {
        $pations = pations::with(['moh', 'fck', 'rdhs'])->where('fck_id', Auth::user()->fckid)->where('status_value', 2)->orderBy('month', 'desc')->get();
    }
    
    return view('pations.pations_nonapprove', compact('rdhs', 'mohs', 'surg', 'fck', 'pations'));
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
            'status' => $request->Status,
            'Approv_dt' =>now(),
            'note'=> $request->note,
            'Approv_user'=>Auth::user()->email,

        ]);

      
    }

    else {
        $pations->update([
            'status_value' => 2,
            'status' => $request->Status,
            'Approv_dt' =>now(),
            'note'=> $request->note,
            'Approv_user'=>Auth::user()->email,
        ]);
      
    }
    session()->flash('Status_Update');
    if ($pations->status_value == 1) {
        return redirect('/pations_approve');
    } else {
        return redirect('/pations_nonapprove');
    }

}public function print_pations(Request $request)
{
    $fck = fck::all();
    $surg = surg::all(); 
    $rdhs = rdhs::all(); 
    $mohs = mohs::all(); 

    // Initialize empty collections
    $surgery = collect([]);
    $salat = collect([]);
    $pations = collect([]);
    $hwadth = null;
    $cases = null;

    // Only query if month and year are provided
    if($request->filled('month') && $request->filled('year')) {
        $surgery = surgery::where('fck_id', Auth::user()->fckid)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->get();

        $salat = salat::where('fck_id', Auth::user()->fckid)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->get();

        $pations = pations::where('fck_id', Auth::user()->fckid)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->get();

        $hwadth = hwadth::where('fck_id', Auth::user()->fckid)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->first();

        $cases = \App\Models\cases::where('fck_id', Auth::user()->fckid)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->first();
    }

    return view('report', compact('rdhs','pations','mohs','surg','fck','surgery','salat','hwadth','cases'));
}
public function getPationsNonApproveData(Request $request)
{
    if (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
        $query = pations::with(['moh', 'fck', 'rdhs'])->where('status_value', 2)->orderBy('month', 'desc');
    } elseif (Auth::user()->roles_name === ["stat-doh"]) {
        $query = Pations::with(['moh', 'fck', 'rdhs'])->where('moh_id', Auth::user()->mohcode)->where('status_value', 2)->orderBy('month', 'desc');
    } else {
        $query = Pations::with(['moh', 'fck', 'rdhs'])->where('fck_id', Auth::user()->fckid)->where('status_value', 2)->orderBy('month', 'desc');
    }

    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('moh_name', function ($pation) {
            return $pation->moh ? $pation->moh->mohname : 'No Section';
        })
        ->addColumn('fck_name', function ($pation) {
            return $pation->fck ? $pation->fck->Fckname : 'No Section';
        })
        ->addColumn('rdhs_name', function ($pation) {
            return $pation->rdhs ? $pation->rdhs->Spcuname : 'No Section';
        })
        ->addColumn('month_name', function ($pation) {
            $months = [
                1 => 'كانون الثاني',
                2 => 'شباط',
                3 => 'اذار',
                4 => 'نيسان',
                5 => 'ايار',
                6 => 'حزيران',
                7 => 'تموز',
                8 => 'أب',
                9 => 'ايلول',
                10 => 'تشرين الاول',
                11 => 'تشرين الثاني',
                12 => 'كانون الاول',
            ];
            return $months[$pation->month] ?? '';
        })
        ->addColumn('status_badge', function ($pation) {
            if ($pation->status_value == 1) {
                return '<span class="badge badge-pill badge-success">' . $pation->status . '</span>';
            } elseif ($pation->status_value == 2) {
                return '<span class="badge badge-pill badge-danger">' . $pation->status . '</span>';
            } else {
                return '<span class="badge badge-pill badge-warning">' . $pation->status . '</span>';
            }
        })
        ->addColumn('actions', function ($pation) {
            $actions = '';
            if (Auth::user()->can('توثيق')) {
                $actions .= '<a class="dropdown-item" href="' . URL::route('status_show', [$pation->id]) . '"><i class="text-success fas fa-money-bill"></i>&nbsp;&nbsp;تغيير حالة الاستمارة</a>';
            }
            return '<div class="dropdown">
                        <button aria-expanded="false" aria-haspopup="true" class="btn ripple btn-primary btn-sm" data-toggle="dropdown" type="button">توثيق<i class="fas fa-caret-down ml-1"></i></button>
                        <div class="dropdown-menu tx-13">' . $actions . '</div>
                    </div>';
        })
        ->rawColumns(['status_badge', 'actions'])
        ->make(true);
}

public function getPationsApproveData(Request $request)
{
    if (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
        $query = pations::with(['moh', 'fck', 'rdhs'])->where('status_value', 1)->orderBy('month', 'desc');
    } elseif (Auth::user()->roles_name === ["stat-doh"]) {
        $query = Pations::with(['moh', 'fck', 'rdhs'])->where('moh_id', Auth::user()->mohcode)->where('status_value', 1)->orderBy('month', 'desc');
    } else {
        $query = Pations::with(['moh', 'fck', 'rdhs'])->where('fck_id', Auth::user()->fckid)->where('status_value', 1)->orderBy('month', 'desc');
    }

    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('moh_name', function ($pation) {
            return $pation->moh ? $pation->moh->mohname : 'No Section';
        })
        ->addColumn('fck_name', function ($pation) {
            return $pation->fck ? $pation->fck->Fckname : 'No Section';
        })
        ->addColumn('rdhs_name', function ($pation) {
            return $pation->rdhs ? $pation->rdhs->Spcuname : 'No Section';
        })
        ->addColumn('month_name', function ($pation) {
            $months = [
                1 => 'كانون الثاني',
                2 => 'شباط',
                3 => 'اذار',
                4 => 'نيسان',
                5 => 'ايار',
                6 => 'حزيران',
                7 => 'تموز',
                8 => 'أب',
                9 => 'ايلول',
                10 => 'تشرين الاول',
                11 => 'تشرين الثاني',
                12 => 'كانون الاول',
            ];
            return $months[$pation->month] ?? '';
        })
        ->addColumn('status_badge', function ($pation) {
            if ($pation->status_value == 1) {
                return '<span class="badge badge-pill badge-success">' . $pation->status . '</span>';
            } elseif ($pation->status_value == 2) {
                return '<span class="badge badge-pill badge-danger">' . $pation->status . '</span>';
            } else {
                return '<span class="badge badge-pill badge-warning">' . $pation->status . '</span>';
            }
        })
        ->addColumn('actions', function ($pation) {
            $actions = '';
            if (Auth::user()->can('تعديل السرير')) {
                $actions .= '<a class="dropdown-item" href="' . url('edit_pations') . '/' . $pation->id . '">تعديل</a>';
            }
            if (Auth::user()->can('حذف')) {
                $actions .= '<a class="dropdown-item" href="#" data-invoice_id="' . $pation->id . '" data-toggle="modal" data-target="#delete_invoice"><i class="text-danger fas fa-trash-alt"></i>&nbsp;&nbsp;حذف</a>';
            }
            if (!empty($actions)) {
                return '<div class="dropdown">
                            <button aria-expanded="false" aria-haspopup="true" class="btn ripple btn-primary btn-sm" data-toggle="dropdown" type="button">المهام<i class="fas fa-caret-down ml-1"></i></button>
                            <div class="dropdown-menu tx-13">' . $actions . '</div>
                        </div>';
            }
            return '';
        })
        ->rawColumns(['status_badge', 'actions'])
        ->make(true);
}

public function getPationsData(Request $request)
{
    if (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
        $query = pations::with(['moh', 'fck', 'rdhs'])->orderBy('month', 'desc');
    } elseif (Auth::user()->roles_name === ["stat-doh"]) {
        $query = Pations::with(['moh', 'fck', 'rdhs'])->where('moh_id', Auth::user()->mohcode)->orderBy('month', 'desc');
    } else {
        $query = Pations::with(['moh', 'fck', 'rdhs'])->where('fck_id', Auth::user()->fckid)->orderBy('month', 'desc');
    }

    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('moh_name', function ($pation) {
            return $pation->moh ? $pation->moh->mohname : 'No Section';
        })
        ->addColumn('fck_name', function ($pation) {
            return $pation->fck ? $pation->fck->Fckname : 'No Section';
        })
        ->addColumn('rdhs_name', function ($pation) {
            return $pation->rdhs ? $pation->rdhs->Spcuname : 'No Section';
        })
        ->addColumn('month_name', function ($pation) {
            $months = [
                1 => 'كانون الثاني',
                2 => 'شباط',
                3 => 'اذار',
                4 => 'نيسان',
                5 => 'ايار',
                6 => 'حزيران',
                7 => 'تموز',
                8 => 'أب',
                9 => 'ايلول',
                10 => 'تشرين الاول',
                11 => 'تشرين الثاني',
                12 => 'كانون الاول',
            ];
            return $months[$pation->month] ?? '';
        })
        ->addColumn('status_badge', function ($pation) {
            if ($pation->status_value == 1) {
                return '<span class="badge badge-pill badge-success">' . $pation->status . '</span>';
            } elseif ($pation->status_value == 2) {
                return '<span class="badge badge-pill badge-danger">' . $pation->status . '</span>';
            } else {
                return '<span class="badge badge-pill badge-warning">' . $pation->status . '</span>';
            }
        })
        ->addColumn('actions', function ($pation) {
            $actions = '';
            if (Auth::user()->can('تعديل السرير')) {
                $actions .= '<a class="dropdown-item" href="' . url('edit_pations') . '/' . $pation->id . '">تعديل</a>';
            }
            if (Auth::user()->can('حذف')) {
                $actions .= '<a class="dropdown-item" href="#" data-invoice_id="' . $pation->id . '" data-toggle="modal" data-target="#delete_invoice"><i class="text-danger fas fa-trash-alt"></i>&nbsp;&nbsp;حذف</a>';
            }
            if (!empty($actions)) {
                return '<div class="dropdown">
                            <button aria-expanded="false" aria-haspopup="true" class="btn ripple btn-primary btn-sm" data-toggle="dropdown" type="button">المهام<i class="fas fa-caret-down ml-1"></i></button>
                            <div class="dropdown-menu tx-13">' . $actions . '</div>
                        </div>';
            }
            return '';
        })
        ->rawColumns(['status_badge', 'actions'])
        ->make(true);
}
}