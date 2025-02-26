<?php

namespace App\Providers\Filament;

use App\Filament\Concerns\InteractsWithQuotes;
use App\Filament\Pages\Backups;
use App\Filament\Pages\Login;
use App\Filament\Pages\ProfilePage;
use App\Filament\Resources\PostingLimitResource;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ninshikiProject\GeneralSettings\GeneralSettingsPlugin;
use Orion\FilamentGreeter\GreeterPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AdminPanelProvider extends PanelProvider
{
    use InteractsWithQuotes;

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('web')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Teal,
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->unsavedChangesAlerts()
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('13rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('My Profile')
                    ->url(fn (): string => ProfilePage::getUrl())
                    ->icon('heroicon-o-user-circle'),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                GreeterPlugin::make()
                    ->timeSensitive(morningStart: 5, afternoonStart: 13, eveningStart: 18, nightStart: 20)
                    ->avatar(size: 'w-16 h-16', url: fn () => Filament::auth()->user()?->getFilamentAvatarUrl())
                    ->title(fn () => $this->todayQuote())
                    ->sort(-6)
                    ->columnSpan('full'),

                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),

                GeneralSettingsPlugin::make()
                    ->setSort(PostingLimitResource::getNavigationSort() - 1)
                    ->setTitle('API Settings')
                    ->setNavigationLabel('API Settings')
                    ->setNavigationParentItem('Settings'),

                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPolingInterval('10s')
                    ->usingQueue('default')
                    ->authorize(fn (): bool => auth()->user()->hasPermissionTo('system backup'))
                    ->usingPage(Backups::class),
            ]);
    }
}
