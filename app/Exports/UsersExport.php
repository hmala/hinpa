<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::with(['fck', 'moh', 'roles'])->orderBy('id', 'DESC')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            'اسم المستخدم',
            'البريد الالكتروني',
            'حالة المستخدم',
            'المؤسسة التابع لها',
            'الدائرة التابع لها',
            'كود المصادقة الثنائية',
            'نوع المستخدم',
            'تاريخ الإنشاء',
        ];
    }

    /**
     * @var User $user
     */
    public function map($user): array
    {
        static $counter = 0;
        $counter++;

        // الحصول على أسماء الأدوار
        $roles = $user->getRoleNames()->implode(', ');

        return [
            $counter,
            $user->name,
            $user->email,
            $user->Status,
            $user->fck ? $user->fck->Fckname : 'لا يوجد مؤسسة',
            $user->moh ? $user->moh->mohname : 'لا يوجد دائرة',
            $user->google2fa_secret ?? 'غير مفعل',
            $roles ?: 'لا يوجد',
            $user->created_at ? $user->created_at->format('Y-m-d H:i') : '',
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // تنسيق الصف الأول (العناوين)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '4CAF50',
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
