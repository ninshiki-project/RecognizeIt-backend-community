<?php

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
    public static string $checked = "<fg=green;options=bold>\u{2714} Completed</>";

    public static string $times = '<fg=red;options=bold>Not Setup</>';

    public static string $maybe = '<fg=yellow;options=bold>Maybe</>';

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
        ]);
        AboutCommand::add('Ninshiki Configuration', static fn () => [
            'Cloudinary' => Str::length(config('cloudinary.cloud_url')) > 0 ? self::$checked : self::$times,
            'Reverb' => Str::length(env('REVERB_APP_KEY')) > 0 ? self::$checked : self::$times,
            'Resend' => Str::length(env('RESEND_KEY')) > 0 ? self::$checked : self::$maybe,
            'Domain Whitelist' => Str::length(env('ALLOWED_EMAIL_DOMAIN')) > 0 ? self::$checked : self::$times,
            'Frontend' => Str::of(env('FRONTEND_URL'))->contains('localhost:3000') ? self::$maybe : self::$checked,
        ]);
    }
}
