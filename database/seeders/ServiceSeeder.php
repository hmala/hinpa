<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            ['sercode' => 2, 'sername' => 'اجور تغذية'],
            ['sercode' => 3, 'sername' => 'اجور زيارات علاجية'],
            ['sercode' => 4, 'sername' => 'اجور عمليات'],
            ['sercode' => 5, 'sername' => 'اجورالخدمات الطبية المقدمة للعرب والأجانب غير المقيمين'],
            ['sercode' => 6, 'sername' => 'ادوية'],
            ['sercode' => 7, 'sername' => 'اسنان'],
            ['sercode' => 8, 'sername' => 'اشعة'],
            ['sercode' => 9, 'sername' => 'اطفال'],
            ['sercode' => 10, 'sername' => 'الأنف والأذن والحنجرة'],
            ['sercode' => 11, 'sername' => 'الجلدية'],
            ['sercode' => 12, 'sername' => 'العلاج الطبيعي'],
            ['sercode' => 13, 'sername' => 'العيون'],
            ['sercode' => 14, 'sername' => 'الكلية والمجاري البولية'],
            ['sercode' => 15, 'sername' => 'تغذية'],
            ['sercode' => 16, 'sername' => 'تمريض وتداوي'],
            ['sercode' => 17, 'sername' => 'خدمات ادارية'],
            ['sercode' => 18, 'sername' => 'خدمات تداوي'],
            ['sercode' => 19, 'sername' => 'خدمات فندقية'],
            ['sercode' => 20, 'sername' => 'طب الاسنان'],
            ['sercode' => 21, 'sername' => 'فحوصات مختبرية'],
            ['sercode' => 22, 'sername' => 'فحوصات وخدمات متفرقة'],
            ['sercode' => 23, 'sername' => 'كسور'],
            ['sercode' => 24, 'sername' => 'مستلزمات مواد'],
            ['sercode' => 25, 'sername' => 'نفسية'],
            ['sercode' => 26, 'sername' => 'نقل'],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
