<?php

namespace App\Exports;

use App\Models\PrivateWingReceipt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReceiptsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    /**
     * الحصول على البيانات
     */
    public function collection()
    {
        $query = PrivateWingReceipt::with('privateWing');

        // تطبيق نفس الفلاتر المستخدمة في الـ Controller
        if ($this->request && $this->request->filled('date_from')) {
            $query->where('receipt_date', '>=', $this->request->date_from);
        }

        if ($this->request && $this->request->filled('date_to')) {
            $query->where('receipt_date', '<=', $this->request->date_to);
        }

        if ($this->request && $this->request->filled('receipt_number')) {
            $query->where('receipt_number', 'like', '%' . $this->request->receipt_number . '%');
        }

        if ($this->request && $this->request->filled('month')) {
            $query->whereMonth('receipt_date', $this->request->month);
        }

        if ($this->request && $this->request->filled('year')) {
            $query->whereYear('receipt_date', $this->request->year);
        }

        $receipts = $query->orderBy('receipt_date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();

        $data = [];

        foreach ($receipts as $receipt) {
            $data[] = [
                'رقم الوصل' => $receipt->receipt_number,
                'تاريخ الوصل' => $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : '-',
                'المبلغ' => number_format($receipt->amount, 2),
                'اسم المريض' => $receipt->privateWing->patient_name ?? 'غير محدد',
                'رقم الحساب' => $receipt->privateWing->file_number ?? '-',
                'نوع الدفع' => $this->getPaymentTypeText($receipt->payment_type),
            ];
        }

        return collect($data);
    }

    /**
     * عناوين الأعمدة
     */
    public function headings(): array
    {
        return [
            'رقم الوصل',
            'تاريخ الوصل',
            'المبلغ',
            'اسم المريض',
            'رقم الحساب',
            'نوع الدفع',
        ];
    }

    /**
     * تنسيق الخلايا
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    /**
     * عرض الأعمدة
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 12,
            'C' => 12,
            'D' => 20,
            'E' => 15,
            'F' => 12,
        ];
    }

    /**
     * عنوان الورقة
     */
    public function title(): string
    {
        return 'الوصولات';
    }

    /**
     * تحويل نوع الدفع إلى نص عربي
     */
    private function getPaymentTypeText($paymentType)
    {
        switch ($paymentType) {
            case 'cash':
                return 'نقدي';
            case 'bank':
                return 'بنكي';
            case 'check':
                return 'شيك';
            default:
                return $paymentType ?? '-';
        }
    }
}
