<?php

namespace App\Filament\Candidato\Resources\Applications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('DADOS PESSOAIS')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('candidate.name')->label('Nome'),
                        TextEntry::make('candidate.cpf')->label('CPF'),
                        TextEntry::make('candidate.rg')->label('RG'),
                    ]),

                Section::make('DADOS DA INSCRIÇÃO')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('code')->label('Inscrição'),
                        TextEntry::make('process.title')->label('Processo Seletivo'),
                        TextEntry::make('position.description')->label('Vaga'),
                        TextEntry::make('quota.description')->label('Tipo de Vaga'),
                    ]),

                Section::make('ATENDIMENTO ESPECIAL')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('requires_assistance')
                            ->label('Atendimento Especial')
                            ->badge()
                            ->colors([
                                'success' => true,
                                'gray' => false,
                            ])
                            ->formatStateUsing(fn($state) => $state === true ? 'solicitado' : 'não solicitado'),

                        TextEntry::make('assistance_details')
                            ->label('Qual Atendimento')
                            ->visible(fn($record) => $record->requires_assistance),
                    ]),
            ]);
    }
}
