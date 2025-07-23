<?php

namespace App\Filament\Candidato\Widgets;

use Filament\Widgets\Widget;

class AcessoRapidoMenu extends Widget
{
    protected static string $view = 'filament.candidato.widgets.acesso-rapido-menu';

    protected int | string | array $columnSpan = 'full';
}
