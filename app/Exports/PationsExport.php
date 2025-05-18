<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class PationsExport implements FromCollection, WithHeadings
{
    protected $pations;

    public function __construct($pations)
    {
        $this->pations = $pations;
    }

    public function collection()
    {
        $data = new Collection();
        
        foreach ($this->pations as $pation) {
            $months = [
                1 => 'كانون الثاني', 2 => 'شباط', 3 => 'اذار', 4 => 'نيسان',
                5 => 'ايار', 6 => 'حزيران', 7 => 'تموز', 8 => 'أب',
                9 => 'ايلول', 10 => 'تشرين الاول', 11 => 'تشرين الثاني', 12 => 'كانون الاول'
            ];

            $data->push([
                'دائرة الصحة' => $pation->moh ? $pation->moh->mohname : 'غير محدد',
                'اسم المستشفى' => $pation->fck ? $pation->fck->Fckname : 'غير محدد',
                'شهر' => $months[$pation->month] ?? 'غير محدد',
                'سنة' => $pation->year,
                'اختصاص السرير' => $pation->rdhs ? $pation->rdhs->Spcuname : 'غير محدد',
                'عدد الوحدات' => $pation->unitnum,
                'عدد الاسرة المهيئة' => $pation->bedm,
                'الخارجين خلال الشهر' => $pation->outpationmon,
                'الباقين آخر الشهر' => $pation->stayoutpation,
                'أيام المكوث' => $pation->mkoth,
                'عدد الوفيات' => $pation->death,
                'حالة الاستمارة' => $pation->status,
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'دائرة الصحة',
            'اسم المستشفى',
            'شهر',
            'سنة',
            'اختصاص السرير',
            'عدد الوحدات',
            'عدد الاسرة المهيئة',
            'الخارجين خلال الشهر',
            'الباقين آخر الشهر',
            'أيام المكوث',
            'عدد الوفيات',
            'حالة الاستمارة'
        ];
    }
}
