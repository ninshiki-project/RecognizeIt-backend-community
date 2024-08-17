<?php

namespace Database\Seeders;

use App\Models\User;
use Closure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Helper\ProgressBar;

class TheSeeder extends Seeder
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
            return ['name' => $permission, 'guard_name' => 'sanctum'];
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
        $this->command->warn(PHP_EOL.'Creating Admin roles...');
        $this->withProgressBar(1, function () {
            $role = Role::create(['name' => 'Administrator', 'guard_name' => 'sanctum']);
            $role->givePermissionTo(Permission::all());
        });
        $this->command->info('Roles has been created.');

        $this->command->warn(PHP_EOL.'Creating member roles...');
        $this->withProgressBar(1, function () {
            $role = Role::create(['name' => 'Member', 'guard_name' => 'sanctum']);
            $roles = collect(Permission::all())->filter(function ($permission) {
                return ! Str::of($permission->name)->contains([
                    'invite user',
                    'delete user',
                    'restore user',
                    'force delete user',
                    'department',
                    'delete',
                    'edit',
                ]);
            });
            $role->givePermissionTo($roles);
        });
        $this->command->info('Roles has been created.');

        /**
         *  Create Admin User
         */
        if (app()->environment() !== 'production') {
            $this->command->warn(PHP_EOL.'Creating Admin user and assigning roles...');
            $this->withProgressBar(1, function () {
                $user = User::factory()->create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'designation' => config('ninshiki.designation')[0],
                ])
                    ->assignRole('Administrator');
                $this->command->warn(PHP_EOL.'Creating Points System for the User...');
                $user->points()->create();
                $this->command->info(PHP_EOL.'Points created and associated...');
            });
            $this->command->info('Administrator user created.');
        }

        /**
         *  Create Normal User
         */
        if (app()->environment() !== 'production') {
            $this->command->warn(PHP_EOL.'Creating Normal user and assigning roles...');
            $this->withProgressBar(5, function () {
                $user = User::factory()->create([
                    'designation' => config('ninshiki.designation')[0],
                ])
                    ->assignRole('Member');
                $this->command->warn(PHP_EOL.'Creating Points System for the User...');
                $user->points()->create();
                $this->command->info(PHP_EOL.'Points created and associated...');
            });
            $this->command->info('Administrator user created.');

            $this->command->newLine(2);
        }

    }

    protected function withProgressBar(int $amount, Closure $createCollectionOfOne): Collection
    {
        $progressBar = new ProgressBar($this->command->getOutput(), $amount);

        $progressBar->start();

        $items = new Collection;

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
