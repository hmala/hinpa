<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Log;

class StatisticsExport implements WithMultipleSheets
{
    private $data;
    private $title;

    public function __construct($data, $title = 'إحصائيات المقارنات')
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // إنشاء 7 أوراق عمل منفصلة تطابق تنسيق الواجهة تماماً
        $sheets[] = new FamilyStatisticsSheet($this->data, 'إحصائيات الأسرة');
        $sheets[] = new SurgeryStatisticsSheet($this->data, 'إحصائيات العمليات');
        $sheets[] = new IncidentStatisticsSheet($this->data, 'إحصائيات الحوادث والولادات');
        $sheets[] = new ConsultationStatisticsSheet($this->data, 'إحصائيات الاستشارية');
        $sheets[] = new CasesStatisticsSheet($this->data, 'إحصائيات الحالات والفحوصات');
        
        // إضافة الأوراق التفصيلية الجديدة
        $sheets[] = new WardDetailsStatisticsSheet($this->data, 'إحصائيات الردهات التفصيلية');
        $sheets[] = new ConsultationDetailsStatisticsSheet($this->data, 'إحصائيات الاستشارية التفصيلية');
        
        return $sheets;
    }
}

// ورقة إحصائيات الأسرة - تطابق تنسيق الواجهة تماماً
class FamilyStatisticsSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    private $data;
    private $sheetTitle;

    public function __construct($data, $sheetTitle)
    {
        $this->data = $data;
        $this->sheetTitle = $sheetTitle;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function headings(): array
    {
        $headings = ['البيان'];
        
        // إضافة أعمدة الأشهر
        foreach ($this->data['monthly_data'] as $monthData) {
            $headings[] = $monthData['month_name'];
        }
        
        return $headings;
    }

    public function array(): array
    {
        $rows = [];
        
        // صف عدد الوحدات
        $unitsRow = ['عدد الوحدات'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            // تحديد مصدر البيانات حسب نوع التقرير
            if (isset($monthData['families'])) {
                // للتقارير على مستوى مؤسسة واحدة أو دائرة واحدة
                $value = $monthData['families']['total_units'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                // للتقارير العامة - جمع من جميع الدوائر
                $value = array_sum(array_column($monthData['moh_data'], 'total_units'));
            }
            
            $unitsRow[] = number_format($value);
        }
        $rows[] = $unitsRow;
        
        // صف عدد الأسرة (مميز)
        $bedsRow = ['عدد الأسرة'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['families'])) {
                $value = $monthData['families']['total_beds'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_beds'));
            }
            
            $bedsRow[] = number_format($value);
        }
        $rows[] = $bedsRow;
        
        // صف المرضى الخارجيون
        $outpatientsRow = ['المرضى الخارجيون'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['families'])) {
                $value = $monthData['families']['total_outpatients'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_outpatients'));
            }
            
            $outpatientsRow[] = number_format($value);
        }
        $rows[] = $outpatientsRow;
        
        // صف المرضى الداخليون
        $inpatientsRow = ['المرضى الداخليون'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['families'])) {
                $value = $monthData['families']['total_inpatients'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_inpatients'));
            }
            
            $inpatientsRow[] = number_format($value);
        }
        $rows[] = $inpatientsRow;
        
        // صف أيام المكوث
        $stayDaysRow = ['أيام المكوث'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['families'])) {
                $value = $monthData['families']['total_stay_days'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_stay_days'));
            }
            
            $stayDaysRow[] = number_format($value);
        }
        $rows[] = $stayDaysRow;
        
        // صف الوفيات (مميز)
        $deathsRow = ['الوفيات'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['families'])) {
                $value = $monthData['families']['total_deaths'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_deaths'));
            }
            
            $deathsRow[] = number_format($value);
        }
        $rows[] = $deathsRow;
        
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // تطبيق الأنماط لتطابق الواجهة
        return [
            // رأس الجدول - خلفية داكنة
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '343A40']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // صف عدد الوحدات
            2 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // صف عدد الأسرة - خلفية زرقاء فاتحة (table-info)
            3 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1ECF1']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // صف المرضى الخارجيون
            4 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // صف المرضى الداخليون
            5 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // صف أيام المكوث
            6 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // صف الوفيات - خلفية حمراء فاتحة (table-danger)
            7 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F8D7DA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // العمود الأول - خط عريض
            'A' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
            ]
        ];
    }
}

