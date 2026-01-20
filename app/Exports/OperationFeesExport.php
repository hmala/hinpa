<?php

namespace App\Exports;

use App\Models\PrivateWingService;
use App\Models\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OperationFeesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $operationDegrees;
    protected $uniqueServices;
    protected $month;
    protected $year;
    protected $operationFeesByDegree;

    public function __construct($month = null, $year = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->operationDegrees = ['الخاصة', 'فوق الكبرى', 'كبرى', 'متوسطة', 'صغرى'];

        // Get operation fees grouped by operation degree
        $operationFeesByDegree = [];

        foreach ($this->operationDegrees as $degree) {
            $query = PrivateWingService::with(['service' => function($query) {
                $query->with('typeSpecialization');
            }])
            ->where('operation_degree', $degree)
            ->whereHas('service', function($query) {
                $query->where('namesv', 'like', '%عملية%')
                      ->orWhere('namesv', 'like', '%جراحة%')
                      ->orWhere('namesv', 'like', '%عمليات%');
            });

            // إضافة فلتر الشهر والسنة
            if ($this->month && $this->year) {
                $query->whereHas('privateWing', function($q) {
                    $q->whereMonth('entry_date', $this->month)
                      ->whereYear('entry_date', $this->year);
                });
            } elseif ($this->year) {
                $query->whereHas('privateWing', function($q) {
                    $q->whereYear('entry_date', $this->year);
                });
            } elseif ($this->month) {
                $query->whereHas('privateWing', function($q) {
                    $q->whereMonth('entry_date', $this->month);
                });
            }

            $operationFeesByDegree[$degree] = $query->selectRaw('service_id, service_fee, COUNT(*) as count, SUM(total_amount) as total_amount')
                ->groupBy('service_id', 'service_fee')
                ->get()
                ->keyBy('service_id');
        }

        // Get all unique services across all degrees
        $allServices = collect();
        foreach ($operationFeesByDegree as $degree => $fees) {
            $allServices = $allServices->merge($fees);
        }
        $this->uniqueServices = $allServices->unique('service_id')->sortBy(function($service) {
            return $service->service->namesv ?? '';
        });

        // Store the operation fees for use in collection method
        $this->operationFeesByDegree = $operationFeesByDegree;
    }

    /**
     * الحصول على البيانات
     */
    public function collection()
    {
        $data = [];

        foreach ($this->uniqueServices as $service) {
            $row = [
                'اسم العملية' => $service->service->namesv ?? 'غير محدد',
            ];

            $rowTotal = 0;

            foreach ($this->operationDegrees as $degree) {
                $feeData = $this->operationFeesByDegree[$degree][$service->service_id] ?? null;
                if ($feeData) {
                    $row[$degree] = $feeData->service_fee;
                    $rowTotal += $feeData->service_fee;
                } else {
                    $row[$degree] = 0;
                }
            }

            $row['المجموع الكلي'] = $rowTotal;

            $data[] = $row;
        }

        // إضافة صف المجاميع الكلية
        $totalsRow = [
            'اسم العملية' => 'المجموع الكلي',
        ];

        $grandTotal = 0;
        foreach ($this->operationDegrees as $degree) {
            $totalAmount = $this->operationFeesByDegree[$degree]->sum('service_fee');
            $totalsRow[$degree] = $totalAmount;
            $grandTotal += $totalAmount;
        }
        $totalsRow['المجموع الكلي'] = $grandTotal;

        $data[] = $totalsRow;

        return collect($data);
    }

    /**
     * عناوين الأعمدة
     */
    public function headings(): array
    {
        $headings = [
            'اسم العملية',
        ];

        foreach ($this->operationDegrees as $degree) {
            $headings[] = $degree;
        }

        $headings[] = 'المجموع الكلي';

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
            // تنسيق صف المجاميع
            $this->uniqueServices->count() + 2 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'argb' => 'FFFFFFFF',
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FF217346',
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
            'A' => 30, // اسم العملية
        ];

        $columnLetters = range('B', 'Z');
        $index = 0;

        foreach ($this->operationDegrees as $degree) {
            if ($index < count($columnLetters)) {
                $widths[$columnLetters[$index]] = 15;
            }
            $index++;
        }

        // عمود المجموع الكلي
        if ($index < count($columnLetters)) {
            $widths[$columnLetters[$index]] = 18;
        }

        return $widths;
    }

    /**
     * عنوان الورقة
     */
    public function title(): string
    {
        return 'تفاصيل أجور العمليات';
    }
}