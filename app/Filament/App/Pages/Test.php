<?php

namespace App\Filament\App\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;

class Test extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.app.pages.test';

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
        ]);
    }
}
