<?php

namespace App\Exports;

use App\Models\PrivateWing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PrivateWingDetailsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $usedServices;
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request ?: request();
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $userMohId = $currentUser->mohcode;
        $userFckId = $currentUser->fckid;

        $usedServicesQuery = \App\Models\PrivateWingService::with('service')->select('service_id');

        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            if ($this->request->filled('moh_id')) {
                $usedServicesQuery->whereHas('privateWing', function ($q) {
                    $q->where('moh_id', $this->request->moh_id);
                });
            }
            if ($this->request->filled('fck_id')) {
                $usedServicesQuery->whereHas('privateWing', function ($q) {
                    $q->where('fck_id', $this->request->fck_id);
                });
            }
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            $usedServicesQuery->whereHas('privateWing', function ($q) use ($userMohId) {
                $q->where('moh_id', $userMohId);
            });
            if ($this->request->filled('fck_id')) {
                $usedServicesQuery->whereHas('privateWing', function ($q) {
                    $q->where('fck_id', $this->request->fck_id);
                });
            }
        } else {
            $usedServicesQuery->whereHas('privateWing', function ($q) use ($userFckId) {
                $q->where('fck_id', $userFckId);
            });
        }

        $this->usedServices = $usedServicesQuery
            ->distinct()
            ->get()
            ->pluck('service.namesv', 'service_id')
            ->filter()
            ->sort();
    }

    /**
     * الحصول على البيانات
     */
    public function collection()
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $userMohId = $currentUser->mohcode;
        $userFckId = $currentUser->fckid;

        $privateWingsQuery = PrivateWing::with(['receipts', 'privateWingServices.service', 'privateWingServices.doctor', 'privateWingServices.service.typeSpecialization'])
            ->select([
                'id',
                'patient_name',
                'file_number',
                'deposit_amount',
                'total_amount',
                'entry_date',
                'exit_date',
                'days_count',
                'is_insured'
            ])->orderBy('created_at', 'desc');

        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            if ($this->request->filled('moh_id')) {
                $privateWingsQuery->where('moh_id', $this->request->moh_id);
            }
            if ($this->request->filled('fck_id')) {
                $privateWingsQuery->where('fck_id', $this->request->fck_id);
            }
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            $privateWingsQuery->where('moh_id', $userMohId);
            if ($this->request->filled('fck_id')) {
                $privateWingsQuery->where('fck_id', $this->request->fck_id);
            }
        } else {
            $privateWingsQuery->where('fck_id', $userFckId);
        }

        // Apply remaining filters from request
        if ($this->request->filled('patient_name')) {
            $privateWingsQuery->where('patient_name', 'like', '%' . $this->request->patient_name . '%');
        }
        if ($this->request->filled('file_number')) {
            $privateWingsQuery->where('file_number', 'like', '%' . $this->request->file_number . '%');
        }
        if ($this->request->filled('entry_date_from')) {
            $privateWingsQuery->whereDate('entry_date', '>=', $this->request->entry_date_from);
        }
        if ($this->request->filled('entry_date_to')) {
            $privateWingsQuery->whereDate('entry_date', '<=', $this->request->entry_date_to);
        }
        if ($this->request->filled('month')) {
            $privateWingsQuery->whereMonth('entry_date', $this->request->month);
        }
        if ($this->request->filled('year')) {
            $privateWingsQuery->whereYear('entry_date', $this->request->year);
        }

        $privateWings = $privateWingsQuery->get();

        $data = [];

        foreach ($privateWings as $wing) {
            // معلومات الجناح الأساسية
            $row = [
                'رقم تسلسلي' => $wing->id,
                'اسم المريض' => $wing->patient_name,
                'رقم الحساب' => $wing->file_number,
                'مبلغ التأمينات' => $wing->deposit_amount,
                'مجموع الخدمات' => $wing->total_amount,
                'المبلغ المتبقي' => $wing->deposit_amount - $wing->total_amount,
                'أجور الأطباء' => $wing->total_amount,
            ];

            // إضافة رسوم الخدمات كأعمدة منفصلة مع اسم الطبيب
            // استخدم PrivateWingService لكي نستطيع الوصول إلى الطبيب المرتبط
            $wingServices = $wing->privateWingServices->keyBy('service_id');
            foreach ($this->usedServices as $serviceId => $serviceName) {
                if (isset($wingServices[$serviceId])) {
                    $service = $wingServices[$serviceId];
                    $row[$serviceName] = $service->service_fee;
                } else {
                    $row[$serviceName] = '-';
                }
            }
            $doctors = $wing->privateWingServices->pluck('doctor.name')->filter()->unique()->implode(', ');
            $row['الأطباء'] = $doctors ?: '-';

            // إضافة 5% رسوم عملية
            $row['5% رسوم عملية'] = number_format($wing->total_amount * 0.05, 2);

            $data[] = $row;
        }

        return collect($data);
    }

    /**
     * عناوين الأعمدة
     */
    public function headings(): array
    {
        $headings = [
            'رقم تسلسلي',
            'اسم المريض',
            'رقم الحساب',
            'مبلغ التأمينات',
            'مجموع الخدمات',
            'المبلغ المتبقي',
            'أجور الأطباء',
        ];

        // إضافة عناوين الخدمات
        foreach ($this->usedServices as $serviceName) {
            $headings[] = $serviceName;
        }
        $headings[] = 'الأطباء';

        // إضافة 5% رسوم عملية
        $headings[] = '5% رسوم عملية';

        return $headings;
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
        $widths = [
            'A' => 15, // رقم تسلسلي
            'B' => 25, // اسم المريض
            'C' => 20, // رقم الحساب
            'D' => 18, // مبلغ التأمينات
            'E' => 18, // مجموع الخدمات
            'E' => 18, // المبلغ المتبقي
            'F' => 18, // أجور الأطباء
        ];

        // إضافة عرض لأعمدة الخدمات
        $columnLetters = range('G', 'Z');
        $index = 0;
        foreach ($this->usedServices as $serviceName) {
            if ($index < count($columnLetters)) {
                $widths[$columnLetters[$index]] = 18; // عرض للخدمة
            }
            $index++;
        }
        if ($index < count($columnLetters)) {
            $widths[$columnLetters[$index]] = 25; // عرض للأطباء
        }
        $index++;
        if ($index < count($columnLetters)) {
            $widths[$columnLetters[$index]] = 15; // عرض للـ5%
        }

        // إضافة عرض لعمود 5%
        if ($index < count($columnLetters)) {
            $widths[$columnLetters[$index]] = 15;
        }

        return $widths;
    }

    /**
     * عنوان الورقة
     */
    public function title(): string
    {
        return 'تفاصيل الأجنحة الخاصة';
    }
}