<?php

namespace App\Providers\Filament;

use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use App\Filament\Candidato\Pages\Auth\RequestPasswordReset;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class GpsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $path = env('APP_ENV') === 'production' ? '' : 'gps';
        $domain = env('APP_ENV') === 'production' ? env('GPS_URL') : null;

        return $panel
            ->id('gps')
            ->path($path)
            ->domain($domain)
            ->viteTheme('resources/css/filament/gps/theme.css')
            // ->maxContentWidth(Width::Full)
            ->login()
            ->brandName('GPS - UEAP')
            ->profile()
            ->databaseNotifications()
            ->colors([
                'primary' => '#017840',
            ])
            ->font('Montserrat')
            ->discoverResources(in: app_path('Filament/Gps/Resources'), for: 'App\\Filament\\Gps\\Resources')
            ->discoverPages(in: app_path('Filament/Gps/Pages'), for: 'App\\Filament\\Gps\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->resourceCreatePageRedirect('index')
            ->discoverWidgets(in: app_path('Filament/Gps/Widgets'), for: 'App\\Filament\\Gps\\Widgets')
            ->widgets([
                AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
            ->passwordReset(RequestPasswordReset::class)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
