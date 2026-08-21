<?php

namespace App\Http\Controllers;

use App\Models\pations;
use App\Models\mohs;
use App\Models\rdhs;
use App\Models\fck;
use App\Models\Fctypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstitutionReportController extends Controller
{
    /**
     * تقرير عدد المؤسسات حسب نوع الردهة
     * 
     * @return \Illuminate\Http\Response
     */
    public function institutionsByHallType(Request $request)
    {
        $currentUser = Auth::user();
        
        // التحقق من الصلاحية - فقط Admin و mohst
        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا التقرير');
        }
        
        // منع الوصول للمستخدمين المرتبطين بالأجنحة الخاصة
        $privateWingRoles = ['private_wing_employee', 'private_wing_supervisor', 'private_wing_admin'];
        if (array_intersect($privateWingRoles, $currentUser->roles_name)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا التقرير');
        }
        
        // تحديد الشهر والسنة الافتراضيين (الشهر الماضي)
        $defaultMonth = $request->filled('month') && $request->month != '' ? $request->month : date('n');
        $defaultYear = $request->filled('year') && $request->year != '' ? $request->year : date('Y');
        
        // جلب جميع أنواع الردهات
        $hallTypes = rdhs::orderBy('Spcuname')->get();
        
        // إنشاء استعلام للحصول على عدد المؤسسات حسب نوع الردهة
        $query = pations::with(['fck', 'rdhs', 'moh'])
            ->select('spebed', DB::raw('COUNT(DISTINCT fck_id) as institution_count'))
            ->groupBy('spebed');
        
        // تطبيق الصلاحيات حسب دور المستخدم
        if (in_array("Admin", $currentUser->roles_name)) {
            // Admin يمكنه رؤية كل شيء
        } elseif (in_array("mohst", $currentUser->roles_name)) {
            // مستخدمو الدوائر - تصفية حسب الدائرة
            $query->where('moh_id', $currentUser->mohcode);
        } else {
            // مستخدمو المؤسسات - تصفية حسب المؤسسة
            $query->where('fck_id', $currentUser->fckid);
        }
        
        // إضافة تصفية حسب الدائرة إذا تم تحديدها
        if ($request->filled('moh_id') && $request->moh_id != '') {
            $query->where('moh_id', $request->moh_id);
        }
        
        // إضافة تصفية حسب نوع المؤسسة إذا تم تحديده
        if ($request->filled('fctype_id') && $request->fctype_id != '') {
            $query->whereHas('fck', function($q) use ($request) {
                $q->where('fctypesid', $request->fctype_id);
            });
        }
        
        // تطبيق فلتر الشهر والسنة الافتراضي
        $query->where('month', $defaultMonth);
        $query->where('year', $defaultYear);
        
        $institutionsByHallType = $query->get();
        
        // إنشاء مصفوفة للنتائج مع تفاصيل الردهة
        $results = [];
        foreach ($institutionsByHallType as $item) {
            $hallType = rdhs::find($item->spebed);
            if ($hallType) {
                $results[] = [
                    'hall_type_id' => $item->spebed,
                    'hall_type_code' => $hallType->Spcuno,
                    'hall_type_name' => $hallType->Spcuname,
                    'institution_count' => $item->institution_count
                ];
            }
        }
        
        // ترتيب النتائج حسب عدد المؤسسات (تنازلياً)
        usort($results, function($a, $b) {
            return $b['institution_count'] - $a['institution_count'];
        });
        
        // جلب جميع الدوائر للفلترة
        $mohs = mohs::orderBy('mohname')->get();
        
        // جلب جميع أنواع المؤسسات للفلترة
        $fctypes = Fctypes::orderBy('Fname')->get();
        
        // حساب الإجمالي
        $totalInstitutions = collect($results)->sum('institution_count');
        
        return view('reports.institutions_by_hall_type', compact('results', 'mohs', 'fctypes', 'totalInstitutions', 'request', 'defaultMonth', 'defaultYear'));
    }
    
    /**
     * جلب المؤسسات حسب نوع الردهة
     * 
     * @param int $hallTypeId
     * @return \Illuminate\Http\Response
     */
    public function getInstitutionsByHallType(Request $request, $hallTypeId)
    {
        \Log::info('getInstitutionsByHallType called', [
            'hall_type_id' => $hallTypeId,
            'request_params' => $request->all(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);
        
        try {
            $currentUser = Auth::user();
            
            if (!$currentUser) {
                return response()->json(['error' => 'غير مصرح لك بالوصول'], 401);
            }
            
            if (!$hallTypeId) {
                return response()->json(['error' => 'معرف نوع الردهة مطلوب'], 400);
            }
            
            // تحويل roles_name إلى array إذا كان string - مع التحقق من الوجود
            $userRoles = [];
            if ($currentUser->roles_name) {
                if (is_array($currentUser->roles_name)) {
                    $userRoles = $currentUser->roles_name;
                } else {
                    $decoded = json_decode($currentUser->roles_name, true);
                    if (is_array($decoded)) {
                        $userRoles = $decoded;
                    } else {
                        $userRoles = [$currentUser->roles_name];
                    }
                }
            }
            
            \Log::info('User roles processed:', [
                'user_id' => $currentUser->id,
                'original_roles' => $currentUser->roles_name,
                'processed_roles' => $userRoles,
                'hall_type_id' => $hallTypeId
            ]);
            
            // إنشاء استعلام للحصول على المؤسسات
            $query = pations::where('spebed', $hallTypeId);
            
            // تطبيق الصلاحيات حسب دور المستخدم - فقط Admin و mohst يمكنهم رؤية كل شيء
            if (in_array("Admin", $userRoles) || in_array("mohst", $userRoles)) {
                // Admin و mohst يمكنهم رؤية كل شيء
            } else {
                // باقي المستخدمين يرون بياناتهم فقط
                if (in_array("stat-doh", $userRoles)) {
                    $query->where('moh_id', $currentUser->mohcode);
                } else {
                    $query->where('fck_id', $currentUser->fckid);
                }
            }
            
            // تطبيق الفلاتر من الطلب
            if ($request->filled('moh_id') && $request->moh_id != '') {
                $query->where('moh_id', $request->moh_id);
            }
            
            // تطبيق فلاتر الشهر والسنة (مهمة لمطابقة البيانات)
            if ($request->filled('month') && $request->month != '') {
                $query->where('month', $request->month);
            }
            
            if ($request->filled('year') && $request->year != '') {
                $query->where('year', $request->year);
            }
        
            $institutionIds = $query->distinct()->pluck('fck_id');
            
            // تطبيق فلتر نوع المؤسسة إذا تم تحديده
            if ($request->filled('fctype_id') && $request->fctype_id != '') {
                $institutionIds = fck::whereIn('id', $institutionIds)
                    ->where('fctypesid', $request->fctype_id)
                    ->pluck('id');
            }
            
            \Log::info('Institution IDs found:', [
                'count' => $institutionIds->count(),
                'ids' => $institutionIds->toArray()
            ]);
            
        // جلب المؤسسات مع عدد الأسرة - بدون eager loading لتجنب مشاكل السيرفر
        $institutions = fck::whereIn('id', $institutionIds)
            ->get()
            ->map(function($institution) use ($hallTypeId, $request) {
                // حساب عدد الأسرة لهذه المؤسسة في هذا النوع من الردهة
                $bedsQuery = pations::where('fck_id', $institution->id)
                    ->where('spebed', $hallTypeId);
                
                // تطبيق نفس الفلاتر
                if ($request->filled('month') && $request->month != '') {
                    $bedsQuery->where('month', $request->month);
                }
                
                if ($request->filled('year') && $request->year != '') {
                    $bedsQuery->where('year', $request->year);
                }
                
                $totalBeds = $bedsQuery->sum('bedm');
                
                // جلب معلومات الدائرة ونوع المؤسسة مباشرة من قاعدة البيانات
                $mohName = 'غير محدد';
                $typeName = 'غير محدد';
                
                try {
                    if ($institution->moh_id) {
                        $moh = mohs::find($institution->moh_id);
                        $mohName = $moh ? $moh->mohname : 'غير محدد';
                    }
                    
                    if ($institution->fctypesid) {
                        $fctype = Fctypes::find($institution->fctypesid);
                        $typeName = $fctype ? $fctype->Fname : 'غير محدد';
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error loading related data: ' . $e->getMessage());
                }
                
                return [
                    'id' => $institution->id,
                    'name' => $institution->Fckname ?? 'غير محدد',
                    'moh_name' => $mohName,
                    'type_name' => $typeName,
                    'total_beds' => (int)$totalBeds,
                ];
            });
            
            \Log::info('Institutions processed:', [
                'count' => $institutions->count(),
                'institutions' => $institutions->toArray()
            ]);
            
            // جلب معلومات نوع الردهة
            $hallType = rdhs::find($hallTypeId);
            if (!$hallType) {
                return response()->json(['error' => 'نوع الردهة غير موجود'], 404);
            }
            
            \Log::info('Final response data:', [
                'hall_type' => $hallType ? $hallType->toArray() : null,
                'institutions_count' => $institutions->count(),
                'institutions' => $institutions->values()->toArray()
            ]);
            
            return response()->json([
                'hallType' => $hallType,
                'institutions' => $institutions->values()->toArray()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getInstitutionsByHallType: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('User ID: ' . ($currentUser->id ?? 'N/A'));
            \Log::error('Hall Type ID: ' . $hallTypeId);
            \Log::error('Request params: ' . json_encode($request->all()));
            
            return response()->json([
                'error' => 'حدث خطأ أثناء تحميل البيانات',
                'message' => config('app.debug') ? $e->getMessage() : 'خطأ في السيرفر',
                'line' => config('app.debug') ? $e->getLine() : null,
                'file' => config('app.debug') ? $e->getFile() : null
            ], 500);
        }
    }

    /**
     * تقرير عدد المؤسسات حسب نوع المؤسسة ودائرة الصحة
     * 
     * @return \Illuminate\Http\Response
     */
    public function institutionsByTypeAndDepartment(Request $request)
    {
        $currentUser = Auth::user();
        
        // التحقق من الصلاحية - فقط Admin و mohst
        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا التقرير');
        }
        
        // إنشاء استعلام للحصول على عدد المؤسسات حسب نوع المؤسسة ودائرة الصحة
        // المؤسسات التي لها مستخدمين
        $queryWithUsers = fck::query()
            ->select(
                'mohs.id as moh_id',
                'mohs.mohname as moh_name',
                'fctypes.id as fctype_id',
                'fctypes.Fname as fctype_name',
                DB::raw('COUNT(fcks.id) as institution_count')
            )
            ->leftJoin('mohs', 'fcks.moh_id', '=', 'mohs.id')
            ->leftJoin('fctypes', 'fcks.fctypesid', '=', 'fctypes.id')
            // فقط المؤسسات التي لها مستخدمين
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('users')
                      ->whereColumn('users.fckid', 'fcks.id');
            })
            ->groupBy('mohs.id', 'mohs.mohname', 'fctypes.id', 'fctypes.Fname');
        
        // المؤسسات التي بدون مستخدمين
        $queryWithoutUsers = fck::query()
            ->select(
                'mohs.id as moh_id',
                'mohs.mohname as moh_name',
                'fctypes.id as fctype_id',
                'fctypes.Fname as fctype_name',
                DB::raw('COUNT(fcks.id) as institution_count_without_users')
            )
            ->leftJoin('mohs', 'fcks.moh_id', '=', 'mohs.id')
            ->leftJoin('fctypes', 'fcks.fctypesid', '=', 'fctypes.id')
            // فقط المؤسسات التي بدون مستخدمين
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('users')
                      ->whereColumn('users.fckid', 'fcks.id');
            })
            ->groupBy('mohs.id', 'mohs.mohname', 'fctypes.id', 'fctypes.Fname');
        
        // تطبيق الصلاحيات حسب دور المستخدم
        if (in_array("Admin", $currentUser->roles_name)) {
            // Admin يمكنه رؤية كل شيء
        } elseif (in_array("mohst", $currentUser->roles_name)) {
            // مستخدمو الدوائر - تصفية حسب الدائرة
            $queryWithUsers->where('fcks.moh_id', $currentUser->mohcode);
            $queryWithoutUsers->where('fcks.moh_id', $currentUser->mohcode);
        }
        
        // إضافة تصفية حسب الدائرة إذا تم تحديدها
        if ($request->filled('moh_id') && $request->moh_id != '' && $request->moh_id != 'all') {
            $queryWithUsers->where('fcks.moh_id', $request->moh_id);
            $queryWithoutUsers->where('fcks.moh_id', $request->moh_id);
        }
        
        // إضافة تصفية حسب نوع المؤسسة إذا تم تحديده
        if ($request->filled('fctype_id') && $request->fctype_id != '' && $request->fctype_id != 'all') {
            $queryWithUsers->where('fcks.fctypesid', $request->fctype_id);
            $queryWithoutUsers->where('fcks.fctypesid', $request->fctype_id);
        }
        
        $resultsWithUsers = $queryWithUsers->orderBy('mohs.mohname')
            ->orderBy('fctypes.Fname')
            ->get()
            ->toArray();
        
        $resultsWithoutUsers = $queryWithoutUsers->orderBy('mohs.mohname')
            ->orderBy('fctypes.Fname')
            ->get()
            ->toArray();
        
        // دمج النتائج
        $results = [];
        $withoutUsersMap = [];
        
        // إنشاء خريطة للمؤسسات بدون مستخدمين
        foreach ($resultsWithoutUsers as $item) {
            $key = $item['moh_id'] . '_' . $item['fctype_id'];
            $withoutUsersMap[$key] = $item['institution_count_without_users'];
        }
        
        // إضافة عدد المؤسسات بدون مستخدمين إلى النتائج
        foreach ($resultsWithUsers as $item) {
            $key = $item['moh_id'] . '_' . $item['fctype_id'];
            $item['institution_count_without_users'] = $withoutUsersMap[$key] ?? 0;
            $results[] = $item;
        }
        
        // إضافة المؤسسات التي بدون مستخدمين فقط (إذا لم يكن لها سجل مع مستخدمين)
        foreach ($resultsWithoutUsers as $item) {
            $key = $item['moh_id'] . '_' . $item['fctype_id'];
            $exists = false;
            foreach ($results as $r) {
                if (($r['moh_id'] . '_' . $r['fctype_id']) == $key) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $results[] = [
                    'moh_id' => $item['moh_id'],
                    'moh_name' => $item['moh_name'],
                    'fctype_id' => $item['fctype_id'],
                    'fctype_name' => $item['fctype_name'],
                    'institution_count' => 0,
                    'institution_count_without_users' => $item['institution_count_without_users']
                ];
            }
        }
        
        // جلب جميع الدوائر للفلترة
        $mohs = mohs::orderBy('mohname')->get();
        
        // جلب جميع أنواع المؤسسات للفلترة
        $fctypes = Fctypes::orderBy('Fname')->get();
        
        // حساب الإجماليات
        $totalInstitutions = array_sum(array_column($results, 'institution_count'));
        
        // حساب عدد المؤسسات التي لها مستخدمين والتي ليس لها مستخدمين
        $institutionsWithUsersQuery = fck::query()
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('users')
                      ->whereColumn('users.fckid', 'fcks.id');
            });
        
        $institutionsWithoutUsersQuery = fck::query()
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('users')
                      ->whereColumn('users.fckid', 'fcks.id');
            });
        
        // تطبيق الفلاتر على كلا الاستعلامين
        if (in_array("mohst", $currentUser->roles_name)) {
            $institutionsWithUsersQuery->where('moh_id', $currentUser->mohcode);
            $institutionsWithoutUsersQuery->where('moh_id', $currentUser->mohcode);
        }
        
        if ($request->filled('moh_id') && $request->moh_id != '' && $request->moh_id != 'all') {
            $institutionsWithUsersQuery->where('moh_id', $request->moh_id);
            $institutionsWithoutUsersQuery->where('moh_id', $request->moh_id);
        }
        
        if ($request->filled('fctype_id') && $request->fctype_id != '' && $request->fctype_id != 'all') {
            $institutionsWithUsersQuery->where('fctypesid', $request->fctype_id);
            $institutionsWithoutUsersQuery->where('fctypesid', $request->fctype_id);
        }
        
        $institutionsWithUsers = $institutionsWithUsersQuery->count();
        $institutionsWithoutUsers = $institutionsWithoutUsersQuery->count();
        $totalAllInstitutions = $institutionsWithUsers + $institutionsWithoutUsers;
        
        // تجميع البيانات حسب الدائرة
        $resultsByDepartment = [];
        foreach ($results as $item) {
            $mohId = $item['moh_id'] ?? 'unknown';
            if (!isset($resultsByDepartment[$mohId])) {
                $resultsByDepartment[$mohId] = [
                    'moh_name' => $item['moh_name'] ?? 'غير محدد',
                    'types' => [],
                    'total' => 0,
                    'total_without_users' => 0
                ];
            }
            $resultsByDepartment[$mohId]['types'][] = [
                'fctype_name' => $item['fctype_name'] ?? 'غير محدد',
                'count' => $item['institution_count'],
                'count_without_users' => $item['institution_count_without_users'] ?? 0
            ];
            $resultsByDepartment[$mohId]['total'] += $item['institution_count'];
            $resultsByDepartment[$mohId]['total_without_users'] += $item['institution_count_without_users'] ?? 0;
        }
        
        return view('reports.institutions_by_type_department', compact(
            'results',
            'resultsByDepartment',
            'mohs',
            'fctypes',
            'totalInstitutions',
            'institutionsWithUsers',
            'institutionsWithoutUsers',
            'totalAllInstitutions',
            'request'
        ));
    }

    /**
     * جلب تفاصيل المؤسسات حسب النوع والدائرة
     * 
     * @return \Illuminate\Http\Response
     */
    public function getInstitutionsByTypeDepartment(Request $request)
    {
        try {
            $currentUser = Auth::user();
            
            if (!$currentUser) {
                return response()->json(['error' => 'غير مصرح لك بالوصول'], 401);
            }
            
            // إنشاء استعلام للحصول على المؤسسات
            $query = fck::query()
                ->select(
                    'fcks.id',
                    'fcks.Fckname as name',
                    'mohs.mohname as moh_name',
                    'fctypes.Fname as fctype_name'
                )
                ->leftJoin('mohs', 'fcks.moh_id', '=', 'mohs.id')
                ->leftJoin('fctypes', 'fcks.fctypesid', '=', 'fctypes.id')
                // فقط المؤسسات التي لها مستخدمين
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('users')
                          ->whereColumn('users.fckid', 'fcks.id');
                });
            
            // تطبيق الفلاتر
            if ($request->filled('moh_id') && $request->moh_id != '' && $request->moh_id != 'all') {
                $query->where('fcks.moh_id', $request->moh_id);
            }
            
            if ($request->filled('fctype_id') && $request->fctype_id != '' && $request->fctype_id != 'all') {
                $query->where('fcks.fctypesid', $request->fctype_id);
            }
            
            $institutions = $query->orderBy('mohs.mohname')
                ->orderBy('fcks.Fckname')
                ->get();
            
            return response()->json([
                'institutions' => $institutions,
                'total' => $institutions->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getInstitutionsByTypeDepartment: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'حدث خطأ أثناء تحميل البيانات',
                'message' => config('app.debug') ? $e->getMessage() : 'خطأ في السيرفر'
            ], 500);
        }
    }

    /**
     * جلب المؤسسات بدون مستخدمين
     * 
     * @return \Illuminate\Http\Response
     */
    public function getInstitutionsWithoutUsers(Request $request)
    {
        try {
            $currentUser = Auth::user();
            
            if (!$currentUser) {
                return response()->json(['error' => 'غير مصرح لك بالوصول'], 401);
            }
            
            // إنشاء استعلام للحصول على المؤسسات بدون مستخدمين
            $query = fck::query()
                ->select(
                    'fcks.id',
                    'fcks.Fckname as name',
                    'mohs.mohname as moh_name',
                    'fctypes.Fname as fctype_name'
                )
                ->leftJoin('mohs', 'fcks.moh_id', '=', 'mohs.id')
                ->leftJoin('fctypes', 'fcks.fctypesid', '=', 'fctypes.id')
                // فقط المؤسسات التي ليس لها مستخدمين
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('users')
                          ->whereColumn('users.fckid', 'fcks.id');
                });
            
            // تطبيق الصلاحيات
            if (in_array("mohst", $currentUser->roles_name)) {
                $query->where('fcks.moh_id', $currentUser->mohcode);
            }
            
            // تطبيق الفلاتر
            if ($request->filled('moh_id') && $request->moh_id != '' && $request->moh_id != 'all') {
                $query->where('fcks.moh_id', $request->moh_id);
            }
            
            if ($request->filled('fctype_id') && $request->fctype_id != '' && $request->fctype_id != 'all') {
                $query->where('fcks.fctypesid', $request->fctype_id);
            }
            
            $institutions = $query->orderBy('mohs.mohname')
                ->orderBy('fcks.Fckname')
                ->get();
            
            return response()->json([
                'institutions' => $institutions,
                'total' => $institutions->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getInstitutionsWithoutUsers: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'حدث خطأ أثناء تحميل البيانات',
                'message' => config('app.debug') ? $e->getMessage() : 'خطأ في السيرفر'
            ], 500);
        }
    }
}