// ورقة إحصائيات العمليات
class SurgeryStatisticsSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    private $data;
    private $sheetTitle;

    public function __construct($data, $sheetTitle)
    {
        $this->data = $data;
        $this->sheetTitle = $sheetTitle;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function headings(): array
    {
        $headings = ['نوع العملية'];
        
        foreach ($this->data['monthly_data'] as $monthData) {
            $headings[] = $monthData['month_name'];
        }
        
        return $headings;
    }

    public function array(): array
    {
        $rows = [];
        
        // عمليات خاصة
        $specialRow = ['عمليات خاصة'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['surgeries'])) {
                $value = $monthData['surgeries']['total_special'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_surgeries_special'));
            }
            
            $specialRow[] = number_format($value);
        }
        $rows[] = $specialRow;
        
        // عمليات كبرى معقدة
        $majorComplexRow = ['عمليات كبرى معقدة'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['surgeries'])) {
                $value = $monthData['surgeries']['total_major_complex'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_surgeries_major_complex'));
            }
            
            $majorComplexRow[] = number_format($value);
        }
        $rows[] = $majorComplexRow;
        
        // عمليات كبرى
        $majorRow = ['عمليات كبرى'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['surgeries'])) {
                $value = $monthData['surgeries']['total_major'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_surgeries_major'));
            }
            
            $majorRow[] = number_format($value);
        }
        $rows[] = $majorRow;
        
        // عمليات متوسطة
        $mediumRow = ['عمليات متوسطة'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['surgeries'])) {
                $value = $monthData['surgeries']['total_medium'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_surgeries_medium'));
            }
            
            $mediumRow[] = number_format($value);
        }
        $rows[] = $mediumRow;
        
        // عمليات صغرى
        $minorRow = ['عمليات صغرى'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['surgeries'])) {
                $value = $monthData['surgeries']['total_minor'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_surgeries_minor'));
            }
            
            $minorRow[] = number_format($value);
        }
        $rows[] = $minorRow;
        
        // الإجمالي (مميز)
        $totalRow = ['الإجمالي'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $total = 0;
            
            if (isset($monthData['surgeries'])) {
                $total = ($monthData['surgeries']['total_special'] ?? 0) +
                        ($monthData['surgeries']['total_major_complex'] ?? 0) +
                        ($monthData['surgeries']['total_major'] ?? 0) +
                        ($monthData['surgeries']['total_medium'] ?? 0) +
                        ($monthData['surgeries']['total_minor'] ?? 0);
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $total = array_sum(array_column($monthData['moh_data'], 'total_surgeries_special')) +
                        array_sum(array_column($monthData['moh_data'], 'total_surgeries_major_complex')) +
                        array_sum(array_column($monthData['moh_data'], 'total_surgeries_major')) +
                        array_sum(array_column($monthData['moh_data'], 'total_surgeries_medium')) +
                        array_sum(array_column($monthData['moh_data'], 'total_surgeries_minor'));
            }
            
            $totalRow[] = number_format($total);
        }
        $rows[] = $totalRow;
        
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // رأس الجدول
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '343A40']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // عمليات خاصة
            2 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // عمليات كبرى معقدة
            3 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // عمليات كبرى
            4 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // عمليات متوسطة
            5 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // عمليات صغرى
            6 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // صف الإجمالي - خلفية خضراء فاتحة (table-success)
            7 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D4EDDA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            'A' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
            ]
        ];
    }
}

