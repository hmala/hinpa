<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
class PermissionTableSeeder extends Seeder
{
/**
* Run the database seeds.
*
* @return void
*/
public function run()
{


$permissions = [

        'الاسرة',
        'الاسرة موثقة',
        'الاسرة غير الموثقة',
        'توثيق الدائرة',
        'غير موثق من دائرة',
        'الصالات',
        'العمليات',
        'عدد الاسرة حسب كل مؤسسة',
        'أدارة المستخدمين',
        'المستخدمين',
        'ألصلاحيات',
        'ألاعدادات',
        'دوائر الصحة',
        'نوع المؤسسة',
        'المؤسسات',
        'أنواع الردهات',
        'أنواع  العمليات',
        'اخرى',
        'اضافة',
        'تصدير اكسيل',
        'التقارير',
        'ربط الخدمات بالتخصصات.عرض',
        'ربط الخدمات بالتخصصات.اضافة',
        'ربط الخدمات بالتخصصات.تعديل',
        'ربط الخدمات بالتخصصات.حذف',
];



foreach ($permissions as $permission) {
    Permission::firstOrCreate(['name' => $permission]);
}


}
}