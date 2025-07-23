<?php

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Enums\Platform;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

function applyFilamentPanelStyles(Panel $panel): Panel
{
    return $panel
        ->colors([
            'primary' => '#017840',
        ])
        ->font('Montserrat')
        // ->brandLogo(asset('img/logo.png'))
        // ->brandLogoHeight('40px')
        ->darkMode(false)
        ->renderHook(
            // Inclui o plugin do vlibras
            PanelsRenderHook::PAGE_START,
            fn(): View => view('filament.vlibras'),
        )
        ->renderHook(
            PanelsRenderHook::BODY_END,
            fn() => view('filament.custom-footer'),
        )
        ->assets([
            Css::make('filament-stylesheet', resource_path('css/filament.css'))
        ]);
}

class HtmlHelper
{
    public static function sliceBodyContent(string $html): string
    {
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)) {
            return $matches[1];
        }

        return $html;
    }
}
