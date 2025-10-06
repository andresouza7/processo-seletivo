<?php

namespace App\Filament\App\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.app.pages.dashboard';

    protected static ?string $navigationLabel = 'Início';

    public function getTitle(): string
    {
        return '';
    }
}
