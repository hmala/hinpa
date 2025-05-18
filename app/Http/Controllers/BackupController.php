<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    function __construct()
    {
    $this->middleware('permission:النسخ الاحتياطي', ['only' => ['backupForUser']]);
   
    
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
    
        } catch (Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء إنشاء النسخة الاحتياطية: ' . $e->getMessage()], 500);
        }
    }
    
}