// ورقة إحصائيات الحوادث والولادات
class IncidentStatisticsSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    private $data;
    private $sheetTitle;

    public function __construct($data, $sheetTitle)
    {
        $this->data = $data;
        $this->sheetTitle = $sheetTitle;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function headings(): array
    {
        $headings = ['البيان'];
        
        foreach ($this->data['monthly_data'] as $monthData) {
            $headings[] = $monthData['month_name'];
        }
        
        return $headings;
    }

    public function array(): array
    {
        $rows = [];
        
        // ولادات طبيعية حية
        $naturalRow = ['ولادات طبيعية حية'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['incidents'])) {
                $value = $monthData['incidents']['total_live_births_natural'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_births_natural'));
            }
            
            $naturalRow[] = number_format($value);
        }
        $rows[] = $naturalRow;
        
        // ولادات قيصرية حية
        $cesareanRow = ['ولادات قيصرية حية'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['incidents'])) {
                $value = $monthData['incidents']['total_live_births_cesarean'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_births_cesarean'));
            }
            
            $cesareanRow[] = number_format($value);
        }
        $rows[] = $cesareanRow;
        
        // إجمالي الولادات (مميز)
        $totalBirthsRow = ['إجمالي الولادات'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['incidents'])) {
                $value = $monthData['incidents']['total_births'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_births'));
            }
            
            $totalBirthsRow[] = number_format($value);
        }
        $rows[] = $totalBirthsRow;
        
        // وفيات الأمهات
        $maternalDeathsRow = ['وفيات الأمهات'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['incidents'])) {
                $value = $monthData['incidents']['total_maternal_deaths'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_maternal_deaths'));
            }
            
            $maternalDeathsRow[] = number_format($value);
        }
        $rows[] = $maternalDeathsRow;
        
        // إجمالي الوفيات (مميز)
        $totalDeathsRow = ['إجمالي الوفيات'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['incidents'])) {
                $value = $monthData['incidents']['total_deaths'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                // للتقرير العام، إجمالي الوفيات يأتي من total_deaths مباشرة وليس من جمع وفيات الأمهات
                $value = array_sum(array_column($monthData['moh_data'], 'total_deaths'));
            }
            
            $totalDeathsRow[] = number_format($value);
        }
        $rows[] = $totalDeathsRow;
        
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '343A40']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // إجمالي الولادات - خلفية صفراء فاتحة (table-warning)
            4 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFF3CD']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // إجمالي الوفيات - خلفية حمراء فاتحة (table-danger)
            6 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F8D7DA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            2 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            3 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            5 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            'A' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
            ]
        ];
    }
}

// ورقة إحصائيات الاستشارية
class ConsultationStatisticsSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    private $data;
    private $sheetTitle;

    public function __construct($data, $sheetTitle)
    {
        $this->data = $data;
        $this->sheetTitle = $sheetTitle;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function headings(): array
    {
        $headings = ['البيان'];
        
        foreach ($this->data['monthly_data'] as $monthData) {
            $headings[] = $monthData['month_name'];
        }
        
        return $headings;
    }

    public function array(): array
    {
        $rows = [];
        
        // إجمالي الصالات
        $hallsRow = ['إجمالي الصالات'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            // البحث في moh_data أولاً (هذا هو المصدر الصحيح بعد getData(true))
            if (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_consultation_halls'));
            } elseif (isset($monthData['consultations'])) {
                $value = $monthData['consultations']['total_halls'] ?? 0;
            }
            
            $hallsRow[] = number_format($value);
        }
        $rows[] = $hallsRow;
        
        // إجمالي الأسرة
        $bedsRow = ['إجمالي الأسرة'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            // البحث في moh_data أولاً (هذا هو المصدر الصحيح بعد getData(true))
            if (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_consultation_beds'));
            } elseif (isset($monthData['consultations'])) {
                $value = $monthData['consultations']['total_hall_beds'] ?? 0;
            }
            
            $bedsRow[] = number_format($value);
        }
        $rows[] = $bedsRow;
        
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '343A40']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            2 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            3 => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            'A' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
            ]
        ];
    }
}

