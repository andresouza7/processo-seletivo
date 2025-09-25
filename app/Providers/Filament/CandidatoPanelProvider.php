<?php

namespace App\Providers\Filament;

use App\Filament\Candidato\Pages\Auth\Cadastro;
use App\Filament\Candidato\Pages\Auth\EditProfile;
use App\Filament\Candidato\Pages\Auth\Login;
use App\Filament\App\Resources\ProcessoSeletivoResource;
use App\Filament\Candidato\Pages\Auth\ConfirmEmail;
use App\Helpers\FilamentSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
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
use App\Filament\Candidato\Pages\Auth\RequestPasswordReset;
use App\Filament\Pages\ResetPassword;

class CandidatoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        applyFilamentPanelStyles($panel);

        return $panel
            ->id('candidato')
            ->path('candidato')
            ->viteTheme('resources/css/filament/candidato/theme.css')
            ->discoverResources(in: app_path('Filament/Candidato/Resources'), for: 'App\\Filament\\Candidato\\Resources')
            ->discoverPages(in: app_path('Filament/Candidato/Pages'), for: 'App\\Filament\\Candidato\\Pages')
            ->discoverWidgets(in: app_path('Filament/Candidato/Widgets'), for: 'App\\Filament\\Candidato\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
            ])
            ->collapsibleNavigationGroups(false)
            ->navigationItems([
                NavigationItem::make('Nova Inscrição')
                    ->url('/candidato/inscricoes/create')
                    ->icon('heroicon-o-plus')
                    ->group('Área do Candidato')
                    ->sort(2)
                    ->isActiveWhen(fn(): bool => request()->routeIs('filament.candidato.resources.inscricoes.create')),
                NavigationItem::make('Alterar Senha')
                    ->url('/candidato/profile')
                    ->icon('heroicon-o-lock-closed')
                    ->group('Área do Candidato')
                    ->sort(4)
                    ->isActiveWhen(fn(): bool => request()->routeIs('filament.candidato.auth.profile')),
            ])
            ->navigationGroups([
                'Área do Candidato',
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
            ->authGuard('candidato')
            ->login(Login::class)
            ->registration(Cadastro::class)
            ->profile(EditProfile::class)
            ->authPasswordBroker('inscricao_pessoa')
            ->passwordReset(RequestPasswordReset::class, ResetPassword::class)
            // ->emailVerification(ConfirmEmail::class)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
