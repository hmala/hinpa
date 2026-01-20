<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\StatisticsController;
use App\Exports\StatisticsExport;
use Illuminate\Http\Request;

echo "=== اختبار عملية التصدير بشكل مباشر ===\n";

// إنشاء طلب اختبار
$request = new Request();
$request->merge([
    'year' => 2025,
    'moh_id' => 'all'
]);

// استدعاء الكونترولر
$statisticsController = new StatisticsController();

try {
    echo "1. الحصول على البيانات للتصدير...\n";
    
    // الحصول على البيانات عبر getComparisonData (نفس المنطق المُستخدم في التصدير)
    $comparisonResponse = $statisticsController->getComparisonData($request);
    $statisticsData = $comparisonResponse->getData(true);
    
    echo "2. تحليل البيانات المُستلمة:\n";
    echo "   - نوع التقرير: {$statisticsData['type']}\n";
    echo "   - السنة: {$statisticsData['year']}\n";
    echo "   - عدد الأشهر: " . count($statisticsData['monthly_data']) . "\n";
    
    // تحليل الشهر الأول
    $firstMonth = $statisticsData['monthly_data'][0];
    echo "   - الشهر الأول: {$firstMonth['month_name']}\n";
    echo "   - عدد الدوائر: " . count($firstMonth['moh_data']) . "\n";
    
    // عدّ الدوائر التي لديها بيانات فعلية
    $mohsWithData = 0;
    $totalBeds = 0;
    foreach($firstMonth['moh_data'] as $mohData) {
        // البيانات تأتي كـ array بعد getData(true)
        if (isset($mohData['total_beds']) && $mohData['total_beds'] > 0) {
            $mohsWithData++;
            $totalBeds += $mohData['total_beds'];
        }
    }
    echo "   - دوائر لديها بيانات: {$mohsWithData}\n";
    echo "   - إجمالي الأسرة: {$totalBeds}\n";
    
    echo "\n3. إنشاء Export Object...\n";
    $export = new StatisticsExport($statisticsData, 'اختبار الإحصائيات');
    
    echo "4. اختبار ورقة الأسرة...\n";
    $sheets = $export->sheets();
    $familySheet = $sheets[0]; // أول ورقة (إحصائيات الأسرة)
    
    echo "   - اسم الورقة: " . $familySheet->title() . "\n";
    
    $headings = $familySheet->headings();
    echo "   - عناوين الأعمدة: " . implode(', ', array_slice($headings, 0, 5)) . "...\n";
    
    $data = $familySheet->array();
    echo "   - عدد الصفوف: " . count($data) . "\n";
    
    // تحليل البيانات المُصدَّرة بشكل تفصيلي
    foreach($data as $index => $row) {
        echo "   - صف {$index} ({$row[0]}): ";
        for($i = 1; $i < min(5, count($row)); $i++) {
            echo $row[$i] . ", ";
        }
        echo "\n";
    }
    
    echo "\n5. فحص البيانات الخام الأساسية...\n";
    // نتأكد من أن البيانات المتاحة للورقة صحيحة
    if (isset($statisticsData['monthly_data'][0]['moh_data'])) {
        $firstMohData = $statisticsData['monthly_data'][0]['moh_data'];
        $bedsSum = array_sum(array_column($firstMohData, 'total_beds'));
        echo "   - مجموع الأسرة من البيانات الخام: {$bedsSum}\n";
        
        // التحقق من استخراج البيانات في FamilyStatisticsSheet
        echo "\n6. محاكاة استخراج البيانات في FamilyStatisticsSheet:\n";
        
        $monthData = $statisticsData['monthly_data'][0];
        $value = 0;
        
        if (isset($monthData['families'])) {
            $value = $monthData['families']['total_beds'] ?? 0;
            echo "   - من families: {$value}\n";
        } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
            $mohDataArray = $monthData['moh_data'];
            $bedsArray = array_column($mohDataArray, 'total_beds');
            $value = array_sum($bedsArray);
            echo "   - من moh_data (array_sum): {$value}\n";
            echo "   - عدد عناصر moh_data: " . count($mohDataArray) . "\n";
            echo "   - نوع moh_data: " . gettype($monthData['moh_data']) . "\n";
            
            // تجربة مختلفة للاستخراج
            $alternativeSum = 0;
            foreach($monthData['moh_data'] as $moh) {
                $alternativeSum += $moh['total_beds'] ?? 0;
            }
            echo "   - مجموع بديل (foreach): {$alternativeSum}\n";
        }
    }
    
} catch (Exception $e) {
    echo "خطأ: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
