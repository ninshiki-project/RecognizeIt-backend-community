<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: TheSeeder.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace Database\Seeders;

use App\Enum\UserEnum;
use App\Models\Departments;
use App\Models\Designations;
use App\Models\Permission;
use App\Models\User;
use Closure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Helper\ProgressBar;

class TheSeeder extends Seeder
{
    public function run(): void
    {
        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions

        $customPermissions = [
            ['name' => 'access panel', 'guard_name' => 'web'],
            ['name' => 'system backup', 'guard_name' => 'web'],
            ['name' => 'delete-backup', 'guard_name' => 'web'],
            ['name' => 'download-backup', 'guard_name' => 'web'],
        ];

        /**
         * Create Permissions
         */
        $this->command->warn(PHP_EOL.'Creating permissions...');
        $this->command->call('shield:generate', ['--all' => true, '--panel' => 'admin']);
        $this->withProgressBar(1, fn () => Permission::insert($customPermissions));
        $this->command->info('Permissions has been created.');

        /**
         * Create Roles
         */
        $this->command->warn(PHP_EOL.'Creating Admin roles...');
        $this->withProgressBar(1, function () {
            $role = Role::where('name', 'Administrator')->first();
            if (! $role) {
                $role = Role::create(['name' => 'Administrator', 'guard_name' => 'web']);
            }
            $role->givePermissionTo(Permission::all());
        });
        $this->command->info('Roles has been created.');

        $this->command->warn(PHP_EOL.'Creating member roles...');
        $this->withProgressBar(1, function () {
            Role::create(['name' => 'Member', 'guard_name' => 'web']);
        });
        $this->command->info('Roles has been created.');

        /**
         *  Create Admin User
         */
        if (app()->isLocal() || app()->runningUnitTests()) {
            $this->command->warn(PHP_EOL.'Creating Admin user and assigning roles...');
            $this->withProgressBar(1, function () {
                $user = User::factory()->create([
                    'name' => 'Test User',
                    'username' => 'test_user',
                    'email' => 'test@example.com',
                    'password' => Hash::make('password'),
                    'status' => UserEnum::Active,
                    'designation' => Designations::inRandomOrder()->first()->name,
                    'department' => Departments::inRandomOrder()->first()->id,
                ]);
                $this->command->callSilently('shield:super-admin', ['--user' => $user->id, '--panel' => 0]);
                $this->command->info(PHP_EOL.'Points created and associated...');
            });
            $this->command->info('Administrator user created.');
        }

        /**
         *  Create Normal User
         */
        if (app()->isLocal() || app()->runningUnitTests()) {
            $this->command->warn(PHP_EOL.'Creating Normal user and assigning roles...');
            $this->withProgressBar(5, function () {
                $user = User::factory()->create([
                    'status' => UserEnum::Active,
                    'designation' => Designations::inRandomOrder()->first()->name,
                    'department' => Departments::inRandomOrder()->first()->id,
                ])
                    ->assignRole('Member');
                $this->command->info(PHP_EOL.'Points created and associated...');
            });
            $this->command->info('Normal user created.');

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
