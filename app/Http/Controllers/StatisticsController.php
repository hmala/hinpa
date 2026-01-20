<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\pations;
use App\Models\surgery;
use App\Models\salat;
use App\Models\hwadth;
use App\Models\mohs;
use App\Models\fck;
use App\Models\rdhs;
use App\Models\cases;
use App\Exports\StatisticsExport;
use Maatwebsite\Excel\Facades\Excel;

class StatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::check() && (empty(Auth::user()->roles_name) || Auth::user()->Status === 'غير مفعل')) {
                return redirect()->route('home');
            }
            return $next($request);
        });
        $this->middleware('permission:تقرير', ['only' => ['index', 'getComparisonData', 'getGeneralMonthlyComparison', 'getMohMonthlyComparison', 'getFckMonthlyComparison', 'getFctypeMonthlyComparison',
         'getMonthName', 'exportStatistics', 'generateFilename', 'getInstitutionsByMoh', 'getFckByMoh', 'getFctypeByFck']]);
    }

    public function index()
    {
        $currentUser = Auth::user();
        
        // تحديد البيانات المتاحة حسب صلاحيات المستخدم
        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            // الإدمن أو مستخدمي وزارة الصحة - يرون كل البيانات
            $mohs = mohs::all();
            $fcks = fck::all();
            $fctypes = fck::join('fctypes', 'fcks.fctypesid', '=', 'fctypes.id')
                          ->select('fctypes.id', 'fctypes.Fname')
                          ->distinct()
                          ->orderBy('fctypes.Fname')
                          ->get();
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            // مستخدمو الدوائر - يرون دائرتهم فقط
            $mohs = mohs::where('id', $currentUser->mohcode)->get();
            $fcks = fck::where('moh_id', $currentUser->mohcode)->get();
            $fctypes = fck::join('fctypes', 'fcks.fctypesid', '=', 'fctypes.id')
                          ->where('fcks.moh_id', $currentUser->mohcode)
                          ->select('fctypes.id', 'fctypes.Fname')
                          ->distinct()
                          ->orderBy('fctypes.Fname')
                          ->get();
        } else {
            // مستخدمو المؤسسات - يرون مؤسستهم فقط
            $mohs = mohs::where('id', $currentUser->mohcode)->get();
            $fcks = fck::where('id', $currentUser->fckid)->get();
            $fctypes = fck::join('fctypes', 'fcks.fctypesid', '=', 'fctypes.id')
                          ->where('fcks.id', $currentUser->fckid)
                          ->select('fctypes.id', 'fctypes.Fname')
                          ->distinct()
                          ->orderBy('fctypes.Fname')
                          ->get();
        }
        
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        // Use 2025 as default since 2025 might not have data yet
        $currentYear = 2025;
        
        return view('statistics.index', compact('mohs', 'fcks', 'fctypes', 'currentYear', 'currentMonth'));
    }
    
    public function getComparisonData(Request $request)
    {
        $currentUser = Auth::user();
        $mohId = $request->input('moh_id');
        $fckId = $request->input('fck_id');
        $fctypeId = $request->input('fctype_id');
        $year = $request->input('year', date('Y'));
        
        // تطبيق قيود الصلاحيات على البيانات المطلوبة
        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                // مستخدمو الدوائر - تقييد البيانات لدائرتهم فقط
                if ($mohId && $mohId != 'all' && $mohId != $currentUser->mohcode) {
                    return response()->json(['error' => 'غير مسموح لك بعرض بيانات دوائر أخرى'], 403);
                }
                if ($mohId === 'all') {
                    $mohId = $currentUser->mohcode;
                }
                
                // التحقق من المؤسسة
                if ($fckId && $fckId != 'all') {
                    $fckMohId = fck::where('id', $fckId)->value('moh_id');
                    if ($fckMohId != $currentUser->mohcode) {
                        return response()->json(['error' => 'غير مسموح لك بعرض بيانات مؤسسات دوائر أخرى'], 403);
                    }
                }
            } else {
                // مستخدمو المؤسسات - تقييد البيانات لمؤسستهم فقط
                if ($fckId && $fckId != 'all' && $fckId != $currentUser->fckid) {
                    return response()->json(['error' => 'غير مسموح لك بعرض بيانات مؤسسات أخرى'], 403);
                }
                if ($fckId === 'all') {
                    $fckId = $currentUser->fckid;
                }
                $mohId = $currentUser->mohcode;
            }
        }
        
        // إضافة تشخيص للبيانات
        Log::info("Statistics request params:", [
            'moh_id' => $mohId,
            'fck_id' => $fckId, 
            'fctype_id' => $fctypeId,
            'year' => $year,
            'user_role' => $currentUser->roles_name,
            'user_mohcode' => $currentUser->mohcode ?? null,
            'user_fckid' => $currentUser->fckid ?? null
        ]);
        
        $statistics = [];
        
        if ($fckId && $fckId != 'all') {
            // إحصائيات على مستوى المؤسسة - مقارنة شهرية
            $statistics = $this->getFckMonthlyComparison($fckId, $year);
        } elseif ($fctypeId && $fctypeId != 'all') {
            // إحصائيات على مستوى نوع المؤسسة - مقارنة شهرية
            $statistics = $this->getFctypeMonthlyComparison($fctypeId, $year, $mohId);
        } elseif ($mohId && $mohId != 'all') {
            // إحصائيات على مستوى الدائرة - مقارنة شهرية
            $statistics = $this->getMohMonthlyComparison($mohId, $year);
        } else {
            // إحصائيات عامة - مقارنة شهرية لجميع الدوائر
            $statistics = $this->getGeneralMonthlyComparison($year, $currentUser);
        }
        
        // تشخيص البيانات المرجعة
        Log::info("Statistics response type:", ['type' => $statistics['type'] ?? 'unknown']);
        if (isset($statistics['monthly_data']) && !empty($statistics['monthly_data'])) {
            Log::info("First month data sample:", [
                'month' => $statistics['monthly_data'][0]['month_name'] ?? 'unknown',
                'moh_data_count' => count($statistics['monthly_data'][0]['moh_data'] ?? [])
            ]);
        }
        
        return response()->json($statistics);
    }
    
    private function getGeneralMonthlyComparison($year, $currentUser = null)
    {
        $monthlyData = [];
        
        // تحديد الدوائر المسموح بعرضها حسب صلاحيات المستخدم
        if ($currentUser && !in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                // مستخدمو الدوائر - دائرتهم فقط
                $basicMohStats = DB::table('mohs as m')
                    ->where('m.id', $currentUser->mohcode)
                    ->select('m.id as moh_id', 'm.mohname')
                    ->get();
            } else {
                // مستخدمو المؤسسات - دائرتهم فقط
                $basicMohStats = DB::table('mohs as m')
                    ->where('m.id', $currentUser->mohcode)
                    ->select('m.id as moh_id', 'm.mohname')
                    ->get();
            }
        } else {
            // الإدمن - جميع الدوائر
            $basicMohStats = DB::table('mohs as m')
                ->select('m.id as moh_id', 'm.mohname')
                ->get();
        }
        
        for ($month = 1; $month <= 12; $month++) {
            // استعلام منفصل لكل نوع بيانات لتجنب المضاعفة
            $mohStats = collect();
            
            foreach($basicMohStats as $mohStat) {
                // إحصائيات الأسرة - المصدر الأساسي
                $familyStats = DB::table('fcks as f')
                    ->join('pations as p', 'f.id', '=', 'p.fck_id')
                    ->where('f.moh_id', $mohStat->moh_id)
                    ->where('p.month', $month)
                    ->where('p.year', $year)
                    ->selectRaw('
                        COALESCE(SUM(p.unitnum), 0) as total_units,
                        COALESCE(SUM(p.bedm), 0) as total_beds,
                        COALESCE(SUM(p.outpationmon), 0) as total_outpatients,
                        COALESCE(SUM(p.stayoutpation), 0) as total_inpatients,
                        COALESCE(SUM(p.mkoth), 0) as total_stay_days,
                        COALESCE(SUM(p.death), 0) as total_deaths
                    ')
                    ->first();
                
                // إحصائيات الاستشارية - من نفس مصدر الأسرة للتطابق
                $consultationStats = DB::table('fcks as f')
                    ->join('pations as p', 'f.id', '=', 'p.fck_id')
                    ->leftJoin('rdhs as r', 'p.spebed', '=', 'r.id')
                    ->where('f.moh_id', $mohStat->moh_id)
                    ->where('p.month', $month)
                    ->where('p.year', $year)
                    ->whereNotNull('r.id') // فقط الردهات الموجودة
                    ->selectRaw('
                        COUNT(DISTINCT r.id) as total_consultation_halls,
                        COALESCE(SUM(p.bedm), 0) as total_consultation_beds
                    ')
                    ->first();
                
                // إحصائيات الصالات التفصيلية - من جدول pations للتطابق
                
                // إحصائيات العمليات التفصيلية
                $surgeryStats = DB::table('fcks as f')
                    ->join('surgeries as s', 'f.id', '=', 's.fck_id')
                    ->where('f.moh_id', $mohStat->moh_id)
                    ->where('s.month', $month)
                    ->where('s.year', $year)
                    ->selectRaw('
                        COALESCE(SUM(s.khasa), 0) as total_surgeries_special,
                        COALESCE(SUM(s.fkubra), 0) as total_surgeries_major_complex,
                        COALESCE(SUM(s.kubra), 0) as total_surgeries_major,
                        COALESCE(SUM(s.mtws), 0) as total_surgeries_medium,
                        COALESCE(SUM(s.sugra), 0) as total_surgeries_minor,
                        COALESCE(SUM(s.khasa + s.fkubra + s.kubra + s.mtws + s.sugra), 0) as total_surgeries
                    ')
                    ->first();
                
                // إحصائيات الحوادث التفصيلية
                $incidentStats = DB::table('fcks as f')
                    ->join('hwadths as h', 'f.id', '=', 'h.fck_id')
                    ->where('f.moh_id', $mohStat->moh_id)
                    ->where('h.month', $month)
                    ->where('h.year', $year)
                    ->selectRaw('
                        COALESCE(SUM(h.livebnt), 0) as total_births_natural,
                        COALESCE(SUM(h.livebst), 0) as total_births_cesarean,
                        COALESCE(SUM(h.totalbt), 0) as total_births,
                        COALESCE(SUM(h.mdeadt), 0) as total_maternal_deaths,
                        COALESCE(SUM(h.totaldt), 0) as total_deaths
                    ')
                    ->first();
                
                // إحصائيات الحالات والفحوصات
                $caseStats = DB::table('fcks as f')
                    ->join('cases as c', 'f.id', '=', 'c.fck_id')
                    ->where('f.moh_id', $mohStat->moh_id)
                    ->where('c.month', $month)
                    ->where('c.year', $year)
                    ->selectRaw('
                        COALESCE(SUM(c.caseest), 0) as total_emergency_cases,
                        COALESCE(SUM(c.casekhaf), 0) as total_light_cases,
                        COALESCE(SUM(c.caseesemer), 0) as total_ambulance_cases,
                        COALESCE(SUM(c.cheoutpationsan), 0) as total_outpatient_clinical,
                        COALESCE(SUM(c.cheoutpationlab), 0) as total_outpatient_lab,
                        COALESCE(SUM(c.cheinpationsan), 0) as total_inpatient_clinical,
                        COALESCE(SUM(c.cheinpationlab), 0) as total_inpatient_lab,
                        COALESCE(SUM(c.bedssav), 0) as total_available_beds,
                        COALESCE(SUM(c.otherbed), 0) as total_other_beds,
                        COALESCE(SUM(c.bedest), 0) as total_emergency_beds,
                        COALESCE(SUM(c.bedrdhclose), 0) as total_closed_ward_beds,
                        COALESCE(SUM(c.tbedsmoh), 0) as total_moh_beds,
                        COALESCE(SUM(c.totalbed), 0) as total_all_beds,
                        COALESCE(SUM(c.tbedsf), 0) as total_facility_beds
                    ')
                    ->first();
                
                // إحصائيات الصالات التفصيلية - من جدول pations للتطابق
                $consultationDetails = DB::table('fcks as f')
                    ->join('pations as p', 'f.id', '=', 'p.fck_id')
                    ->join('rdhs as r', 'p.spebed', '=', 'r.id')
                    ->where('f.moh_id', $mohStat->moh_id)
                    ->where('p.month', $month)
                    ->where('p.year', $year)
                    ->whereNotNull('r.id')
                    ->selectRaw('
                        r.id as type_id,
                        r.Spcuname as type_name,
                        COUNT(DISTINCT p.unitnum) as halls,
                        SUM(p.bedm) as beds
                    ')
                    ->groupBy('r.id', 'r.Spcuname')
                    ->orderBy('beds', 'desc')
                    ->get();

                // إحصائيات الردهات التفصيلية - شاملة لجميع الأسرة
                $wardDetails = DB::table('fcks as f')
                    ->join('pations as p', 'f.id', '=', 'p.fck_id')
                    ->leftJoin('rdhs as r', 'p.spebed', '=', 'r.id')
                    ->where('f.moh_id', $mohStat->moh_id)
                    ->where('p.month', $month)
                    ->where('p.year', $year)
                    ->selectRaw('
                        COALESCE(r.id, 0) as ward_id,
                        COALESCE(r.Spcuname, "غير محدد") as ward_name,
                        COALESCE(r.Spcuno, 0) as ward_number,
                        SUM(p.unitnum) as total_units,
                        SUM(p.bedm) as total_beds,
                        SUM(p.outpationmon) as total_outpatients,
                        SUM(p.stayoutpation) as total_inpatients
                    ')
                    ->groupBy('r.id', 'r.Spcuname', 'r.Spcuno')
                    ->having(DB::raw('SUM(p.bedm)'), '>', 0)
                    ->orderBy('total_beds', 'desc')
                    ->get();
                
                // عدد المؤسسات
                $institutionsCount = DB::table('fcks')
                    ->where('moh_id', $mohStat->moh_id)
                    ->count();
                
                // تشخيص مفصل للشهر الأول والدائرة الخامسة (نينوى) التي لديها بيانات
                if ($month == 1 && $mohStat->moh_id == 5) {
                    $wardDetailsSum = $wardDetails->sum('total_beds');
                    Log::info("MOH {$mohStat->moh_id} Month {$month} Detailed Bed Analysis:", [
                        'family_total_beds' => $familyStats->total_beds ?? 0,
                        'consultation_beds_from_pations' => $consultationStats->total_consultation_beds ?? 0,
                        'ward_details_sum' => $wardDetailsSum,
                        'ward_details_count' => $wardDetails->count(),
                        'sample_ward' => $wardDetails->first() ? [
                            'name' => $wardDetails->first()->ward_name,
                            'beds' => $wardDetails->first()->total_beds
                        ] : null
                    ]);
                }
                
                $mohStats->push((object)[
                    'moh_id' => $mohStat->moh_id,
                    'mohname' => $mohStat->mohname,
                    'total_institutions' => $institutionsCount,
                    'total_units' => $familyStats->total_units ?? 0,
                    'total_beds' => $familyStats->total_beds ?? 0,
                    'total_outpatients' => $familyStats->total_outpatients ?? 0,
                    'total_inpatients' => $familyStats->total_inpatients ?? 0,
                    'total_stay_days' => $familyStats->total_stay_days ?? 0,
                    'total_deaths' => $familyStats->total_deaths ?? 0,
                    'total_surgeries_special' => $surgeryStats->total_surgeries_special ?? 0,
                    'total_surgeries_major_complex' => $surgeryStats->total_surgeries_major_complex ?? 0,
                    'total_surgeries_major' => $surgeryStats->total_surgeries_major ?? 0,
                    'total_surgeries_medium' => $surgeryStats->total_surgeries_medium ?? 0,
                    'total_surgeries_minor' => $surgeryStats->total_surgeries_minor ?? 0,
                    'total_surgeries' => $surgeryStats->total_surgeries ?? 0,
                    'total_births_natural' => $incidentStats->total_births_natural ?? 0,
                    'total_births_cesarean' => $incidentStats->total_births_cesarean ?? 0,
                    'total_births' => $incidentStats->total_births ?? 0,
                    'total_maternal_deaths' => $incidentStats->total_maternal_deaths ?? 0,
                    'total_consultation_halls' => $consultationStats->total_consultation_halls ?? 0,
                    'total_consultation_beds' => $consultationStats->total_consultation_beds ?? 0,
                    'total_emergency_cases' => $caseStats->total_emergency_cases ?? 0,
                    'total_light_cases' => $caseStats->total_light_cases ?? 0,
                    'total_ambulance_cases' => $caseStats->total_ambulance_cases ?? 0,
                    'total_outpatient_clinical' => $caseStats->total_outpatient_clinical ?? 0,
                    'total_outpatient_lab' => $caseStats->total_outpatient_lab ?? 0,
                    'total_inpatient_clinical' => $caseStats->total_inpatient_clinical ?? 0,
                    'total_inpatient_lab' => $caseStats->total_inpatient_lab ?? 0,
                    'total_available_beds' => $caseStats->total_available_beds ?? 0,
                    'total_other_beds' => $caseStats->total_other_beds ?? 0,
                    'total_emergency_beds' => $caseStats->total_emergency_beds ?? 0,
                    'total_closed_ward_beds' => $caseStats->total_closed_ward_beds ?? 0,
                    'total_moh_beds' => $caseStats->total_moh_beds ?? 0,
                    'total_all_beds' => $caseStats->total_all_beds ?? 0,
                    'total_facility_beds' => $caseStats->total_facility_beds ?? 0,
                    'consultation_details' => $consultationDetails,
                    'ward_details' => $wardDetails,
                ]);
            }
            
            $monthlyData[] = [
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'moh_data' => $mohStats
            ];
        }
        
        return [
            'type' => 'monthly_general',
            'year' => $year,
            'monthly_data' => $monthlyData
        ];
    }
    
    private function getMohMonthlyComparison($mohId, $year)
    {
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            // إحصائيات الأسرة
            $familyStats = pations::whereHas('fck', function($query) use ($mohId) {
                    $query->where('moh_id', $mohId);
                })
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(unitnum) as total_units,
                    SUM(bedm) as total_beds,
                    SUM(outpationmon) as total_outpatients,
                    SUM(stayoutpation) as total_inpatients,
                    SUM(mkoth) as total_stay_days,
                    SUM(death) as total_deaths
                ')
                ->first();
            
            // إحصائيات العمليات
            $surgeryStats = surgery::whereHas('fck', function($query) use ($mohId) {
                    $query->where('moh_id', $mohId);
                })
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(khasa) as total_special,
                    SUM(fkubra) as total_major_complex,
                    SUM(kubra) as total_major,
                    SUM(mtws) as total_medium,
                    SUM(sugra) as total_minor
                ')
                ->first();
            
            // إحصائيات الحوادث
            $incidentStats = hwadth::whereHas('fck', function($query) use ($mohId) {
                    $query->where('moh_id', $mohId);
                })
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(livebnt) as total_live_births_natural,
                    SUM(livebst) as total_live_births_cesarean,
                    SUM(totalbt) as total_births,
                    SUM(totaldt) as total_deaths,
                    SUM(mdeadt) as total_maternal_deaths
                ')
                ->first();
            
            // إحصائيات الاستشارية
            $consultationStats = salat::whereHas('fck', function($query) use ($mohId) {
                    $query->where('moh_id', $mohId);
                })
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(salnum) as total_halls,
                    SUM(bsalnum) as total_hall_beds
                ')
                ->first();
            
            // إحصائيات الحالات والفحوصات
            $caseStats = cases::whereHas('fck', function($query) use ($mohId) {
                    $query->where('moh_id', $mohId);
                })
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(caseest) as total_emergency_cases,
                    SUM(casekhaf) as total_light_cases,
                    SUM(caseesemer) as total_ambulance_cases,
                    SUM(cheoutpationsan) as total_outpatient_clinical,
                    SUM(cheoutpationlab) as total_outpatient_lab,
                    SUM(cheinpationsan) as total_inpatient_clinical,
                    SUM(cheinpationlab) as total_inpatient_lab,
                    SUM(bedssav) as total_available_beds,
                    SUM(otherbed) as total_other_beds,
                    SUM(bedest) as total_emergency_beds,
                    SUM(tbedsmoh) as total_moh_beds,
                    SUM(bedrdhclose) as total_closed_ward_beds,
                    SUM(totalbed) as total_all_beds,
                    SUM(tbedsf) as total_facility_beds
                ')
                ->first();
            
            // إحصائيات الاستشارية التفصيلية حسب النوع
            $consultationDetails = salat::whereHas('fck', function($query) use ($mohId) {
                    $query->where('moh_id', $mohId);
                })
                ->join('salsurs as su', 'salats.salid', '=', 'su.id')
                ->where('salats.month', $month)
                ->where('salats.year', $year)
                ->whereNull('su.deleted_at')
                ->selectRaw('
                    su.id as type_id,
                    su.salsur as type_name,
                    SUM(salats.salnum) as halls,
                    SUM(salats.bsalnum) as beds
                ')
                ->groupBy('su.id', 'su.salsur')
                ->orderBy('halls', 'desc')
                ->get();

            // إحصائيات الردهات حسب النوع - مطابقة للإدخال
            $wardDetails = DB::table('pations as p')
                ->join('fcks as f', 'p.fck_id', '=', 'f.id')
                ->leftJoin('rdhs as r', 'p.spebed', '=', 'r.id')
                ->where('f.moh_id', $mohId)
                ->where('p.month', $month)
                ->where('p.year', $year)
                ->selectRaw('
                    COALESCE(r.id, 0) as ward_id,
                    COALESCE(r.Spcuname, "غير محدد") as ward_name,
                    COALESCE(r.Spcuno, p.spebed) as ward_number,
                    SUM(p.unitnum) as total_units,
                    SUM(p.bedm) as total_beds,
                    SUM(p.outpationmon) as total_outpatients,
                    SUM(p.stayoutpation) as total_inpatients
                ')
                ->groupBy('r.id', 'r.Spcuname', 'r.Spcuno', 'p.spebed')
                ->having(DB::raw('SUM(p.bedm)'), '>', 0)
                ->orderBy('total_beds', 'desc')
                ->get();
            
            $monthlyData[] = [
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'families' => $familyStats ?: (object)[
                    'total_units' => 0,
                    'total_beds' => 0,
                    'total_outpatients' => 0,
                    'total_inpatients' => 0,
                    'total_stay_days' => 0,
                    'total_deaths' => 0
                ],
                'surgeries' => $surgeryStats ? (object)[
                    'total_special' => $surgeryStats->total_special,
                    'total_major_complex' => $surgeryStats->total_major_complex,
                    'total_major' => $surgeryStats->total_major,
                    'total_medium' => $surgeryStats->total_medium,
                    'total_minor' => $surgeryStats->total_minor,
                    'total_all' => $surgeryStats->total_special + $surgeryStats->total_major_complex + $surgeryStats->total_major + $surgeryStats->total_medium + $surgeryStats->total_minor
                ] : (object)[
                    'total_special' => 0,
                    'total_major_complex' => 0,
                    'total_major' => 0,
                    'total_medium' => 0,
                    'total_minor' => 0,
                    'total_all' => 0
                ],
                'incidents' => $incidentStats ?: (object)[
                    'total_live_births_natural' => 0,
                    'total_live_births_cesarean' => 0,
                    'total_births' => 0,
                    'total_deaths' => 0,
                    'total_maternal_deaths' => 0
                ],
                'consultations' => $consultationStats ?: (object)[
                    'total_halls' => 0,
                    'total_hall_beds' => 0
                ],
                'cases' => $caseStats ?: (object)[
                    'total_emergency_cases' => 0,
                    'total_light_cases' => 0,
                    'total_ambulance_cases' => 0,
                    'total_outpatient_clinical' => 0,
                    'total_outpatient_lab' => 0,
                    'total_inpatient_clinical' => 0,
                    'total_inpatient_lab' => 0,
                    'total_available_beds' => 0,
                    'total_other_beds' => 0,
                    'total_emergency_beds' => 0,
                    'total_moh_beds' => 0,
                    'total_closed_ward_beds' => 0,
                    'total_all_beds' => 0,
                    'total_facility_beds' => 0
                ],
                'consultation_details' => $consultationDetails ?: [],
                'ward_details' => $wardDetails ?: []
            ];
        }
        
        return [
            'type' => 'monthly_moh',
            'year' => $year,
            'moh_name' => mohs::find($mohId)->mohname ?? 'غير محدد',
            'monthly_data' => $monthlyData
        ];
    }
    
    private function getFckMonthlyComparison($fckId, $year)
    {
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            // إحصائيات الأسرة
            $familyStats = pations::where('fck_id', $fckId)
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(unitnum) as total_units,
                    SUM(bedm) as total_beds,
                    SUM(outpationmon) as total_outpatients,
                    SUM(stayoutpation) as total_inpatients,
                    SUM(mkoth) as total_stay_days,
                    SUM(death) as total_deaths
                ')
                ->first();
            
            // إحصائيات العمليات
            $surgeryStats = surgery::where('fck_id', $fckId)
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(khasa) as total_special,
                    SUM(fkubra) as total_major_complex,
                    SUM(kubra) as total_major,
                    SUM(mtws) as total_medium,
                    SUM(sugra) as total_minor
                ')
                ->first();
            
            // إحصائيات الحوادث
            $incidentStats = hwadth::where('fck_id', $fckId)
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(livebnt) as total_live_births_natural,
                    SUM(livebst) as total_live_births_cesarean,
                    SUM(totalbt) as total_births,
                    SUM(totaldt) as total_deaths,
                    SUM(mdeadt) as total_maternal_deaths
                ')
                ->first();
            
            // إحصائيات الاستشارية
            $consultationStats = salat::where('fck_id', $fckId)
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(salnum) as total_halls,
                    SUM(bsalnum) as total_hall_beds
                ')
                ->first();

            // إحصائيات الحالات والفحوصات
            $caseStats = cases::where('fck_id', $fckId)
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(caseest) as total_emergency_cases,
                    SUM(casekhaf) as total_light_cases,
                    SUM(caseesemer) as total_ambulance_cases,
                    SUM(cheoutpationsan) as total_outpatient_clinical,
                    SUM(cheoutpationlab) as total_outpatient_lab,
                    SUM(cheinpationsan) as total_inpatient_clinical,
                    SUM(cheinpationlab) as total_inpatient_lab,
                    SUM(bedssav) as total_available_beds,
                    SUM(otherbed) as total_other_beds,
                    SUM(bedest) as total_emergency_beds,
                    SUM(tbedsmoh) as total_moh_beds,
                    SUM(bedrdhclose) as total_closed_ward_beds,
                    SUM(totalbed) as total_all_beds,
                    SUM(tbedsf) as total_facility_beds
                ')
                ->first();
            
            // إحصائيات الاستشارية التفصيلية حسب النوع
            $consultationDetails = salat::where('fck_id', $fckId)
                ->join('salsurs as su', 'salats.salid', '=', 'su.id')
                ->where('salats.month', $month)
                ->where('salats.year', $year)
                ->whereNull('su.deleted_at')
                ->selectRaw('
                    su.id as type_id,
                    su.salsur as type_name,
                    SUM(salats.salnum) as halls,
                    SUM(salats.bsalnum) as beds
                ')
                ->groupBy('su.id', 'su.salsur')
                ->orderBy('halls', 'desc')
                ->get();

            // إحصائيات الردهات حسب النوع - مطابقة للإدخال
            $wardDetails = DB::table('pations as p')
                ->leftJoin('rdhs as r', 'p.spebed', '=', 'r.id')
                ->where('p.fck_id', $fckId)
                ->where('p.month', $month)
                ->where('p.year', $year)
                ->selectRaw('
                    COALESCE(r.id, 0) as ward_id,
                    COALESCE(r.Spcuname, "غير محدد") as ward_name,
                    COALESCE(r.Spcuno, p.spebed) as ward_number,
                    SUM(p.unitnum) as total_units,
                    SUM(p.bedm) as total_beds,
                    SUM(p.outpationmon) as total_outpatients,
                    SUM(p.stayoutpation) as total_inpatients
                ')
                ->groupBy('r.id', 'r.Spcuname', 'r.Spcuno', 'p.spebed')
                ->having(DB::raw('SUM(p.bedm)'), '>', 0)
                ->orderBy('total_beds', 'desc')
                ->get();
            
            $monthlyData[] = [
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'families' => $familyStats ?: (object)[
                    'total_units' => 0,
                    'total_beds' => 0,
                    'total_outpatients' => 0,
                    'total_inpatients' => 0,
                    'total_stay_days' => 0,
                    'total_deaths' => 0
                ],
                'surgeries' => $surgeryStats ? (object)[
                    'total_special' => $surgeryStats->total_special,
                    'total_major_complex' => $surgeryStats->total_major_complex,
                    'total_major' => $surgeryStats->total_major,
                    'total_medium' => $surgeryStats->total_medium,
                    'total_minor' => $surgeryStats->total_minor,
                    'total_all' => $surgeryStats->total_special + $surgeryStats->total_major_complex + $surgeryStats->total_major + $surgeryStats->total_medium + $surgeryStats->total_minor
                ] : (object)[
                    'total_special' => 0,
                    'total_major_complex' => 0,
                    'total_major' => 0,
                    'total_medium' => 0,
                    'total_minor' => 0,
                    'total_all' => 0
                ],
                'incidents' => $incidentStats ?: (object)[
                    'total_live_births_natural' => 0,
                    'total_live_births_cesarean' => 0,
                    'total_births' => 0,
                    'total_deaths' => 0,
                    'total_maternal_deaths' => 0
                ],
                'consultations' => $consultationStats ?: (object)[
                    'total_halls' => 0,
                    'total_hall_beds' => 0
                ],
                'cases' => $caseStats ?: (object)[
                    'total_emergency_cases' => 0,
                    'total_light_cases' => 0,
                    'total_ambulance_cases' => 0,
                    'total_outpatient_clinical' => 0,
                    'total_outpatient_lab' => 0,
                    'total_inpatient_clinical' => 0,
                    'total_inpatient_lab' => 0,
                    'total_available_beds' => 0,
                    'total_other_beds' => 0,
                    'total_emergency_beds' => 0,
                    'total_moh_beds' => 0,
                    'total_closed_ward_beds' => 0,
                    'total_all_beds' => 0,
                    'total_facility_beds' => 0
                ],
                'consultation_details' => $consultationDetails ?: [],
                'ward_details' => $wardDetails ?: []
            ];
        }
        
        return [
            'type' => 'monthly_fck',
            'year' => $year,
            'fck_name' => fck::find($fckId)->Fckname ?? 'غير محدد',
            'monthly_data' => $monthlyData
        ];
    }
    
    private function getFctypeMonthlyComparison($fctypeId, $year, $mohId = null)
    {
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            // بناء الاستعلام الأساسي للمؤسسات من النوع المحدد
            $fckQuery = fck::where('fctypesid', $fctypeId);
            
            // إضافة فلتر الدائرة إذا تم تحديدها
            if ($mohId && $mohId !== 'all') {
                $fckQuery->where('moh_id', $mohId);
            }
            
            $fckIds = $fckQuery->pluck('id');
            
            // إحصائيات الأسرة
            $familyStats = pations::whereIn('fck_id', $fckIds)
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(unitnum) as total_units,
                    SUM(bedm) as total_beds,
                    SUM(outpationmon) as total_outpatients,
                    SUM(stayoutpation) as total_inpatients,
                    SUM(mkoth) as total_stay_days,
                    SUM(death) as total_deaths
                ')
                ->first();
            
            // إحصائيات العمليات
            $surgeryStats = surgery::whereIn('fck_id', $fckIds)
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(khasa) as total_special,
                    SUM(fkubra) as total_major_complex,
                    SUM(kubra) as total_major,
                    SUM(mtws) as total_medium,
                    SUM(sugra) as total_minor
                ')
                ->first();
            
            // إحصائيات الحوادث
            $incidentStats = hwadth::whereIn('fck_id', $fckIds)
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(livebnt) as total_live_births_natural,
                    SUM(livebst) as total_live_births_cesarean,
                    SUM(totalbt) as total_births,
                    SUM(totaldt) as total_deaths,
                    SUM(mdeadt) as total_maternal_deaths
                ')
                ->first();
            
            // إحصائيات الاستشارية
            $consultationStats = salat::whereIn('fck_id', $fckIds)
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(salnum) as total_halls,
                    SUM(bsalnum) as total_hall_beds
                ')
                ->first();

            // إحصائيات الحالات والفحوصات
            $caseStats = cases::whereIn('fck_id', $fckIds)
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('
                    SUM(caseest) as total_emergency_cases,
                    SUM(casekhaf) as total_light_cases,
                    SUM(caseesemer) as total_ambulance_cases,
                    SUM(cheoutpationsan) as total_outpatient_clinical,
                    SUM(cheoutpationlab) as total_outpatient_lab,
                    SUM(cheinpationsan) as total_inpatient_clinical,
                    SUM(cheinpationlab) as total_inpatient_lab,
                    SUM(bedssav) as total_available_beds,
                    SUM(otherbed) as total_other_beds,
                    SUM(bedest) as total_emergency_beds,
                    SUM(tbedsmoh) as total_moh_beds,
                    SUM(bedrdhclose) as total_closed_ward_beds,
                    SUM(totalbed) as total_all_beds,
                    SUM(tbedsf) as total_facility_beds
                ')
                ->first();
            
            // إحصائيات الاستشارية التفصيلية حسب النوع
            $consultationDetails = salat::whereIn('fck_id', $fckIds)
                ->join('salsurs as su', 'salats.salid', '=', 'su.id')
                ->where('salats.month', $month)
                ->where('salats.year', $year)
                ->whereNull('su.deleted_at')
                ->selectRaw('
                    su.id as type_id,
                    su.salsur as type_name,
                    SUM(salats.salnum) as halls,
                    SUM(salats.bsalnum) as beds
                ')
                ->groupBy('su.id', 'su.salsur')
                ->orderBy('halls', 'desc')
                ->get();

            // إحصائيات الردهات حسب النوع - مطابقة للإدخال
            $wardDetails = DB::table('pations as p')
                ->leftJoin('rdhs as r', 'p.spebed', '=', 'r.id')
                ->whereIn('p.fck_id', $fckIds)
                ->where('p.month', $month)
                ->where('p.year', $year)
                ->selectRaw('
                    COALESCE(r.id, 0) as ward_id,
                    COALESCE(r.Spcuname, "غير محدد") as ward_name,
                    COALESCE(r.Spcuno, p.spebed) as ward_number,
                    SUM(p.unitnum) as total_units,
                    SUM(p.bedm) as total_beds,
                    SUM(p.outpationmon) as total_outpatients,
                    SUM(p.stayoutpation) as total_inpatients
                ')
                ->groupBy('r.id', 'r.Spcuname', 'r.Spcuno', 'p.spebed')
                ->having(DB::raw('SUM(p.bedm)'), '>', 0)
                ->orderBy('total_beds', 'desc')
                ->get();
            
            $monthlyData[] = [
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'families' => $familyStats ?: (object)[
                    'total_units' => 0,
                    'total_beds' => 0,
                    'total_outpatients' => 0,
                    'total_inpatients' => 0,
                    'total_stay_days' => 0,
                    'total_deaths' => 0
                ],
                'surgeries' => $surgeryStats ? (object)[
                    'total_special' => $surgeryStats->total_special,
                    'total_major_complex' => $surgeryStats->total_major_complex,
                    'total_major' => $surgeryStats->total_major,
                    'total_medium' => $surgeryStats->total_medium,
                    'total_minor' => $surgeryStats->total_minor,
                    'total_all' => $surgeryStats->total_special + $surgeryStats->total_major_complex + $surgeryStats->total_major + $surgeryStats->total_medium + $surgeryStats->total_minor
                ] : (object)[
                    'total_special' => 0,
                    'total_major_complex' => 0,
                    'total_major' => 0,
                    'total_medium' => 0,
                    'total_minor' => 0,
                    'total_all' => 0
                ],
                'incidents' => $incidentStats ?: (object)[
                    'total_live_births_natural' => 0,
                    'total_live_births_cesarean' => 0,
                    'total_births' => 0,
                    'total_deaths' => 0,
                    'total_maternal_deaths' => 0
                ],
                'consultations' => $consultationStats ?: (object)[
                    'total_halls' => 0,
                    'total_hall_beds' => 0
                ],
                'cases' => $caseStats ?: (object)[
                    'total_emergency_cases' => 0,
                    'total_light_cases' => 0,
                    'total_ambulance_cases' => 0,
                    'total_outpatient_clinical' => 0,
                    'total_outpatient_lab' => 0,
                    'total_inpatient_clinical' => 0,
                    'total_inpatient_lab' => 0,
                    'total_available_beds' => 0,
                    'total_other_beds' => 0,
                    'total_emergency_beds' => 0,
                    'total_moh_beds' => 0,
                    'total_closed_ward_beds' => 0,
                    'total_all_beds' => 0,
                    'total_facility_beds' => 0
                ],
                'consultation_details' => $consultationDetails ?: [],
                'ward_details' => $wardDetails ?: []
            ];
        }
        
        $fctypeName = DB::table('fctypes')->where('id', $fctypeId)->value('Fname') ?? 'غير محدد';
        $mohName = $mohId && $mohId !== 'all' ? (mohs::find($mohId)->mohname ?? '') : '';
        
        return [
            'type' => 'monthly_fctype',
            'year' => $year,
            'fctype_name' => $fctypeName,
            'moh_name' => $mohName,
            'monthly_data' => $monthlyData
        ];
    }
    
    private function getMonthName($month)
    {
        $months = [
            1 => 'كانون الثاني',
            2 => 'شباط', 
            3 => 'اذار',
            4 => 'نيسان',
            5 => 'ايار',
            6 => 'حزيران',
            7 => 'تموز',
            8 => 'اب',
            9 => 'ايلول',
            10 => 'تشرين الاول',
            11 => 'تشرين الثاني',
            12 => 'كانون الاول'
        ];
        
        return $months[$month] ?? 'غير محدد';
    }
    
    public function exportStatistics(Request $request)
    {
        $currentUser = Auth::user();
        $mohId = $request->input('moh_id');
        $fckId = $request->input('fck_id');
        $fctypeId = $request->input('fctype_id');
        $year = $request->input('year', date('Y'));
        
        // تطبيق نفس قيود الصلاحيات كما في getComparisonData
        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                // مستخدمو الدوائر
                if ($mohId && $mohId != 'all' && $mohId != $currentUser->mohcode) {
                    return response()->json(['error' => 'غير مسموح لك بتصدير بيانات دوائر أخرى'], 403);
                }
                if ($mohId === 'all') {
                    $mohId = $currentUser->mohcode;
                    $request->merge(['moh_id' => $mohId]);
                }
                
                if ($fckId && $fckId != 'all') {
                    $fckMohId = fck::where('id', $fckId)->value('moh_id');
                    if ($fckMohId != $currentUser->mohcode) {
                        return response()->json(['error' => 'غير مسموح لك بتصدير بيانات مؤسسات دوائر أخرى'], 403);
                    }
                }
            } else {
                // مستخدمو المؤسسات
                if ($fckId && $fckId != 'all' && $fckId != $currentUser->fckid) {
                    return response()->json(['error' => 'غير مسموح لك بتصدير بيانات مؤسسات أخرى'], 403);
                }
                if ($fckId === 'all') {
                    $fckId = $currentUser->fckid;
                    $request->merge(['fck_id' => $fckId]);
                }
                $mohId = $currentUser->mohcode;
                $request->merge(['moh_id' => $mohId]);
            }
        }
        
        // استخدام نفس المنطق المُستخدم في getComparisonData لضمان التوافق
        $comparisonResponse = $this->getComparisonData($request);
        $statisticsData = $comparisonResponse->getData(true); // تحويل JsonResponse إلى array
        
        // التحقق من وجود خطأ في البيانات
        if (isset($statisticsData['error'])) {
            return response()->json($statisticsData, 403);
        }
        
        // Generate filename based on selection
        $filename = $this->generateFilename($request, $statisticsData);
        
        // Create the export with the statistics data
        $export = new StatisticsExport($statisticsData, 'إحصائيات المقارنات');
        
        try {
            return Excel::download($export, $filename);
        } catch (\Exception $e) {
            Log::error('Statistics export error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير الإحصائيات: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function generateFilename($request, $data)
    {
        $mohId = $request->input('moh_id');
        $fckId = $request->input('fck_id');
        $fctypeId = $request->input('fctype_id');
        $year = $request->input('year', date('Y'));
        
        $filename = 'احصائيات_';
        
        if ($fckId && $fckId != 'all') {
            $fckName = fck::find($fckId)->Fckname ?? 'مؤسسة_غير_محددة';
            $filename .= 'مؤسسة_' . str_replace([' ', '/', '\\'], '_', $fckName);
        } elseif ($fctypeId && $fctypeId != 'all') {
            $fctypeName = DB::table('fctypes')->where('id', $fctypeId)->value('Fname') ?? 'نوع_غير_محدد';
            $filename .= 'نوع_' . str_replace([' ', '/', '\\'], '_', $fctypeName);
            if ($mohId && $mohId != 'all') {
                $mohName = mohs::find($mohId)->mohname ?? '';
                if ($mohName) {
                    $filename .= '_' . str_replace([' ', '/', '\\'], '_', $mohName);
                }
            }
        } elseif ($mohId && $mohId != 'all') {
            $mohName = mohs::find($mohId)->mohname ?? 'دائرة_غير_محددة';
            $filename .= 'دائرة_' . str_replace([' ', '/', '\\'], '_', $mohName);
        } else {
            $filename .= 'عام_جميع_الدوائر';
        }
        
        $filename .= '_' . $year . '_' . date('Y_m_d_H_i_s') . '.xlsx';
        
        return $filename;
    }
    
    public function getInstitutionsByMoh($mohId)
    {
        $currentUser = Auth::user();
        
        // تطبيق قيود الصلاحيات
        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                // مستخدمو الدوائر - التحقق من أن الدائرة المطلوبة هي دائرتهم
                if ($mohId !== 'all' && $mohId != $currentUser->mohcode) {
                    return response()->json(['error' => 'غير مسموح لك بعرض مؤسسات دوائر أخرى'], 403);
                }
                $mohId = $currentUser->mohcode; // فرض دائرة المستخدم
            } else {
                // مستخدمو المؤسسات - مؤسستهم فقط
                return response()->json(fck::where('id', $currentUser->fckid)->get());
            }
        }
        
        if ($mohId === 'all') {
            if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
                // غير الإدمن لا يستطيع رؤية كل المؤسسات
                if ($currentUser->roles_name === ["stat-doh"]) {
                    $institutions = fck::where('moh_id', $currentUser->mohcode)->get();
                } else {
                    $institutions = fck::where('id', $currentUser->fckid)->get();
                }
            } else {
                $institutions = fck::all();
            }
        } else {
            $institutions = fck::where('moh_id', $mohId)->get();
        }
        
        return response()->json($institutions);
    }
}
