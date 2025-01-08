<?php

namespace App\Providers;

use App\Http\Services\Zoho\Zoho;
use Illuminate\Support\ServiceProvider;

class SocialServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind('Zoho', function () {
            return new Zoho;
        });
    }
}
