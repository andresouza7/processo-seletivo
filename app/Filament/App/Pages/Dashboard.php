<?php

namespace App\Filament\App\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.app.pages.dashboard';

    protected static ?string $navigationLabel = 'Início';

    public function getTitle(): string
    {
        return '';
    }
}
