<?php

namespace App\Http\Controllers;

use App\Imports\ServiceSpecializationImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ServiceSpecializationImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new ServiceSpecializationImport, $request->file('file'));

            return redirect()->back()->with('success', 'تم استيراد البيانات بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء استيراد البيانات: ' . $e->getMessage());
        }
    }
}
