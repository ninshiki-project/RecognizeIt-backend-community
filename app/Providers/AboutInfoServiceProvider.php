<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: AboutInfoServiceProvider.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Providers;

use App\Models\Departments;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Spatie\Emoji\Emoji;

class AboutInfoServiceProvider extends ServiceProvider
{
    //https://unicode.org/Public/emoji/13.1/emoji-test.txt
    public static string $checked = "<fg=green;options=bold>\u{2714} COMPLETED</>";

    public static string $times = '<fg=red;options=bold>NOT SETUP</>';

    public static string $maybe = '<fg=yellow;options=bold>MAYBE</>';

    public function boot(): void
    {
        AboutCommand::add('Ninshiki Seeding', static fn () => [
            'Roles' => fn () => Role::count() > 0 ? self::$checked : self::$times,
            'Permissions' => fn () => Permission::count() > 0 ? self::$checked : self::$times,
            'Department' => fn () => Departments::count() > 0 ? self::$checked : self::$times,
        ]);
        AboutCommand::add('Ninshiki Owner', static fn () => [
            'User Administrator' => fn () => User::with('roles')->get()->filter(
                fn ($user) => $user->roles->where('name', 'Administrator')->toArray()
            )->count() > 0 ? self::$checked : self::$times,
            'Owner' => fn () => User::with('roles')->get()->filter(
                fn ($user) => $user->roles->where('name', 'Administrator')->toArray()
            )->count(),
        ]);
        AboutCommand::add('Ninshiki Configuration', static fn () => [
            'Cloudinary' => Str::length(config('cloudinary.cloud_url')) > 0 ? self::$checked : self::$times,
            'Reverb' => Str::length(config('reverb.apps.apps[0].key')) > 0 ? self::$checked : self::$times,
            'Resend' => Str::length(config('services.resend.key')) > 0 ? self::$checked : self::$maybe,
            'Domain Whitelist' => Str::length(config('ninshiki.allowed_email_domain')) > 0 ? self::$checked : self::$times,
            'Frontend' => Str::of(config('app.frontend_url'))->contains('localhost:3000') ? self::$maybe : self::$checked,
        ]);
    }
}
