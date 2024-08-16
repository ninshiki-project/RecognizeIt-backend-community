<?php

namespace App\Providers;

use App\Models\Departments;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Spatie\Emoji\Emoji;

class AboutInfoServiceProvider extends ServiceProvider
{
    //https://unicode.org/Public/emoji/13.1/emoji-test.txt
    public static string $checked = "<fg=green;options=bold>\u{2714} Completed</>";

    public static string $times = "<fg=red;options=bold>\u{274C}  Not Completed</>";

    public static string $maybe = "<fg=yellow;options=bold>\u{2753} Maybe</>";

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
    }
}
