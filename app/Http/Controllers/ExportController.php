<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DynamicExport;
use App\Models\mohs;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * عرض صفحة التصدير
     */
    public function index()
    {
        // الحصول على قائمة الجداول
        $tables = $this->getAllTables();
        
        // الحصول على قائمة الدوائر مرتبة أبجدياً
        $circles = mohs::orderBy('mohname', 'asc')->get();
        
        // الحصول على معلومات المستخدم وصلاحياته
        $user = auth()->user();
        $isMohstOrAdmin = $user->hasRole('mohst') || $user->hasRole('admin') || 
                         $user->hasRole('Admin') || $user->hasRole('ADMIN') ||
                         $user->hasRole('Mohst') || $user->hasRole('MOHST');
        $userCircleId = $user->circle_id;
        
        return view('export.index', compact('tables', 'circles', 'isMohstOrAdmin', 'userCircleId', 'user'));

         $this->middleware('permission:تصدير', ['only' => ['index', 'getTableDisplayName', 'export', 'getTableEnglishName', 'getTableColumns', 'getTableEnglishName', 'preview', 'getCircleColumn']]);
    }

    /**
     * الحصول على الجداول المسموحة للتصدير فقط
     */
    private function getAllTables()
    {
        // استخدام قائمة الجداول المسموحة من DynamicExport فقط
        $allowedTables = DynamicExport::allowedTables();
        $tableNames = [];
        
        // الحصول على أسماء العرض للجداول المسموحة من DynamicExport
        $displayNames = DynamicExport::getAllowedTableDisplayNames();
        
        foreach ($allowedTables as $tableName) {
            $tableNames[] = [
                'name' => $tableName,
                'display_name' => $displayNames[$tableName] ?? $this->getTableDisplayName($tableName)
            ];
        }
        
        return $tableNames;
    }

    /**
     * الحصول على الاسم المعروض للجدول
     */
    private function getTableDisplayName($tableName)
    {
        $displayNames = [
            'users' => 'المستخدمين',
            'pations' => 'الأسرة',
            'salats' => 'الصالات',
            'hwadths' => 'الحوادث',
            'cases' => 'الاستشارات',
            'surgeries' => 'العمليات الجراحية',
            'private_wings' => 'الأجنحة الخاصة',
            'mohs' => 'دوائر الصحة',
            'fctypes' => 'أنواع المؤسسات',
            'fck' => 'المؤسسات',
            'rdhs' => 'أنواع الردهات',
            'roles' => 'الأدوار',
            'permissions' => 'الأذونات',
            'model_has_permissions' => 'أذونات النماذج',
            'model_has_roles' => 'أدوار النماذج',
            'role_has_permissions' => 'أذونات الأدوار',
        ];
        
        return $displayNames[$tableName] ?? $tableName;
    }

    /**
     * الحصول على الاسم الإنجليزي للجدول لاسم الملف
     */
    private function getTableEnglishName($tableName)
    {
        $englishNames = [
            'users' => 'users',
            'pations' => 'beds',
            'salats' => 'halls',
            'hwadths' => 'accidents',
            'cases' => 'consultations',
            'surgeries' => 'surgeries',
            'private_wings' => 'private_wings',
            'mohs' => 'health_departments',
            'fctypes' => 'institution_types',
            'fck' => 'institutions',
            'rdhs' => 'ward_types',
            'roles' => 'roles',
            'permissions' => 'permissions',
            'model_has_permissions' => 'model_permissions',
            'model_has_roles' => 'model_roles',
            'role_has_permissions' => 'role_permissions',
        ];
        
        return $englishNames[$tableName] ?? $tableName;
    }

    /**
     * الحصول على أعمدة الجدول
     */
    public function getTableColumns(Request $request)
    {
        $tableName = $request->input('table');
        
        try {
            // التحقق من أن الجدول مسموح به للتصدير
            if (!DynamicExport::isAllowedTable($tableName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'الجدول غير مسموح به للتصدير'
                ]);
            }
            
            // التأكد من وجود الجدول
            if (!Schema::hasTable($tableName)) {
                Log::error('جدول غير موجود في getTableColumns', [
                    'table' => $tableName
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'الجدول غير موجود: ' . $tableName
                ]);
            }
            
            $columns = Schema::getColumnListing($tableName);
            
            // تسجيل بيانات التشخيص
            Log::info('تم جلب أعمدة الجدول', [
                'table' => $tableName,
                'columns_count' => count($columns),
                'columns' => $columns
            ]);
            
            return response()->json([
                'success' => true,
                'columns' => $columns
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في جلب أعمدة الجدول', [
                'table' => $tableName,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'فشل في جلب أعمدة الجدول: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * تصدير البيانات
     */
    public function export(Request $request)
    {
        Log::info('تم استلام طلب التصدير', [
            'table' => $request->input('table'),
            'circle' => $request->input('circle'),
            'columns_count' => is_array($request->input('columns')) ? count($request->input('columns')) : 0
        ]);

        $request->validate([
            'table' => 'required|string',
            'circle' => 'nullable|string',
            'columns' => 'required|array'
        ]);

        $tableName = $request->input('table');
        $circle = $request->input('circle');
        $columns = $request->input('columns');

        try {
            $fileName = $this->getTableEnglishName($tableName);
            if ($circle && $circle !== 'all') {
                $circleInfo = mohs::find($circle);
                if ($circleInfo) {
                    $fileName .= '_circle_' . $circleInfo->mohname; // استبدال الكود بالاسم
                }
            }
            $fileName .= '_' . date('Y-m-d_H-i-s') . '.xlsx';

            Log::info('اسم الملف المُنشأ', ['filename' => $fileName]);

            // تحضير الاستعلام
            $query = DB::table($tableName);

            // تحديد الأعمدة المطلوبة
            if (!empty($columns)) {
                $query->select($columns);
            }

            // إضافة شرط الدائرة إذا كان موجوداً
            if ($circle && $circle !== 'all') {
                $circleColumn = $this->getCircleColumn($tableName);
                if ($circleColumn) {
                    $query->where($circleColumn, $circle);
                }
            }

            // تنفيذ الاستعلام وتحويل البيانات
            $data = $query->get();

            // استبدال كود دائرة الصحة باسمها في البيانات
            if (in_array('moh_id', $columns)) {
                $mohsData = mohs::pluck('mohname', 'id')->toArray();
                $exportData = [];
                
                foreach ($data as $row) {
                    $rowArray = (array) $row;
                    if (isset($rowArray['moh_id']) && isset($mohsData[$rowArray['moh_id']])) {
                        $rowArray['moh_id'] = $mohsData[$rowArray['moh_id']];
                    }
                    $exportData[] = $rowArray;
                }
                $data = collect($exportData);
            }

            // تصدير البيانات باستخدام DynamicExport
            return Excel::download(new DynamicExport($tableName, $circle, $columns, $data->toArray()), $fileName);

        } catch (\Exception $e) {
            Log::error('خطأ في تصدير البيانات', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'table' => $tableName ?? 'unknown',
                'circle' => $circle ?? 'unknown',
                'columns' => $columns ?? []
            ]);
            return response()->json([
                'success' => false,
                'message' => 'فشل في تصدير البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * معاينة البيانات
     */
    public function preview(Request $request)
    {
        $tableName = $request->input('table');
        $circle = $request->input('circle');
        $columns = $request->input('columns', []);

        try {
            $query = DB::table($tableName);

            // تطبيق فلتر الدائرة إذا كان متاحاً
            if ($circle && $circle !== 'all') {
                // تحديد العمود المناسب للفلترة حسب الجدول
                $circleColumn = $this->getCircleColumn($tableName);
                if ($circleColumn) {
                    $query->where($circleColumn, $circle);
                }
            }

            // الحصول على العدد الإجمالي أولاً
            $totalCount = $query->count();

            // تحديد الأعمدة المطلوبة
            if (!empty($columns)) {
                $query->select($columns);
            }

            // الحصول على 10 صفوف للمعاينة
            $data = $query->limit(10)->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'total_count' => $totalCount,
                'preview_count' => $data->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في معاينة البيانات: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * الحصول على عمود الدائرة للجدول
     */
    private function getCircleColumn($tableName)
    {
        $circleColumns = [
            'pations' => 'moh_id',
            'salats' => 'moh_id',
            'hwadths' => 'moh_id',
            'cases' => 'moh_id',
            'surgeries' => 'moh_id',
            'private_wings' => 'moh_id',
            'fctypes' => 'moh_id',
            'fck' => 'moh_id',
            'users' => 'mohcode',
        ];

        return $circleColumns[$tableName] ?? null;
    }
}