// ورقة إحصائيات الحالات والفحوصات
class CasesStatisticsSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    private $data;
    private $sheetTitle;

    public function __construct($data, $sheetTitle)
    {
        $this->data = $data;
        $this->sheetTitle = $sheetTitle;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function headings(): array
    {
        $headings = ['البيان'];
        
        foreach ($this->data['monthly_data'] as $monthData) {
            $headings[] = $monthData['month_name'];
        }
        
        return $headings;
    }

    public function array(): array
    {
        $rows = [];
        
        // الحالات المرضية
        $emergencyCasesRow = ['عدد الحالات المرضية في العيادات الاستشارية'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_emergency_cases'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_emergency_cases'));
            }
            
            $emergencyCasesRow[] = number_format($value);
        }
        $rows[] = $emergencyCasesRow;
        
        $lightCasesRow = ['عدد الحالات المرضية في العيادات الخافة او العامة'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_light_cases'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_light_cases'));
            }
            
            $lightCasesRow[] = number_format($value);
        }
        $rows[] = $lightCasesRow;
        
        $ambulanceCasesRow = ['عدد الحالات المرضية في عيادات الطوارئ'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_ambulance_cases'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_ambulance_cases'));
            }
            
            $ambulanceCasesRow[] = number_format($value);
        }
        $rows[] = $ambulanceCasesRow;
        
        // عنوان فرعي للفحوصات
        $examsHeaderRow = ['الفحوصات'];
        for ($i = 0; $i < count($this->data['monthly_data']); $i++) {
            $examsHeaderRow[] = '';
        }
        $rows[] = $examsHeaderRow;
        
        // الفحوصات
        $outpatientClinicalRow = ['عدد الفحوصات الساندة للمراجعين'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_outpatient_clinical'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_outpatient_clinical'));
            }
            
            $outpatientClinicalRow[] = number_format($value);
        }
        $rows[] = $outpatientClinicalRow;
        
        $outpatientLabRow = ['عدد الفحوصات المختبرية للمراجعين'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_outpatient_lab'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_outpatient_lab'));
            }
            
            $outpatientLabRow[] = number_format($value);
        }
        $rows[] = $outpatientLabRow;
        
        $inpatientClinicalRow = ['عدد الفحوصات الساندة للراقدين'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_inpatient_clinical'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_inpatient_clinical'));
            }
            
            $inpatientClinicalRow[] = number_format($value);
        }
        $rows[] = $inpatientClinicalRow;
        
        $inpatientLabRow = ['عدد الفحوصات المختبرية للراقدين'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_inpatient_lab'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_inpatient_lab'));
            }
            
            $inpatientLabRow[] = number_format($value);
        }
        $rows[] = $inpatientLabRow;
        
        // عنوان فرعي لبيانات الأسرة
        $bedsHeaderRow = ['بيانات الأسرة'];
        for ($i = 0; $i < count($this->data['monthly_data']); $i++) {
            $bedsHeaderRow[] = '';
        }
        $rows[] = $bedsHeaderRow;
        
        // بيانات الأسرة
        $availableBedsRow = ['عدد الاسرة في المخزن'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_available_beds'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_available_beds'));
            }
            
            $availableBedsRow[] = number_format($value);
        }
        $rows[] = $availableBedsRow;
        
        $otherBedsRow = ['عدد الاسرة في الأخرى'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_other_beds'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_other_beds'));
            }
            
            $otherBedsRow[] = number_format($value);
        }
        $rows[] = $otherBedsRow;
        
        $emergencyBedsRow = ['عدد الاسرة في الاستشاريات'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_emergency_beds'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_emergency_beds'));
            }
            
            $emergencyBedsRow[] = number_format($value);
        }
        $rows[] = $emergencyBedsRow;
        
        $closedBedsRow = ['عدد الاسرة في الردهات المغلقة'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_closed_ward_beds'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_closed_ward_beds'));
            }
            
            $closedBedsRow[] = number_format($value);
        }
        $rows[] = $closedBedsRow;
        
        $mohBedsRow = ['عدد الاسرة الكلية المهيئة'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_moh_beds'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_moh_beds'));
            }
            
            $mohBedsRow[] = number_format($value);
        }
        $rows[] = $mohBedsRow;
        
        // عدد الأسرة الكلي (مميز)
        $totalBedsRow = ['عدد الاسرة الكلي'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_all_beds'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_all_beds'));
            }
            
            $totalBedsRow[] = number_format($value);
        }
        $rows[] = $totalBedsRow;
        
        $facilityBedsRow = ['إجمالي الاسرة'];
        foreach ($this->data['monthly_data'] as $monthData) {
            $value = 0;
            
            if (isset($monthData['cases'])) {
                $value = $monthData['cases']['total_facility_beds'] ?? 0;
            } elseif (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                $value = array_sum(array_column($monthData['moh_data'], 'total_facility_beds'));
            }
            
            $facilityBedsRow[] = number_format($value);
        }
        $rows[] = $facilityBedsRow;
        
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '343A40']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // عناوين الأقسام الفرعية - خلفية رمادية (table-secondary)
            4 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E2E3E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            9 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E2E3E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // عدد الأسرة الكلي - خلفية زرقاء فاتحة (table-info)
            15 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1ECF1']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // باقي الصفوف
            2 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            3 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            5 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            6 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            7 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            8 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            10 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            11 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            12 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            13 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            14 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            16 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            'A' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
            ]
        ];
    }
}

