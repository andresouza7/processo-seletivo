<?php

namespace App\Providers\Filament;

use App\Filament\Candidato\Pages\Auth\RequestPasswordReset;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
        return $panel
            ->id('gps')
            ->path('gps')
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
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Gps/Widgets'), for: 'App\\Filament\\Gps\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
