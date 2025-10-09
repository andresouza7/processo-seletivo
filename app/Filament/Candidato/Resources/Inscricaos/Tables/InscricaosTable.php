<?php

namespace App\Filament\Candidato\Resources\Inscricaos\Tables;

use App\Models\Application;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class InscricaosTable
{
    public static function configure(Table $table): Table
    {
        // Use a safe query closure which will not blow up if the kandidat is not logged in.
        return $table
            ->query(function (): Builder {
                $guardId = Auth::guard('candidato')->id();
                $query = Application::query()->orderBy('idinscricao', 'desc');

                // If there's no logged candidate, return an empty result set (don't throw)
                if (! $guardId) {
                    // This forces no results but keeps a valid Builder instance
                    return $query->whereRaw('0 = 1');
                }

                return $query->where('idinscricao_pessoa', $guardId);
            })
            ->heading('Inscrições realizadas')
            ->description('Use a caixa de busca para filtrar uma informação.')
            ->columns([
                Stack::make([
                    TextColumn::make('code')
                        ->label('Código')
                        ->searchable()
                        ->weight('bold')
                        ->size('sm'),

                    TextColumn::make('process.titulo')
                        ->label('Processo Seletivo')
                        ->searchable()
                        ->size('sm')
                        ->color('gray'),

                    TextColumn::make('position.codigo')
                        ->label('Cód. Vaga')
                        ->size('sm')
                        ->color('gray'),

                    TextColumn::make('position.descricao')
                        ->label('Descrição')
                        ->size('sm')
                        ->color('gray'),

                    TextColumn::make('quota.descricao')
                        ->label('Tipo')
                        ->size('sm')
                        ->color('gray'),
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