// ورقة إحصائيات الردهات التفصيلية
class WardDetailsStatisticsSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    private $data;
    private $sheetTitle;

    public function __construct($data, $sheetTitle)
    {
        $this->data = $data;
        $this->sheetTitle = $sheetTitle;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function headings(): array
    {
        $headings = ['اسم الردهة', 'رمز الردهة'];
        
        foreach ($this->data['monthly_data'] as $monthData) {
            $headings[] = $monthData['month_name'] . ' - الوحدات';
            $headings[] = $monthData['month_name'] . ' - الأسرة';
            $headings[] = $monthData['month_name'] . ' - الخارجيون';
            $headings[] = $monthData['month_name'] . ' - الداخليون';
        }
        
        return $headings;
    }

    public function array(): array
    {
        $rows = [];
        
        // جمع جميع الردهات من جميع الأشهر ومن جميع المصادر
        $allWards = [];
        
        foreach ($this->data['monthly_data'] as $monthData) {
            // الحالة الأولى: البيانات التفصيلية موجودة مباشرة في الشهر (للدائرة المحددة)
            if (isset($monthData['ward_details']) && !empty($monthData['ward_details'])) {
                $wardDetails = $monthData['ward_details'];
                if (is_string($wardDetails)) {
                    $wardDetails = json_decode($wardDetails, true);
                }
                
                if (is_array($wardDetails)) {
                    $allWards = array_merge($allWards, $wardDetails);
                }
            }
            
            // الحالة الثانية: البيانات التفصيلية موجودة في moh_data لكل دائرة (للتقرير العام)
            if (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                foreach ($monthData['moh_data'] as $mohData) {
                    if (isset($mohData['ward_details']) && !empty($mohData['ward_details'])) {
                        $wardDetails = $mohData['ward_details'];
                        if (is_string($wardDetails)) {
                            $wardDetails = json_decode($wardDetails, true);
                        }
                        
                        if (is_array($wardDetails)) {
                            $allWards = array_merge($allWards, $wardDetails);
                        }
                    }
                }
            }
        }
        
        // إذا لم توجد بيانات، إضافة صف يوضح عدم توفر البيانات
        if (empty($allWards)) {
            $row = ['لا توجد بيانات تفصيلية للردهات', ''];
            // إضافة خلايا فارغة لكل شهر
            foreach ($this->data['monthly_data'] as $monthData) {
                $row[] = 'لا توجد بيانات';
                $row[] = 'لا توجد بيانات';
                $row[] = 'لا توجد بيانات';
                $row[] = 'لا توجد بيانات';
            }
            $rows[] = $row;
            return $rows;
        }
        
        // إزالة التكرار بناءً على معرف الردهة
        $seenWardIds = [];
        $uniqueWards = [];
        foreach ($allWards as $ward) {
            $wardId = is_array($ward) ? ($ward['ward_id'] ?? null) : ($ward->ward_id ?? null);
            if ($wardId && !in_array($wardId, $seenWardIds)) {
                $seenWardIds[] = $wardId;
                $uniqueWards[] = $ward;
            }
        }
        
        // ترتيب الردهات حسب عدد الأسرة
        usort($uniqueWards, function($a, $b) {
            $totalBedsA = is_array($a) ? ($a['total_beds'] ?? 0) : ($a->total_beds ?? 0);
            $totalBedsB = is_array($b) ? ($b['total_beds'] ?? 0) : ($b->total_beds ?? 0);
            return $totalBedsB - $totalBedsA;
        });
        
        foreach ($uniqueWards as $ward) {
            $wardName = is_array($ward) ? ($ward['ward_name'] ?? 'غير محدد') : ($ward->ward_name ?? 'غير محدد');
            $wardNumber = is_array($ward) ? ($ward['ward_number'] ?? 'غير محدد') : ($ward->ward_number ?? 'غير محدد');
            $wardId = is_array($ward) ? ($ward['ward_id'] ?? null) : ($ward->ward_id ?? null);
            
            $row = [$wardName, $wardNumber];
            
            // إضافة بيانات كل شهر لهذه الردهة
            foreach ($this->data['monthly_data'] as $monthData) {
                $wardData = null;
                
                // البحث في البيانات المباشرة أولاً (للدائرة المحددة)
                if (isset($monthData['ward_details']) && !empty($monthData['ward_details'])) {
                    $wardDetails = $monthData['ward_details'];
                    if (is_string($wardDetails)) {
                        $wardDetails = json_decode($wardDetails, true);
                    }
                    
                    if (is_array($wardDetails)) {
                        foreach ($wardDetails as $detail) {
                            $detailWardId = is_array($detail) ? ($detail['ward_id'] ?? null) : ($detail->ward_id ?? null);
                            if ($detailWardId == $wardId) {
                                $wardData = $detail;
                                break;
                            }
                        }
                    }
                }
                
                // إذا لم نجد البيانات، ابحث في جميع دوائر الصحة (للتقرير العام)
                if (!$wardData && isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                    foreach ($monthData['moh_data'] as $mohData) {
                        if (isset($mohData['ward_details'])) {
                            $wardDetails = $mohData['ward_details'];
                            if (is_string($wardDetails)) {
                                $wardDetails = json_decode($wardDetails, true);
                            }
                            
                            if (is_array($wardDetails)) {
                                foreach ($wardDetails as $detail) {
                                    $detailWardId = is_array($detail) ? ($detail['ward_id'] ?? null) : ($detail->ward_id ?? null);
                                    if ($detailWardId == $wardId) {
                                        $wardData = $detail;
                                        break 2; // خروج من كلا الحلقتين
                                    }
                                }
                            }
                        }
                    }
                }
                
                if ($wardData) {
                    $totalUnits = is_array($wardData) ? ($wardData['total_units'] ?? 0) : ($wardData->total_units ?? 0);
                    $totalBeds = is_array($wardData) ? ($wardData['total_beds'] ?? 0) : ($wardData->total_beds ?? 0);
                    $totalOutpatients = is_array($wardData) ? ($wardData['total_outpatients'] ?? 0) : ($wardData->total_outpatients ?? 0);
                    $totalInpatients = is_array($wardData) ? ($wardData['total_inpatients'] ?? 0) : ($wardData->total_inpatients ?? 0);
                    
                    $row[] = number_format($totalUnits);
                    $row[] = number_format($totalBeds);
                    $row[] = number_format($totalOutpatients);
                    $row[] = number_format($totalInpatients);
                } else {
                    $row[] = '0';
                    $row[] = '0';
                    $row[] = '0';
                    $row[] = '0';
                }
            }
            
            $rows[] = $row;
        }
        
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            // رأس الجدول
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '343A40']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // العمودان الأول والثاني
            'A' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
            ],
            'B' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
        
        // تنسيق باقي البيانات
        for ($row = 2; $row <= 100; $row++) {
            $styles[$row] = [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
        }
        
        return $styles;
    }
}

