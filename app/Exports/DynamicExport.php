<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\mohs;
use Illuminate\Support\Facades\Auth;

class DynamicExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $tableName;
    protected $circle;
    protected $columns;
    protected $user;

    /**
     * قائمة الجداول المسموحة للتصدير
     */
    public static function allowedTables()
    {
        return [
            'pations',
            'hwadths',
            'cases',
            'salats',
            'surgeries',
        ];
    }

    /**
     * قائمة الأعمدة المستبعدة من التصدير
     */
    public static function excludedColumns()
    {
        return [
            'created_at',
            'fctypesid',
            'fapprove',
            'Approv_user',
            'f_approve',
            'hfcode',
            'status_value',
            'status',
            'Approv_dt',
            'Created_by',
            'creator',
            'save',
            'updated_at',
            'user_id',
            'created_by',
            'updated_by',
            'deleted_at',
            'password',
            'remember_token',
            'email_verified_at'

            
        ];
    }
    
    /**
     * التحقق إذا كان الجدول مسموح به للتصدير
     */
    public static function isAllowedTable($tableName)
    {
        return in_array($tableName, self::allowedTables());
    }

    public function __construct($tableName, $circle = null, $columns = [])
    {
        $this->tableName = $tableName;
        $this->circle = $circle;
        $this->columns = $columns;
        $this->user = Auth::user();
        
        // تسجيل المعلومات للتشخيص
        Log::info('إنشاء DynamicExport', [
            'table' => $tableName,
            'circle' => $circle,
            'columns' => $columns,
            'columns_count' => count($columns),
            'user_role' => $this->user ? $this->user->role : 'غير مسجل دخول'
        ]);
    }

    /**
     * الحصول على البيانات
     */
    public function collection()
    {
        try {
            Log::info('بدء جمع البيانات', [
                'table' => $this->tableName,
                'circle' => $this->circle,
                'columns' => $this->columns
            ]);

            $query = DB::table($this->tableName);

            // تطبيق فلتر الصلاحيات حسب دور المستخدم
            if ($this->user) {
                if ($this->user->role === 'user') {
                    // مستخدم دائرة - يستطيع فقط رؤية بيانات دائرته
                    $circleColumn = $this->getCircleColumn($this->tableName);
                    if ($circleColumn) {
                        Log::info('تطبيق فلتر الدائرة حسب صلاحية المستخدم', [
                            'circle_column' => $circleColumn,
                            'user_circle' => $this->user->mohcode
                        ]);
                        $query->where($circleColumn, $this->user->mohcode);
                    }
                } else {
                    // للأدمن و mohst - يمكن تطبيق فلتر الدائرة إذا تم اختياره
                    if ($this->circle && $this->circle !== 'all') {
                        $circleColumn = $this->getCircleColumn($this->tableName);
                        if ($circleColumn) {
                            Log::info('تطبيق فلتر الدائرة للأدمن أو mohst', [
                                'circle_column' => $circleColumn,
                                'circle_value' => $this->circle,
                                'user_role' => $this->user->role
                            ]);
                            $query->where($circleColumn, $this->circle);
                        }
                    }
                }
            }

            // تحديد الأعمدة المطلوبة
            if (!empty($this->columns)) {
                // التحقق من وجود الأعمدة في الجدول واستبعاد الأعمدة غير الضرورية
                $availableColumns = Schema::getColumnListing($this->tableName);
                $validColumns = array_intersect($this->columns, $availableColumns);
                $validColumns = array_diff($validColumns, self::excludedColumns());
                
                Log::info('فحص الأعمدة', [
                    'requested_columns' => $this->columns,
                    'available_columns' => $availableColumns,
                    'valid_columns' => $validColumns,
                    'excluded_columns' => self::excludedColumns()
                ]);

                if (empty($validColumns)) {
                    Log::warning('لا توجد أعمدة صحيحة، سيتم استخدام الأعمدة المتاحة باستثناء المستبعدة');
                    // استخدم جميع الأعمدة المتاحة باستثناء المستبعدة
                    $validColumns = array_diff($availableColumns, self::excludedColumns());
                    $query->select($validColumns);
                } else {
                    $query->select($validColumns);
                }
            }

            $data = $query->get();
            
            // إضافة معلومات الدائرة والمؤسسة للبيانات المصدرة
            $data = $this->addCircleAndInstitutionInfo($data);
            
            Log::info('تم جمع البيانات بنجاح', [
                'record_count' => $data->count()
            ]);

            return $data;

        } catch (\Exception $e) {
            Log::error('خطأ في جمع البيانات', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * عناوين الأعمدة
     */
    public function headings(): array
    {
        try {
            if (!empty($this->columns)) {
                // التأكد من أن الأعمدة صحيحة واستبعاد الأعمدة غير الضرورية
                $availableColumns = Schema::getColumnListing($this->tableName);
                $validColumns = array_intersect($this->columns, $availableColumns);
                $validColumns = array_diff($validColumns, self::excludedColumns());
                
                return array_map(function($column) {
                    return $this->getColumnDisplayName($column);
                }, $validColumns);
            }

            // إذا لم يتم تحديد أعمدة، احصل على جميع الأعمدة باستثناء المستبعدة
            $columns = Schema::getColumnListing($this->tableName);
            $columns = array_diff($columns, self::excludedColumns());
            return array_map(function($column) {
                return $this->getColumnDisplayName($column);
            }, $columns);
        } catch (\Exception $e) {
            Log::error('خطأ في إنشاء عناوين الأعمدة', [
                'message' => $e->getMessage(),
                'table' => $this->tableName,
                'columns' => $this->columns
            ]);
            
            // إرجاع عناوين افتراضية في حالة الخطأ
            return !empty($this->columns) ? $this->columns : ['البيانات'];
        }
    }

    /**
     * تنسيق الورقة
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // تنسيق الصف الأول (العناوين)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => [
                        'argb' => 'FFFFFFFF',
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FF4472C4',
                    ],
                ],
            ],
        ];
    }

    /**
     * عرض الأعمدة
     */
    public function columnWidths(): array
    {
        try {
            $widths = [];
            
            if (!empty($this->columns)) {
                $availableColumns = Schema::getColumnListing($this->tableName);
                $validColumns = array_intersect($this->columns, $availableColumns);
                $columns = $validColumns;
            } else {
                $columns = Schema::getColumnListing($this->tableName);
            }
            
            foreach ($columns as $index => $column) {
                if ($index < 26) { // فقط الحروف A-Z
                    $letter = chr(65 + $index); // A, B, C, etc.
                    $widths[$letter] = 20;
                }
            }
            
            return $widths;
        } catch (\Exception $e) {
            Log::error('خطأ في تحديد عرض الأعمدة', [
                'message' => $e->getMessage(),
                'table' => $this->tableName
            ]);
            
            // إرجاع عرض افتراضي
            return ['A' => 20];
        }
    }

    /**
     * عنوان الورقة
     */
    public function title(): string
    {
        $title = $this->getTableDisplayName($this->tableName);
        
        if ($this->circle && $this->circle !== 'all') {
            $circleInfo = mohs::find($this->circle);
            if ($circleInfo) {
                $title .= ' - ' . $circleInfo->name;
            }
        }
        
        return $title;
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

    /**
     * الحصول على الاسم المعروض للجدول
     */
    private function getTableDisplayName($tableName)
    {
        $displayNames = [
            'pations' => 'الأسرة',
            'hwadths' => 'الحوادث',
            'cases' => 'الاستشارات',
            'salats' => 'الصالات',
            'surgeries' => 'العمليات الجراحية',
        ];
        return $displayNames[$tableName] ?? $tableName;
    }
    
    /**
     * الحصول على أسماء العرض للجداول المسموحة (للواجهة)
     */
    public static function getAllowedTableDisplayNames()
    {
        $displayNames = [
            'pations' => 'الأسرة',
            'hwadths' => 'الحوادث',
            'cases' => 'الاستشارات',
            'salats' => 'الصالات',
            'surgeries' => 'العمليات الجراحية',
        ];
        
        return $displayNames;
    }

    /**
     * التحقق مما إذا كان المستخدم يمكنه اختيار دوائر متعددة
     */
    public static function canSelectMultipleCircles()
    {
        $user = Auth::user();
        return $user && in_array($user->role, ['admin', 'mohst']);
    }

    /**
     * الحصول على الاسم المعروض للعمود
     */
    private function getColumnDisplayName($columnName)
    {
        $columnNames = [
            'id' => 'الرقم التسلسلي',
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'MOH' => 'دائرة الصحة',
            'FCK' => 'المؤسسة',
            'created_at' => 'تاريخ الإنشاء',
            'updated_at' => 'تاريخ التحديث',
            'phone' => 'رقم الهاتف',
            'address' => 'العنوان',
            'status' => 'الحالة',
            'type' => 'النوع',
            'description' => 'الوصف',
            'date' => 'التاريخ',
            'time' => 'الوقت',
            'notes' => 'الملاحظات',
            'patient_name' => 'اسم المريض',
            'doctor_name' => 'اسم الطبيب',
            'department' => 'القسم',
            'ward_type' => 'نوع الردهة',
            'bed_number' => 'رقم السرير',
            'admission_date' => 'تاريخ الدخول',
            'discharge_date' => 'تاريخ الخروج',
            'moh_id' => 'رمز الدائرة',
            'mohcode' => 'رمز الدائرة',
            'moh_name' => 'اسم الدائرة',
            'circle_name' => 'اسم الدائرة',
            'fck_id' => 'رمز المؤسسة',
            'fck_name' => 'اسم المؤسسة',
            'institution_name' => 'اسم المؤسسة',
        ];
        
        return $columnNames[$columnName] ?? $columnName;
    }
    
    /**
     * إضافة معلومات الدائرة والمؤسسة إلى البيانات المصدرة
     */
    private function addCircleAndInstitutionInfo($data)
    {
        // إذا كانت البيانات فارغة، لا داعي للمعالجة
        if ($data->isEmpty()) {
            return $data;
        }
        
        try {
            // الحصول على عمود الدائرة للجدول الحالي
            $circleColumn = $this->getCircleColumn($this->tableName);
            
            // إذا لم يكن هناك عمود للدائرة، أرجع البيانات كما هي
            if (!$circleColumn) {
                return $data;
            }
            
            // جمع كل أكواد الدوائر في البيانات
            $circleIds = $data->pluck($circleColumn)->unique()->filter()->toArray();
            
            // إذا لم تكن هناك أكواد دوائر، أرجع البيانات كما هي
            if (empty($circleIds)) {
                return $data;
            }
            
            // الحصول على معلومات الدوائر من قاعدة البيانات
            $circles = DB::table('mohs')
                        ->whereIn('id', $circleIds)
                        ->select('id', 'mohname')
                        ->get()
                        ->keyBy('id');
            
            // إذا كانت هناك حاجة لمعلومات المؤسسات
            $hasFckId = false;
            if (!empty($data) && count($data) > 0) {
                // تحقق مما إذا كان أي سجل يحتوي على عمود fck_id
                foreach ($data as $item) {
                    if (property_exists($item, 'fck_id') && !is_null($item->fck_id)) {
                        $hasFckId = true;
                        break;
                    }
                }
            }
            
            $institutionsByCircle = [];
            if ($hasFckId) {
                // الحصول على معلومات المؤسسات
                $institutions = DB::table('fck')
                              ->whereIn('moh_id', $circleIds)
                              ->select('id', 'name', 'moh_id')
                              ->get();
                
                // إنشاء مصفوفة للمؤسسات مفهرسة حسب الدائرة
                foreach ($institutions as $institution) {
                    if (!isset($institutionsByCircle[$institution->moh_id])) {
                        $institutionsByCircle[$institution->moh_id] = [];
                    }
                    $institutionsByCircle[$institution->moh_id][$institution->id] = $institution->name;
                }
            }
            
            // تحويل البيانات إلى مصفوفة لسهولة التعديل
            $resultArray = [];
            $currentIndex = 0;
            
            // إضافة معلومات الدائرة والمؤسسة لكل سجل
            foreach ($data as $item) {
                // انشاء نسخة من السجل الأصلي
                $newItem = clone $item;
                $circleId = $item->{$circleColumn} ?? null;
                
                if ($circleId && isset($circles[$circleId])) {
                    // استبدال كود الدائرة باسم الدائرة (اضافة اسم الدائرة مع الكود)
                    if (property_exists($newItem, $circleColumn)) {
                        // إضافة اسم الدائرة في عمود منفصل بدلاً من استبدال الكود
                        $newItem->circle_name = $circles[$circleId]->mohname;
                    }
                    
                    // إضافة معلومات المؤسسة
                    if (property_exists($newItem, 'fck_id') && !is_null($newItem->fck_id) && isset($institutionsByCircle[$circleId][$newItem->fck_id])) {
                        // إضافة اسم المؤسسة في عمود منفصل
                        $newItem->institution_name = $institutionsByCircle[$circleId][$newItem->fck_id];
                    }
                }
                
                $resultArray[$currentIndex] = $newItem;
                $currentIndex++;
            }
            
            // إرجاع البيانات بعد التعديل
            return collect($resultArray);
            
        } catch (\Exception $e) {
            Log::error('خطأ أثناء إضافة معلومات الدائرة والمؤسسة', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // في حالة حدوث خطأ، أرجع البيانات الأصلية
            return $data;
        }
    }
}
