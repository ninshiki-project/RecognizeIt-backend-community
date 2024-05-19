<?php

namespace Database\Seeders;

use App\Models\User;
use Closure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Helper\ProgressBar;

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

        $allPermissionNames = [
            ...$userModelPermission,
            ...$departmentModelPermission,
        ];

        $permissions = collect($allPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        /**
         * Create Permissions
         */
        $this->command->warn(PHP_EOL.'Creating permissions...');
        $this->withProgressBar(1, fn () => Permission::insert($permissions->toArray()));
        $this->command->info('Permissions has been created.');

        /**
         * Create Roles
         */
        $this->command->warn(PHP_EOL.'Creating roles...');
        $this->withProgressBar(1, function () use ($allPermissionNames) {
            $role = Role::create(['name' => 'administrator']);
            $role->givePermissionTo($allPermissionNames);
        });
        $this->command->info('Roles has been created.');

        /**
         *  Create User
         */
        $this->command->warn(PHP_EOL.'Creating user and assigning roles...');
        $this->withProgressBar(1, function () {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ])->assignRole('administrator');
            $this->command->warn(PHP_EOL.'Creating Points System for the User...');
            $user->points()->create();
            $this->command->info(PHP_EOL.'Points created and associated...');
        });
        $this->command->info('Administrator user created.');


        $this->command->newLine(2);
    }

    protected function withProgressBar(int $amount, Closure $createCollectionOfOne): Collection
    {
        $progressBar = new ProgressBar($this->command->getOutput(), $amount);

        $progressBar->start();

        $items = new Collection();

        foreach (range(1, $amount) as $i) {
            $items = $items->merge(
                $createCollectionOfOne()
            );
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->command->getOutput()->writeln('');

        return $items;
    }
}
