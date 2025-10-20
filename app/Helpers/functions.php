<?php

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Enums\Platform;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

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
        ->assets([
            Css::make('filament-stylesheet', resource_path('css/filament.css'))
        ]);
}

function tempMediaUrl(Model $record, $collection = 'default')
{
    if (!$record->HasMedia($collection)) return;

    return route('media.temp', $record->getFirstMedia($collection)?->uuid);
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
