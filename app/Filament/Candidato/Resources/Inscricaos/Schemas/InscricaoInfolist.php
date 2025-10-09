<?php

namespace App\Filament\Candidato\Resources\Inscricaos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InscricaoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('DADOS PESSOAIS')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('candidate.nome')->label('Nome'),
                        TextEntry::make('candidate.cpf')->label('CPF'),
                        TextEntry::make('candidate.ci')->label('RG'),
                    ]),

                Section::make('DADOS DA INSCRIÇÃO')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('code')->label('Inscrição'),
                        TextEntry::make('process.titulo')->label('Processo Seletivo'),
                        TextEntry::make('position.descricao')->label('Vaga'),
                        TextEntry::make('quota.descricao')->label('Tipo de Vaga'),
                    ]),

                Section::make('ATENDIMENTO ESPECIAL')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('requires_assistance')
                            ->label('')
                            ->badge()
                            ->colors([
                                'success' => 'S',
                                'gray' => 'N',
                            ])
                            ->formatStateUsing(fn($state) => $state === 'S' ? 'solicitado' : 'não solicitado'),

                        TextEntry::make('assistance_details')
                            ->label('Qual Atendimento')
                            ->visible(fn($record) => $record->requires_assistance === 'S'),
                    ]),
            ]);
    }
}
