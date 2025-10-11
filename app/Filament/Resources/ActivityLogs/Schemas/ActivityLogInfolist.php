<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Log')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('event')
                            ->label('Tipo de Ação')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                default   => 'gray',
                            }),

                        TextEntry::make('subject_type')
                            ->label('Entidade Afetada')
                            ->formatStateUsing(fn($state, $record) => class_basename($state) . " (#{$record->subject_id})")
                            ->color('primary'),

                        TextEntry::make('causer_type')
                            ->label('Responsável')
                            ->formatStateUsing(fn($state, $record) => $record->causer
                                ? class_basename($state) . " (#{$record->causer_id})"
                                : 'Sistema')
                            ->color('secondary'),

                        TextEntry::make('created_at')
                            ->label('Data')
                            ->dateTime('d/m/Y H:i'),
                    ]),

                Section::make('Propriedades')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        KeyValueEntry::make('properties')
                            ->label('Propriedades')
                            ->hidden(fn($record) => isset($record->properties['attributes']))
                            ->columnSpanFull(),

                        KeyValueEntry::make('properties.old')
                            ->label('Antes da Alteração')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor Antigo')
                            ->placeholder('Nenhum valor antigo'),

                        KeyValueEntry::make('properties.attributes')
                            ->label('Após a Alteração')
                            ->keyLabel('Campo')
                            ->valueLabel('Novo Valor')
                            ->placeholder('Nenhum valor novo'),
                    ]),
            ]);
    }
}
