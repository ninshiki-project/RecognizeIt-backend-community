<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Zoho Socialite
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('zoho', \SocialiteProviders\Zoho\Provider::class);
        });

        // Scramble API Documentation Generator
        Scramble::extendOpenApi(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', '')
            );
        });

        /**
         *  ==========================================================
         * |              Request Throttle                           |
         * ===========================================================
         */
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5, decayMinutes: 5)->by($request->input('email').'|'.$request->ip());
        });

    }
}
