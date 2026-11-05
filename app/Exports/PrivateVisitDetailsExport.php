<?php

namespace App\Exports;

use App\Models\PrivateVisit;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PrivateVisitDetailsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $request;
    protected $rowCount = 0;

    public function __construct(Request $request = null)
    {
        $this->request = $request ?: request();
    }

    public function collection()
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $userRoles = [];
        $isAdmin = false;
        if ($currentUser) {
            $userRoles = is_array($currentUser->roles_name) ? $currentUser->roles_name : (is_string($currentUser->roles_name) ? json_decode($currentUser->roles_name, true) : []);
            $isAdmin = (is_array($userRoles) && in_array('Admin', $userRoles)) || (method_exists($currentUser, 'hasRole') && $currentUser->hasRole('Admin')) || (is_array($userRoles) && in_array('mohst', $userRoles));
        }

        $query = PrivateVisit::with(['serviceSpecialization', 'doctor']);

        // تقييد الصلاحيات
        if ($currentUser && !$isAdmin) {
            if (is_array($userRoles) && in_array('stat-doh', $userRoles)) {
                $query->where('moh_id', $currentUser->mohcode);
            } else {
                $query->where('fck_id', $currentUser->fckid);
            }
        }

        if ($this->request->filled('moh_id')) {
            $query->where('moh_id', $this->request->moh_id);
        }
        if ($this->request->filled('fck_id')) {
            $query->where('fck_id', $this->request->fck_id);
        }
        if ($this->request->filled('patient_name')) {
            $name = $this->request->patient_name;
            $query->where('visitor_name', 'like', "%$name%");
        }
        if ($this->request->filled('receipt_number')) {
            $query->where('receipt_number', 'like', '%' . $this->request->receipt_number . '%');
        }
        if ($this->request->filled('payment_type')) {
            $query->where('payment_type', $this->request->payment_type);
        }
        if ($this->request->filled('doctor_id')) {
            $query->where('doctor_id', $this->request->doctor_id);
        }
        if ($this->request->filled('service_id')) {
            $query->where('service_id', $this->request->service_id);
        }
        if ($this->request->filled('status')) {
            if ($this->request->status === 'active') {
                $query->whereNull('cancelled_at');
            } elseif ($this->request->status === 'cancelled') {
                $query->whereNotNull('cancelled_at');
            }
        }

        // فلترة الفترة الزمنية
        $dateFrom = $this->request->input('date_from', $this->request->input('receipt_date_from', $this->request->input('created_at_from')));
        $dateTo = $this->request->input('date_to', $this->request->input('receipt_date_to', $this->request->input('created_at_to')));

        if ($dateFrom && $dateTo) {
            $query->where(function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('visit_date', [$dateFrom, $dateTo])
                  ->orWhereBetween('receipt_date', [$dateFrom, $dateTo]);
            });
        } elseif ($dateFrom) {
            $query->where(function($q) use ($dateFrom) {
                $q->whereDate('visit_date', '>=', $dateFrom)
                  ->orWhereDate('receipt_date', '>=', $dateFrom);
            });
        } elseif ($dateTo) {
            $query->where(function($q) use ($dateTo) {
                $q->whereDate('visit_date', '<=', $dateTo)
                  ->orWhereDate('receipt_date', '<=', $dateTo);
            });
        }

        $visits = $query->orderBy('id', 'desc')->get();
        $this->rowCount = $visits->count();

        $data = [];
        $serial = 1;
        foreach ($visits as $visit) {
            $data[] = [
                'تسلسل' => $serial++,
                'الترقيم السنوي' => $visit->annual_number ?: '-',
                'الترقيم التسلسلي' => $visit->sequential_number ?: '-',
                'اسم المريض / الزائر' => $visit->visitor_name ?? $visit->patient_name ?? '-',
                'الجنسية' => $visit->visitor_nationality ?? $visit->patient_nationality ?? 'عراقي',
                'رقم الهاتف' => $visit->visitor_phone ?? $visit->patient_phone ?? '-',
                'المستشفى' => $visit->hospital ?? '-',
                'دائرة الصحة' => $visit->health_department ?? '-',
                'الخدمة المقدمة' => $visit->serviceSpecialization ? $visit->serviceSpecialization->namesv : '-',
                'الطبيب المعالج / الأخصائي' => $visit->doctor ? $visit->doctor->name : '-',
                'تاريخ الزيارة' => $visit->visit_date ? $visit->visit_date->format('Y-m-d') : '-',
                'وقت الزيارة' => $visit->visit_time ? (is_string($visit->visit_time) ? substr($visit->visit_time, 0, 5) : $visit->visit_time->format('H:i')) : '-',
                'رقم الوصل' => $visit->receipt_number ?: '-',
                'تاريخ الوصل' => $visit->receipt_date ? $visit->receipt_date->format('Y-m-d') : ($visit->visit_date ? $visit->visit_date->format('Y-m-d') : '-'),
                'طريقة الدفع' => $visit->payment_type ?: 'كاش',
                'أجور الخدمة' => (float)($visit->service_fee ?? 0),
                'نسبة الفائدة %' => (float)($visit->interest_rate ?? 0),
                'مبلغ الفائدة' => (float)($visit->interest_amount ?? 0),
                'المبلغ الإجمالي' => (float)($visit->total_amount ?? $visit->service_fee ?? 0),
                'حالة القيد' => $visit->cancelled_at ? 'ملغي' : 'فعال',
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'ت',
            'الترقيم السنوي',
            'الترقيم التسلسلي',
            'اسم المريض / الزائر',
            'الجنسية',
            'رقم الهاتف',
            'المستشفى',
            'دائرة الصحة',
            'الخدمة المقدمة',
            'الطبيب المعالج',
            'تاريخ الزيارة',
            'وقت الزيارة',
            'رقم الوصل',
            'تاريخ الوصل',
            'طريقة الدفع',
            'أجور الخدمة (د.ع)',
            'نسبة العمولة %',
            'مبلغ العمولة (د.ع)',
            'المبلغ الإجمالي (د.ع)',
            'حالة القيد',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '1E40AF']], // أزرق رسمي
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // ت
            'B' => 14,  // الترقيم السنوي
            'C' => 14,  // الترقيم التسلسلي
            'D' => 26,  // اسم المريض
            'E' => 12,  // الجنسية
            'F' => 16,  // الهاتف
            'G' => 24,  // المستشفى
            'H' => 20,  // دائرة الصحة
            'I' => 24,  // الخدمة
            'J' => 22,  // الطبيب
            'K' => 14,  // تاريخ الزيارة
            'L' => 12,  // وقت الزيارة
            'M' => 14,  // رقم الوصل
            'N' => 14,  // تاريخ الوصل
            'O' => 12,  // طريقة الدفع
            'P' => 18,  // أجور الخدمة
            'Q' => 14,  // نسبة العمولة
            'R' => 18,  // مبلغ العمولة
            'S' => 20,  // المبلغ الإجمالي
            'T' => 12,  // حالة القيد
        ];
    }

    public function title(): string
    {
        return 'تقرير الزيارات المعتمد';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // تفعيل الاتجاه من اليمين إلى اليسار
                $sheet->setRightToLeft(true);

                $lastRow = $this->rowCount + 1; // صف الترويسة + البيانات

                // تنسيق كل الخلايا
                $cellRange = 'A1:T' . $lastRow;
                $sheet->getStyle($cellRange)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // اسم المريض محاذاة لليمين
                $sheet->getStyle('D2:D' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // حدود كاملة للجدول
                $sheet->getStyle($cellRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setARGB('CBD5E1');

                // إضافة سطر المجاميع النهائي
                if ($this->rowCount > 0) {
                    $totalRow = $lastRow + 1;
                    $sheet->setCellValue('A' . $totalRow, 'المجموع الكلي:');
                    $sheet->mergeCells("A{$totalRow}:O{$totalRow}");

                    // معادلات الجمع التلقائية
                    $sheet->setCellValue('P' . $totalRow, "=SUM(P2:P{$lastRow})");
                    $sheet->setCellValue('R' . $totalRow, "=SUM(R2:R{$lastRow})");
                    $sheet->setCellValue('S' . $totalRow, "=SUM(S2:S{$lastRow})");

                    // تنسيق سطر المجاميع
                    $sheet->getStyle("A{$totalRow}:T{$totalRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => '0F172A'], 'size' => 12],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'F1F5F9']],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['argb' => '94A3B8'],
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ]
                    ]);

                    // تنسيق الأرقام والعملة
                    $sheet->getStyle("P2:P{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("R2:R{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("S2:S{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
                }
            },
        ];
    }
}
