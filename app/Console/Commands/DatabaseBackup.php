<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--compress : Compress the backup file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إنشاء نسخة احتياطية من قاعدة البيانات';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('بدء إنشاء النسخة الاحتياطية...');

        try {
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
            $mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
            $command = sprintf(
                '"%s" --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers %s > "%s"',
                $mysqldumpPath,
                $dbUsername,
                $dbPassword,
                $dbHost,
                $dbPort,
                $dbName,
                $filePath
            );
            
            $this->info('تشغيل أمر النسخ الاحتياطي...');
            
            // تشغيل الأمر
            $output = null;
            $returnCode = null;
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && File::exists($filePath)) {
                $fileSize = File::size($filePath);
                $this->info("تم إنشاء النسخة الاحتياطية بنجاح!");
                $this->info("اسم الملف: {$fileName}");
                $this->info("حجم الملف: " . $this->formatBytes($fileSize));
                
                // ضغط الملف إذا طُلب ذلك
                if ($this->option('compress')) {
                    $this->info('بدء ضغط الملف...');
                    $compressedFile = $filePath . '.gz';
                    
                    $fp_out = gzopen($compressedFile, 'wb9');
                    $fp_in = fopen($filePath, 'rb');
                    
                    while (!feof($fp_in)) {
                        gzwrite($fp_out, fread($fp_in, 1024 * 512));
                    }
                    
                    fclose($fp_in);
                    gzclose($fp_out);
                    
                    // حذف الملف الأصلي
                    File::delete($filePath);
                    
                    $compressedSize = File::size($compressedFile);
                    $this->info("تم ضغط الملف بنجاح!");
                    $this->info("حجم الملف المضغوط: " . $this->formatBytes($compressedSize));
                    $this->info("نسبة الضغط: " . round((($fileSize - $compressedSize) / $fileSize) * 100, 2) . '%');
                }
                
                return Command::SUCCESS;
            } else {
                $this->error('فشل في إنشاء النسخة الاحتياطية');
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('خطأ: ' . $e->getMessage());
            return Command::FAILURE;
        }
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
