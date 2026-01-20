<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Record;
use App\Models\Fctypes;
use App\Models\fck;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{
    function __construct()
    {
    $this->middleware('permission:المرفقات', ['only' => ['index','filterInstitutions','store','download','delete']]);
    $this->middleware('permission:جدول الاستمارات المرفوعة', ['only' => ['recordindex']]);
    
    
    
    }
    // عرض النموذج الرئيسي
    public function index()
    {
        $fctypes = Fctypes::all();
        return view('record', compact('fctypes'));
    }
    public function recordindex()
    {
        $records = Record::with('fck')->get(); // جلب السجلات مع العلاقات
        return view('recordindex', compact('records')); 
    }
    // جلب المؤسسات بناءً على نوع المؤسسة
    public function filterInstitutions(Request $request)
    {
        $user = Auth::user();
        $institutionType = $request->query('type');

        $query = fck::where('moh_id', $user->mohcode);
        if (!empty($institutionType)) {
            $query->where('fctypesid', $institutionType);
        }

        return response()->json($query->get());
    }

    // معالجة البيانات القادمة من النموذج
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fck' => 'required|exists:fcks,id',
            'institution_type' => 'required|exists:fctypes,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'attachment' => 'nullable|file|mimes:pdf|max:10240',
        ]);
    
        $filename = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
    
            $institution = Fck::find($validatedData['fck']);
            $institutionName = $institution->Fckname;
            $filename = $institutionName . '_' . $validatedData['month'] . '_' . $validatedData['year'] . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads', $filename, 'public');
        }
    
        try {
            Record::create([
                'fck_id' => $validatedData['fck'],
                'institution_id' => $validatedData['institution_type'],
                'month' => $validatedData['month'],
                'year' => $validatedData['year'],
                'attachment' => $filename,
            ]);
    
            session()->flash('success', 'تم تسجيل البيانات بنجاح!');
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء حفظ البيانات. حاول مرة أخرى.');
        }
    
        return redirect()->back();
    }public function download($file)
    {
         $filePath = storage_path('app/public/uploads/' . $file);
    
         if (file_exists($filePath)) {
             return response()->download($filePath);
         }
    
         return redirect()->back()->with('error', 'الملف غير موجود.');
    }
    public function delete($file)
{
    // تحديد المسار الكامل للملف
    $filePath = storage_path('app/public/uploads/' . $file);

    // التحقق من وجود الملف وحذفه
    if (file_exists($filePath)) {
        unlink($filePath); // حذف الملف من التخزين
    }

    // حذف السجل من قاعدة البيانات
    $record = Record::where('attachment', $file)->first();
    if ($record) {
        $record->delete(); // حذف السجل
        return redirect()->back()->with('success', 'تم حذف الملف وبياناته بنجاح.');
    }

    return redirect()->back()->with('error', 'الملف أو بياناته غير موجودة.');
}
public function viewFile($file)
{
    $filePath = storage_path('app/public/uploads/' . $file); // تم تعديل المسار هنا
    if (file_exists($filePath)) {
        return response()->file($filePath);
    } else {
        return redirect()->back()->with('error', 'الملف غير موجود.');
    }
}
}