<?php

namespace App\Http\Controllers;

use App\Models\pations;
use App\Models\rdhs;
use App\Models\salsurs;
use App\Models\fck;
use App\Models\mohs;
use App\Models\salat;
use App\Models\surg;
use App\Models\surgery;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $currentDate = Carbon::now();
        $lastMonth = $currentDate->subMonth()->month;
        $year = $currentDate->year;
        
        $month = $request->input('month', $lastMonth);
        $year = $request->input('year', $year);
        $monthsArabic = [
            1 => 'كانون الثاني',
            2 => 'شباط',
            3 => 'آذار',
            4 => 'نيسان',
            5 => 'أيار',
            6 => 'حزيران',
            7 => 'تموز',
            8 => 'آب',
            9 => 'أيلول',
            10 => 'تشرين الأول',
            11 => 'تشرين الثاني',
            12 => 'كانون الأول',
        ];
    
        // تحويل رقم الشهر إلى اسم الشهر
        $monthName = $monthsArabic[$month];
    
        $mohcodes = explode(',', Auth::user()->mohcode);
    
       // تحديد الدور والمؤسسات بناءً على المستخدم
if (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
    // جمع البيانات على مستوى العراق
    $conditions = ['month' => $month, 'year' => $year];
    $khasa = Surgery::where($conditions)->sum('khasa');
    $fkubra = Surgery::where($conditions)->sum('fkubra');
    $kubra = Surgery::where($conditions)->sum('kubra');
    $mtws = Surgery::where($conditions)->sum('mtws');
    $sugra = Surgery::where($conditions)->sum('sugra');
    $salat = Salat::where($conditions)->sum('bsalnum');
} else {
    // جمع البيانات بناءً على المؤسسات المرتبطة بالمستخدم
    $conditions = ['month' => $month, 'year' => $year];
    
    if (!empty($mohcodes) && count($mohcodes) > 1) {
        // جمع البيانات عند وجود أكثر من مؤسسة
        $conditions['moh_id'] = $mohcodes;
        $khasa = Surgery::where($conditions)->where('moh_id', Auth::user()->mohcode)->sum('khasa');
        $fkubra = Surgery::where($conditions)->where('moh_id', Auth::user()->mohcode)->sum('fkubra');
        $kubra = Surgery::where($conditions)->where('moh_id', Auth::user()->mohcode)->sum('kubra');
        $mtws = Surgery::where($conditions)->where('moh_id', Auth::user()->mohcode)->sum('mtws');
        $sugra = Surgery::where($conditions)->where('moh_id', Auth::user()->mohcode)->sum('sugra');
        $salat = Salat::where($conditions)->where('moh_id', Auth::user()->mohcode)->sum('bsalnum');
    } elseif (!empty($mohcodes) && count($mohcodes) === 1) {
        // جمع البيانات عند وجود مؤسسة واحدة
        $orgId = $mohcodes[0];
        $conditions['moh_id'] = $orgId;
        $khasa = Surgery::where($conditions)->where('fck_id', Auth::user()->fckid)->sum('khasa');
        $fkubra = Surgery::where($conditions)->where('fck_id', Auth::user()->fckid)->sum('fkubra');
        $kubra = Surgery::where($conditions)->where('fck_id', Auth::user()->fckid)->sum('kubra');
        $mtws = Surgery::where($conditions)->where('fck_id', Auth::user()->fckid)->sum('mtws');
        $sugra = Surgery::where($conditions)->where('fck_id', Auth::user()->fckid)->sum('sugra');
        $salat = Salat::where($conditions)->where('fck_id', Auth::user()->fckid)->sum('bsalnum');
    } else {
        // إذا لم تكن هناك مؤسسات مرتبطة
        $khasa = $fkubra = $kubra = $mtws = $sugra = $salat = 0;
    }
}
        
        // إعداد البيانات للرسوم البيانية
        $spebed_values1 = pations::distinct()->pluck('spebed');
        $chart_data1 = [];
        $labels1 = [];
        foreach ($spebed_values1 as $spebed) {
            if  (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
                // إذا كان المستخدم Admin
                $count_invoices1 = pations::where('spebed', $spebed)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->whereNotNull('bedm') // استبعاد القيم الفارغة
                    ->where('bedm', '!=', 0) // استبعاد القيم التي تساوي صفر
                    ->sum('bedm');
            } elseif (Auth::user()->roles_name === ["stat-doh"]) {
                // إذا كان هناك أكثر من مؤسسة
                $count_invoices1 = pations::where('spebed', $spebed)
                    ->where('moh_id', Auth::user()->mohcode)
                    ->whereIn('moh_id', $mohcodes)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->sum('bedm');
            } else {
                // إذا كانت مؤسسة واحدة أو لا توجد مؤسسات
                $orgId = !empty($mohcodes) && count($mohcodes) === 1 ? $mohcodes[0] : null;
                $count_invoices1 = $orgId 
                    ? pations::where('spebed', $spebed)
                        ->where('fck_id', Auth::user()->fckid)
                        ->where('moh_id', $orgId)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->sum('bedm')
                    : 0;
            }
        
            // إضافة النتائج إلى البيانات
            $chart_data1[] = $count_invoices1;
            $label1 = rdhs::where('id', $spebed)->pluck('Spcuname')->first();
            $labels1[] = $label1;
        }
        $spebed_values2 = salat::distinct()->pluck('salid');
        $chart_data2 = [];
        $labels2 = [];
        
        foreach ($spebed_values2 as $salid) {
            if  (in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name)) {
                // إذا كان المستخدم Admin
                $count_invoices2 = salat::where('salid', $salid)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->sum('bsalnum');
                }elseif (Auth::user()->roles_name  === ["stat-doh"]){
                // إذا كان هناك أكثر من مؤسسة
                $count_invoices2 = salat::where('salid', $salid)
                ->where('moh_id', Auth::user()->mohcode)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->sum('bsalnum');
            } else  {
                // إذا كانت مؤسسة واحدة
                $count_invoices2 = salat::where('salid', $salid)
                    ->where('fck_id', Auth::user()->fckid)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->sum('bsalnum');
            } 
        
            $chart_data2[] = $count_invoices2;
            $label2 = salsurs::where('id', $salid)->pluck('salsur')->first();
            $labels2[] = $label2;
        }
        
        $chartjs1 = app()->chartjs
            ->name('lineChartTest1')
            ->type('bar')
            ->size(['width' => 400, 'height' => 200])
            ->labels($labels1)
            ->datasets([
                [
                    "label" => "عدد الاسرة الكلية",
                    'backgroundColor' => [
                        "rgb(41, 117, 218)",
                        "rgb(23, 37, 54)",
                        "rgb(135, 218, 41)",
                        "rgb(218, 41, 159)",
                        "rgb(110, 0, 124)",
                        "rgb(17, 230, 212)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(3, 10, 19)",
                        "rgb(255, 99, 132)",
                        "rgb(199, 54, 158)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(120, 168, 31)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(4, 131, 99)",
                        "rgb(149, 184, 229)",
                        "rgb(125, 117, 236)",
                        "rgb(29, 2, 21)",
                        "rgb(149, 184, 229)",
                        "rgb(36, 2, 44)",
                        "rgb(226, 190, 216)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(61, 12, 194)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(74, 4, 83)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(233, 130, 13)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(88, 46, 7)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(80, 5, 5)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(63, 4, 4)",
                        "rgb(149, 184, 229)"
                    ],
                    'data' => $chart_data1,
                ],
            ]);
        
        $chartjs2 = app()->chartjs
            ->name('lineChartTest2')
            ->type('doughnut')
            ->size(['width' => 400, 'height' => 200])
            ->labels($labels2)
            ->datasets([
                [
                    "label" => "عدد اسرة الصالات",
                    'backgroundColor' => [
                        "rgb(41, 117, 218)",
                        "rgb(23, 37, 54)",
                        "rgb(135, 218, 41)",
                        "rgb(218, 41, 159)",
                        "rgb(110, 0, 124)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)",
                        "rgb(255, 99, 132)",
                        "rgb(146, 115, 137)",
                        "rgb(149, 184, 229)"
                    ],
                    'data' => $chart_data2,
                ],
            ]);
        
    
        return view('home', compact('chartjs1', 'chartjs2', 'fkubra', 'khasa', 'kubra', 'mtws', 'sugra', 'salat','monthName','year'));
    }
    
    
}
