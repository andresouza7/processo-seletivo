<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Auth\Cadastro;
use App\Filament\App\Pages\Auth\Login;
use App\Filament\App\Pages\RequestEmailReset;
use App\Filament\App\Resources\ProcessoSeletivos\ProcessoSeletivoResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Platform;
use Filament\Support\Facades\FilamentAsset;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        applyFilamentPanelStyles($panel);

        return $panel
            ->id('app')
            ->path('')
            ->login()
            ->viteTheme('resources/css/filament/app/theme.css')
            ->navigationItems([
                NavigationItem::make('Login')
                    ->url('/candidato/login')
                    ->icon('heroicon-o-user-circle')
                    ->group('Acesso')
                    ->sort(1)
                    ->hidden(fn() => Auth::guard('candidato')->check()),
                NavigationItem::make('Registrar-se')
                    ->url('/candidato/register')
                    ->icon('heroicon-o-user-plus')
                    ->group('Acesso')
                    ->sort(2)
                    ->hidden(fn() => Auth::guard('candidato')->check()),
                ...collect([
                    'inscricoes_abertas' => ['label' => 'Inscrições Abertas', 'icon' => 'heroicon-o-pencil-square', 'sort' => 1],
                    'em_andamento'       => ['label' => 'Em Andamento', 'icon' => 'heroicon-o-folder-open',   'sort' => 2],
                    'finalizados'        => ['label' => 'Finalizados',    'icon' => 'heroicon-o-check',         'sort' => 3],
                ])->map(
                    fn($data, $key) => NavigationItem::make($data['label'])
                        ->url(fn() => ProcessoSeletivoResource::getUrl('index', [
                            'filters' => ['status' => ['value' => $key]]
                        ]))
                        ->icon($data['icon'])
                        ->group('Acompanhamento')
                        ->sort($data['sort'])
                )->toArray()
            ])
            ->navigationGroups([
                'Acompanhamento',
                'Acesso'
            ])
            ->collapsibleNavigationGroups(false)
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
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
            ]);
    }
}
