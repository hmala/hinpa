<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeSpecialization;

class TypeSpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specializations = [
            [
                'tscode' => 101,
                'tsname' => 'طب عام'
            ],
            [
                'tscode' => 102,
                'tsname' => 'أمراض القلب'
            ],
            [
                'tscode' => 103,
                'tsname' => 'عظام'
            ],
            [
                'tscode' => 104,
                'tsname' => 'طب أطفال'
            ]
        ];

        foreach ($specializations as $specialization) {
            TypeSpecialization::create($specialization);
        }
    }
}
