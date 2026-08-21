<?php

namespace App\Exports;

use App\Models\PrivateWingReceipt;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PrivateVisitDetailsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request ?: request();
    }

    public function collection()
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $query = PrivateWingReceipt::with('privateWing')
            ->whereHas('privateWing', function ($q) {
                $q->where('patient_type', 'outpatient');
            });

        // Apply institutional bounds
        if (in_array("Admin", $currentUser->roles_name) || in_array("mohst", $currentUser->roles_name)) {
            // No filter
        } elseif ($currentUser->roles_name === ["stat-doh"]) {
            $query->whereHas('privateWing', function ($q) use ($currentUser) {
                $q->where('moh_id', $currentUser->mohcode);
            });
        } else {
            $query->whereHas('privateWing', function ($q) use ($currentUser) {
                $q->where('fck_id', $currentUser->fckid);
            });
        }

        // Search filters
        if ($this->request->filled('patient_name')) {
            $query->whereHas('privateWing', function($q) {
                $q->where('patient_name', 'like', '%' . $this->request->patient_name . '%');
            });
        }

        if ($this->request->filled('receipt_number')) {
            $query->where('receipt_number', 'like', '%' . $this->request->receipt_number . '%');
        }

        if ($this->request->filled('payment_type')) {
            $query->where('payment_type', $this->request->payment_type);
        }

        if ($this->request->filled('receipt_date_from')) {
            $query->whereDate('receipt_date', '>=', $this->request->receipt_date_from);
        }
        if ($this->request->filled('receipt_date_to')) {
            $query->whereDate('receipt_date', '<=', $this->request->receipt_date_to);
        }

        $receipts = $query->orderBy('receipt_date', 'desc')->get();

        $data = [];
        $serial = 1;
        foreach ($receipts as $receipt) {
            $data[] = [
                'تسلسل' => $serial++,
                'اسم المريض' => $receipt->privateWing->patient_name ?? '-',
                'رقم الوصل' => $receipt->receipt_number,
                'التاريخ' => $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : '-',
                'نوع الدفع' => $receipt->payment_type,
                'مبلغ الوصل' => $receipt->amount,
                'مبلغ الغرامة 5%' => $receipt->interest_amount,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'تسلسل',
            'اسم المريض',
            'رقم الوصل',
            'التاريخ',
            'نوع الدفع',
            'مبلغ الوصل',
            'مبلغ الغرامة 5%',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
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

    public function columnWidths(): array
    {
        return [
            'A' => 12, // تسلسل
            'B' => 30, // اسم المريض
            'C' => 20, // رقم الوصل
            'D' => 18, // التاريخ
            'E' => 15, // نوع الدفع
            'F' => 18, // مبلغ الوصل
            'G' => 18, // مبلغ الغرامة 5%
        ];
    }

    public function title(): string
    {
        return 'تفاصيل وصولات الزيارات';
    }
}
