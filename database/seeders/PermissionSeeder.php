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

        $userModelPermission = [
            // User Model
            'invite user',
            'view user',
            'update user',
            'delete user',
            'restore user',
            'force delete user',
        ];

        $departmentModelPermission = [
            // Department Model
            'create department',
            'view department',
            'update department',
            'delete department',
            'restore department',
            'force delete department',
        ];

        $allPermissionNames = collect();
        $allPermissionNames->push($userModelPermission);
        $allPermissionNames->push($departmentModelPermission);

        $permissions = collect($allPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($permissions->toArray());

    }
}
