<?php

namespace App\Filament\Gps\Resources\Recursos\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;

class RecursoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('descricao')
                    ->columnSpanFull()
                    ->disabled(),
                Actions::make([
                    Action::make('ver_anexo')
                        ->visible(fn($record) => $record->hasMedia('anexo_recurso'))
                        ->url(fn($record) => route('recurso.anexo', $record->idrecurso))
                        ->openUrlInNewTab()
                ])->columnSpanFull(),
                Select::make('situacao')
                    ->required()
                    ->options([
                        'D' => 'Deferido',
                        'I' => 'Indeferido',
                        'P' => 'Parcialmente Deferido',
                    ]),
                Textarea::make('resposta')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),

                SpatieMediaLibraryFileUpload::make('anexo_avaliador')
                    ->columnSpanFull()
                    ->maxFiles(1)
                    ->disk('local')
                    ->collection('anexo_avaliador')
                    ->rules(['file', 'mimes:pdf', 'max:2048'])
            ]);
    }
}
