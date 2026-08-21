<?php

namespace App\Imports;

use App\Models\Service;
use App\Models\TypeSpecialization;
use App\Models\Service_Specialization;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;

class ServiceSpecializationImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }    public function model(array $row)
    {
        try {
            // تجاهل الصفوف الفارغة
            if (empty($row['service_id']) || empty($row['type_specializations_id']) || empty($row['codesv'])) {
                Log::info('تم تجاهل صف فارغ: ' . json_encode($row));
                return null;
            }

            // البحث عن الخدمة؛ حاول إيجاد الخدمة أولاً بالـ id ثم بـ sercode
            $serviceId = trim($row['service_id']);
            $service = Service::find($serviceId);

            if (!$service) {
                // محاولة البحث بواسطة sercode إن وُجد
                if (!empty($row['sercode'])) {
                    $service = Service::where('sercode', trim($row['sercode']))->first();
                }
            }

            // إذا لم توجد الخدمة بعد ذلك، وإذا كانت البيانات كافية فأنشئها
            if (!$service) {
                if (!empty($row['sercode']) && !empty($row['sername'])) {
                    $service = Service::create([
                        'sercode' => trim($row['sercode']),
                        'sername' => trim($row['sername']),
                        'type_specializations_id' => !empty($row['type_specializations_id']) ? trim($row['type_specializations_id']) : null
                    ]);
                    Log::info('تم إنشاء خدمة جديدة أثناء الاستيراد:', ['service_id' => $service->id, 'sercode' => $service->sercode]);
                } else {
                    // إذا لم تتوفر بيانات كافية لإنشاء الخدمة فنتجاهل الصف مع تسجيل تحذير
                    Log::warning('خدمة غير موجودة ولم تتوفر بيانات كافية للإنشاء، تم تجاهل الصف: ' . json_encode($row));
                    return null;
                }
            }

            // البحث عن التخصص أو إنشاؤه إذا لم يكن موجوداً
            $specialization = TypeSpecialization::firstOrCreate(                ['id' => trim($row['type_specializations_id'])]
            );

            if ($service && $specialization) {
                // تنظيف وتحضير البيانات
                $codesv = trim($row['codesv']);
                $namesv = !empty($row['namesv']) ? trim($row['namesv']) : '';
                $price = !empty($row['price']) ? $this->cleanPrice($row['price']) : 0;
                $notes = !empty($row['notes']) ? trim($row['notes']) : '';

                Log::info('جاري معالجة السجل:', [
                    'codesv' => $codesv,
                    'namesv' => $namesv,
                    'price' => $price,
                    'service_id' => $service->id,
                    'type_specialization_id' => $specialization->id
                ]);

                $result = Service_Specialization::updateOrCreate(
                    [
                        'codesv' => $codesv,
                        'service_id' => $service->id,
                        'type_specializations_id' => $specialization->id
                    ],
                    [
                        'namesv' => $namesv,
                        'price' => $price,
                        'notes' => $notes
                    ]
                );

                Log::info('تم حفظ السجل بنجاح:', ['id' => $result->id]);
                return $result;
            }
        } catch (\Exception $e) {
            Log::error('خطأ في استيراد السجل: ' . json_encode($row) . ' الخطأ: ' . $e->getMessage());
            throw $e; // رمي الخطأ للتعامل معه في المستوى الأعلى
        }
        
        return null;
    }

    private function cleanPrice($price)
    {
        if (is_string($price)) {
            // إزالة أي فراغات
            $price = trim($price);
            // إزالة أي فواصل آلاف
            $price = str_replace(',', '', $price);
            // إزالة أي رموز عملة أو نصوص
            $price = preg_replace('/[^0-9.]/', '', $price);
        }
        return floatval($price) ?: 0;
    }
}
