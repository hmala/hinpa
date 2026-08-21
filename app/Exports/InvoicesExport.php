<?php

namespace App\Exports;

use App\Models\PrivateWing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
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
        $currentUser = \Illuminate\Support\Facades\Auth::user();

        $query = PrivateWing::query();

        // تطبيق قيود الصلاحيات
        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                $query->where('moh_id', $currentUser->mohcode);
            } else {
                $query->where('fck_id', $currentUser->fckid);
            }
        }

        // تطبيق نفس الفلاتر المستخدمة في الـ Controller
        if ($this->request && $this->request->filled('date_from')) {
            $query->where('entry_date', '>=', $this->request->date_from);
        }

        if ($this->request && $this->request->filled('date_to')) {
            $query->where('entry_date', '<=', $this->request->date_to);
        }

        if ($this->request && $this->request->filled('patient_name')) {
            $query->where('patient_name', 'like', '%' . $this->request->patient_name . '%');
        }

        if ($this->request && $this->request->filled('file_number')) {
            $query->where('file_number', 'like', '%' . $this->request->file_number . '%');
        }

        if ($this->request && $this->request->filled('month')) {
            $query->whereMonth('entry_date', $this->request->month);
        }

        if ($this->request && $this->request->filled('year')) {
            $query->whereYear('entry_date', $this->request->year);
        }

        $invoices = $query->orderBy('entry_date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();

        $data = [];

        foreach ($invoices as $invoice) {
            $data[] = [
                'رقم الحساب' => $invoice->file_number,
                'اسم المريض' => $invoice->patient_name,
                'تاريخ الدخول' => $invoice->entry_date ? $invoice->entry_date->format('Y-m-d') : '-',
                'تاريخ الخروج' => $invoice->exit_date ? $invoice->exit_date->format('Y-m-d') : '-',
                'عدد الأيام' => $invoice->days_count ?? '-',
                'المبلغ الإجمالي' => number_format($invoice->total_amount ?? 0, 2),
                'المبلغ المدفوع' => number_format($invoice->paid_amount ?? 0, 2),
                'المبلغ المتبقي' => number_format(($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0), 2),
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
            'رقم الحساب',
            'اسم المريض',
            'تاريخ الدخول',
            'تاريخ الخروج',
            'عدد الأيام',
            'المبلغ الإجمالي',
            'المبلغ المدفوع',
            'المبلغ المتبقي',
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
            'B' => 20,
            'C' => 12,
            'D' => 12,
            'E' => 10,
            'F' => 15,
            'G' => 15,
            'H' => 15,
        ];
    }

    /**
     * عنوان الورقة
     */
    public function title(): string
    {
        return 'الفواتير';
    }
}