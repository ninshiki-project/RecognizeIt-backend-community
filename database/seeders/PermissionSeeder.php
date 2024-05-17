<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $arrayOfPermissionNames = collect([
            // User Model
            'invite user',
            'view user',
            'update user',
            'delete user',
            'restore user',
            'force delete user',
            // Department Model
            'create department',
            'view department',
            'update department',
            'delete department',
            'restore department',
            'force delete department',

        ]);

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($permissions->toArray());

    }
}
