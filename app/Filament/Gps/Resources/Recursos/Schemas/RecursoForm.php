<?php

namespace App\Filament\Gps\Resources\Recursos\Schemas;

use App\Filament\Components\AttachmentUpload;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RecursoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([

                    TextEntry::make('text')
                        ->label('Justificativa do Candidato')
                        ->columnSpanFull(),

                    TextEntry::make('text')
                        ->visible(fn($record) => $record->hasMedia('anexo_candidato'))
                        ->hiddenLabel()
                        ->formatStateUsing(function ($record) {
                            $link = tempMediaUrl($record, 'anexo_candidato');
                            $text = '<a href="' . $link . '" target="_blank" class="hover:underline">Visualizar Anexo</a>';

                            return $link ? $text : '-';
                        })
                        ->color('primary')
                        ->html(),

                    Select::make('result')
                        ->label('Resultado')
                        ->required()
                        ->options([
                            'D' => 'Deferido',
                            'I' => 'Indeferido',
                            'P' => 'Parcialmente Deferido',
                        ]),

                    Textarea::make('response')
                        ->label('Resposta Avaliador')
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(255),

                    AttachmentUpload::make('anexo_avaliador')
                        ->columnSpanFull()
                        ->disk('public')
                        ->required(false)
                        ->collection('anexo_avaliador')
                ])
            ]);
    }
}
