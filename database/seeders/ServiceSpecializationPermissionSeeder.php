<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ServiceSpecializationPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'service-specializations.view',
            'service-specializations.create',
            'service-specializations.edit',
            'service-specializations.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
