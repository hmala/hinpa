<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:النسخ الاحتياطي', ['only' => ['backupForUser', 'index', 'create', 'download', 'delete']]);
        $this->middleware('auth');
    }

    /**
     * عرض صفحة النسخ الاحتياطية
     */
    public function index()
    {
        $backups = $this->getBackupHistory();
        return view('backup.index', compact('backups'));
    }

    /**
     * إنشاء نسخة احتياطية جديدة باستخدام mysqldump مباشرة
     */
    public function create()
    {
        try {
            // إضافة headers للاستجابة
            ini_set('max_execution_time', 300); // 5 دقائق
            ini_set('memory_limit', '512M');
            
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbPort = env('DB_PORT', '3306');
            $dbName = env('DB_DATABASE');
            $dbUsername = env('DB_USERNAME');
            $dbPassword = env('DB_PASSWORD');
            
            // إنشاء اسم ملف مع التاريخ والوقت
            $fileName = 'database_backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = storage_path('app/backups/' . $fileName);
            
            // التأكد من وجود مجلد النسخ الاحتياطية
            if (!File::exists(storage_path('app/backups'))) {
                File::makeDirectory(storage_path('app/backups'), 0755, true);
            }
            
            // إنشاء أمر mysqldump
            $mysqldumpPath = 'C:\\wamp64\\bin\\mysql\\mysql9.1.0\\bin\\mysqldump.exe';
            
            // التحقق من وجود mysqldump في مسارات مختلفة
            $possiblePaths = [
                'C:\\wamp64\\bin\\mysql\\mysql9.1.0\\bin\\mysqldump.exe',
                'C:\\wamp64\\bin\\mysql\\mysql8.0.39\\bin\\mysqldump.exe',
                'C:\\wamp64\\bin\\mysql\\mysql8.3.0\\bin\\mysqldump.exe',
                'C:\\wamp64\\bin\\mysql\\mysql8.2.12\\bin\\mysqldump.exe',
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'mysqldump' // إذا كان في PATH
            ];
            
            $mysqldumpPath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path) || $path === 'mysqldump') {
                    $mysqldumpPath = $path;
                    break;
                }
            }
            
            // التحقق من وجود mysqldump
            if (!$mysqldumpPath) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على mysqldump في أي من المسارات المتوقعة. يرجى التأكد من تثبيت MySQL بشكل صحيح.'
                ]);
            }
            
            // بناء الأمر مع معالجة خاصة لكلمة المرور الفارغة
            if (empty($dbPassword)) {
                $command = sprintf(
                    '"%s" --user=%s --password= --host=%s --port=%s --single-transaction --routines --triggers --add-drop-table --create-options %s > "%s" 2>&1',
                    $mysqldumpPath,
                    escapeshellarg($dbUsername),
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbName),
                    $filePath
                );
            } else {
                $command = sprintf(
                    '"%s" --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers --add-drop-table --create-options %s > "%s" 2>&1',
                    $mysqldumpPath,
                    escapeshellarg($dbUsername),
                    escapeshellarg($dbPassword),
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbName),
                    $filePath
                );
            }
            
            // تشغيل الأمر
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            // التحقق من النتيجة
            if ($returnCode === 0 && File::exists($filePath) && File::size($filePath) > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء النسخة الاحتياطية بنجاح',
                    'filename' => $fileName,
                    'size' => $this->formatBytes(File::size($filePath))
                ]);
            } else {
                // حذف الملف إذا كان فارغاً أو تالفاً
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                
                $errorMessage = 'فشل في إنشاء النسخة الاحتياطية';
                $debugInfo = [
                    'return_code' => $returnCode,
                    'file_exists' => File::exists($filePath),
                    'file_size' => File::exists($filePath) ? File::size($filePath) : 0,
                    'output' => $output,
                    'command' => $command,
                    'mysqldump_path' => $mysqldumpPath
                ];
                
                if (!empty($output)) {
                    $errorMessage .= ': ' . implode(' ', $output);
                }
                
                // Log التفاصيل للتشخيص
                Log::error('Backup failed', $debugInfo);
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('خطأ في إنشاء النسخة الاحتياطية: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'فشل في إنشاء النسخة الاحتياطية: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTraceAsString() : null
            ]);
        }
    }

    /**
     * تحميل نسخة احتياطية
     */
    public function download($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (File::exists($filePath)) {
            return response()->download($filePath);
        }
        
        return redirect()->back()->with('error', 'الملف غير موجود');
    }

    /**
     * حذف نسخة احتياطية
     */
    public function delete($filename)
    {
        try {
            $filePath = storage_path('app/backups/' . $filename);
            
            if (File::exists($filePath)) {
                File::delete($filePath);
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف النسخة الاحتياطية بنجاح'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'الملف غير موجود'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في حذف النسخة الاحتياطية: ' . $e->getMessage()
            ]);
        }
    }
    public function backupForUser()
    {
        $user = auth()->user(); // الحصول على معلومات المستخدم
        $currentDate = date('Y-m-d_H-i-s'); // إنشاء الطابع الزمني
    
        $backupFilePath = storage_path('backups/' . $user->id . '_backup_' . $currentDate . '.sql'); // اسم الملف مع التاريخ
    
        try {
            $connection = DB::connection()->getPdo();
            $tables = $connection->query("SHOW TABLES")->fetchAll();
    
            $sqlDump = "";
            foreach ($tables as $table) {
                $tableName = $table[0];
                $createTable = $connection->query("SHOW CREATE TABLE $tableName")->fetch();
                $sqlDump .= $createTable['Create Table'] . ";\n\n";
    
                $rows = $connection->query("SELECT * FROM $tableName")->fetchAll();
                foreach ($rows as $row) {
                    $sqlDump .= "INSERT INTO $tableName VALUES (" . implode(',', array_map([$connection, 'quote'], $row)) . ");\n";
                }
                $sqlDump .= "\n\n";
            }
    
            if (!file_exists(storage_path('backups'))) {
                mkdir(storage_path('backups'), 0775, true); // إنشاء مجلد النسخ إذا لم يكن موجودًا
            }
    
            file_put_contents($backupFilePath, $sqlDump); // حفظ النسخة الاحتياطية
            return response()->download($backupFilePath)->deleteFileAfterSend(true); // تحميل الملف وحذفه بعد الإرسال
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء إنشاء النسخة الاحتياطية: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * الحصول على تاريخ النسخ الاحتياطية
     */
    private function getBackupHistory()
    {
        $backupPath = storage_path('app/backups');
        
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
            return [];
        }

        $files = File::allFiles($backupPath);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'date' => Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                'date_ar' => Carbon::createFromTimestamp($file->getMTime())->locale('ar')->format('Y-m-d H:i:s'),
                'path' => $file->getRealPath()
            ];
        }

        // ترتيب حسب التاريخ (الأحدث أولاً)
        usort($backups, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $backups;
    }

    /**
     * تنسيق حجم الملف
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

}