// ورقة إحصائيات الاستشارية التفصيلية
class ConsultationDetailsStatisticsSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    private $data;
    private $sheetTitle;

    public function __construct($data, $sheetTitle)
    {
        $this->data = $data;
        $this->sheetTitle = $sheetTitle;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function headings(): array
    {
        $headings = ['نوع الاستشارية'];
        
        foreach ($this->data['monthly_data'] as $monthData) {
            $headings[] = $monthData['month_name'] . ' - الصالات';
            $headings[] = $monthData['month_name'] . ' - الأسرة';
        }
        
        return $headings;
    }

    public function array(): array
    {
        $rows = [];
        
        // جمع جميع أنواع الاستشارية من جميع الأشهر ومن جميع المصادر
        $allConsultationTypes = [];
        
        foreach ($this->data['monthly_data'] as $monthData) {
            // الحالة الأولى: البيانات التفصيلية موجودة مباشرة في الشهر (للدائرة المحددة)
            if (isset($monthData['consultation_details']) && !empty($monthData['consultation_details'])) {
                $consultationDetails = $monthData['consultation_details'];
                if (is_string($consultationDetails)) {
                    $consultationDetails = json_decode($consultationDetails, true);
                }
                
                if (is_array($consultationDetails)) {
                    foreach ($consultationDetails as $consultation) {
                        $consultationData = (array) $consultation;
                        
                        $typeId = $consultationData['type_id'] ?? null;
                        $typeName = $consultationData['type_name'] ?? 'غير محدد';
                        
                        if ($typeId && !isset($allConsultationTypes[$typeId])) {
                            $allConsultationTypes[$typeId] = [
                                'type_id' => $typeId,
                                'type_name' => $typeName
                            ];
                        }
                    }
                }
            }
            
            // الحالة الثانية: البيانات التفصيلية موجودة في moh_data لكل دائرة (للتقرير العام)
            if (isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                foreach ($monthData['moh_data'] as $mohData) {
                    if (isset($mohData['consultation_details']) && !empty($mohData['consultation_details'])) {
                        $consultationDetails = $mohData['consultation_details'];
                        if (is_string($consultationDetails)) {
                            $consultationDetails = json_decode($consultationDetails, true);
                        }
                        
                        if (is_array($consultationDetails)) {
                            foreach ($consultationDetails as $consultation) {
                                $consultationData = (array) $consultation;
                                
                                $typeId = $consultationData['type_id'] ?? null;
                                $typeName = $consultationData['type_name'] ?? 'غير محدد';
                                
                                if ($typeId && !isset($allConsultationTypes[$typeId])) {
                                    $allConsultationTypes[$typeId] = [
                                        'type_id' => $typeId,
                                        'type_name' => $typeName
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // إذا لم توجد بيانات تفصيلية
        if (empty($allConsultationTypes)) {
            $row = ['لا توجد بيانات تفصيلية للاستشارية'];
            // إضافة خلايا فارغة لكل شهر
            foreach ($this->data['monthly_data'] as $monthData) {
                $row[] = 'لا توجد بيانات';
                $row[] = 'لا توجد بيانات';
            }
            $rows[] = $row;
            return $rows;
        }
        
        // ترتيب أنواع الاستشارية حسب الاسم
        uasort($allConsultationTypes, function($a, $b) {
            return strcmp($a['type_name'], $b['type_name']);
        });
        
        // إضافة صف لكل نوع استشارية
        foreach ($allConsultationTypes as $consultationType) {
            $row = [$consultationType['type_name']];
            
            // إضافة بيانات كل شهر لهذا النوع
            foreach ($this->data['monthly_data'] as $monthData) {
                $halls = 0;
                $beds = 0;
                
                // البحث في البيانات المباشرة أولاً (للدائرة المحددة)
                if (isset($monthData['consultation_details']) && !empty($monthData['consultation_details'])) {
                    $consultationDetails = $monthData['consultation_details'];
                    if (is_string($consultationDetails)) {
                        $consultationDetails = json_decode($consultationDetails, true);
                    }
                    
                    if (is_array($consultationDetails)) {
                        foreach ($consultationDetails as $consultation) {
                            $consultationData = (array) $consultation;
                            
                            if (($consultationData['type_id'] ?? null) == $consultationType['type_id']) {
                                $halls += intval($consultationData['halls'] ?? 0);
                                $beds += intval($consultationData['beds'] ?? 0);
                            }
                        }
                    }
                }
                
                // إذا لم نجد البيانات، ابحث في جميع دوائر الصحة (للتقرير العام)
                if ($halls == 0 && $beds == 0 && isset($monthData['moh_data']) && is_array($monthData['moh_data'])) {
                    foreach ($monthData['moh_data'] as $mohData) {
                        if (isset($mohData['consultation_details'])) {
                            $consultationDetails = $mohData['consultation_details'];
                            if (is_string($consultationDetails)) {
                                $consultationDetails = json_decode($consultationDetails, true);
                            }
                            
                            if (is_array($consultationDetails)) {
                                foreach ($consultationDetails as $consultation) {
                                    $consultationData = (array) $consultation;
                                    
                                    if (($consultationData['type_id'] ?? null) == $consultationType['type_id']) {
                                        $halls += intval($consultationData['halls'] ?? 0);
                                        $beds += intval($consultationData['beds'] ?? 0);
                                    }
                                }
                            }
                        }
                    }
                }
                
                $row[] = number_format($halls);
                $row[] = number_format($beds);
            }
            
            $rows[] = $row;
        }
        
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            // رأس الجدول
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '343A40']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // العمود الأول
            'A' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
            ]
        ];
        
        // تنسيق باقي البيانات
        for ($row = 2; $row <= 50; $row++) {
            $styles[$row] = [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
        }
        
        return $styles;
    }
}
