<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Controllers\StatisticsController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatisticsExportTest extends TestCase
{
    public function test_export_statistics_returns_file()
    {
        // محاكاة طلب التصدير
        $request = new Request([
            'moh_id' => 'all',
            'fck_id' => 'all',
            'fck_type_id' => 'all',
            'year' => '2025'
        ]);

        $controller = new StatisticsController();
        
        // اختبار وظيفة getComparisonData أولاً
        $comparisonResponse = $controller->getComparisonData($request);
        
        echo "اختبار getComparisonData:\n";
        echo "نوع الاستجابة: " . get_class($comparisonResponse) . "\n";
        
        if ($comparisonResponse instanceof \Illuminate\Http\JsonResponse) {
            $data = $comparisonResponse->getData(true);
            echo "البيانات موجودة: " . (empty($data) ? "لا" : "نعم") . "\n";
            
            if (!empty($data)) {
                echo "عدد العناصر في البيانات: " . count($data) . "\n";
                
                // عرض بعض البيانات للتأكد
                if (isset($data['family_total_beds'])) {
                    echo "إجمالي الأسرة العائلية: " . $data['family_total_beds'] . "\n";
                }
            }
        }
        
        // اختبار وظيفة التصدير
        echo "\nاختبار exportStatistics:\n";
        
        try {
            $response = $controller->exportStatistics($request);
            echo "نوع استجابة التصدير: " . get_class($response) . "\n";
            
            if ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
                $file = $response->getFile();
                echo "حجم الملف: " . $file->getSize() . " بايت\n";
                echo "اسم الملف: " . $file->getFilename() . "\n";
                echo "نجح التصدير!\n";
                
                // حذف الملف
                unlink($file->getPathname());
                echo "تم حذف الملف.\n";
                
            } else {
                echo "فشل التصدير - نوع غير متوقع\n";
            }
            
        } catch (\Exception $e) {
            echo "خطأ في التصدير: " . $e->getMessage() . "\n";
        }
    }
}
