<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonInterval;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;
use Laravel\Pulse\Facades\Pulse;

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
        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });

        // Filament Notification alignment
        Notifications::alignment(Alignment::Right);
        Notifications::verticalAlignment(VerticalAlignment::End);

        /**
         *  ==========================================================
         * |              Request Throttle                           |
         * ===========================================================
         */
        // Throttle for login
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5, decayMinutes: 5)->by($request->input('email').'|'.$request->ip());
        });
        RateLimiter::for('post', function (Request $request) {
            $key = $request->user()->id;

            return Limit::perMinute(1, decayMinutes: 10)->by($key)
                ->response(function (Request $request, array $headers) {
                    $retryAfter = CarbonInterval::seconds($headers['Retry-After'])->forHumans();

                    return response("You're to fast in posting. please try again after ".$retryAfter, 429, $headers);
                });
        });
        // Throttle for request forgot password
        RateLimiter::for('forgotPassword', function (Request $request) {
            return Limit::perMinute(1, decayMinutes: 5)->by($request->input('email').'|'.$request->ip());
        });

        // Laravel Pulse disable its path to make the Filament page is only accessible
        Gate::define('viewPulse', function (User $user) {
            return false;
        });
        Pulse::user(fn ($user) => [
            'name' => $user->name,
            'extra' => $user->email,
            'avatar' => $user->getFilamentAvatarUrl(),
        ]);


        /** Laravel Feature Flag */
        Feature::discover();

    }
}
