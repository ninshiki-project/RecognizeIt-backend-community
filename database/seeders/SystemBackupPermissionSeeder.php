<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class SystemBackupPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $permission1 = Permission::findOrCreate('system backup', 'web');
        $permission2 = Permission::findOrCreate('download system backup', 'web');
        $permission3 = Permission::findOrCreate('delete system backup', 'web');
        $role = Role::findByName('Administrator');
        $role->givePermissionTo([$permission1, $permission2, $permission3]);
    }
}
