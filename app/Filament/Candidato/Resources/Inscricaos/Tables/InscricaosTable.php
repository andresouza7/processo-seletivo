<?php

namespace App\Filament\Candidato\Resources\Inscricaos\Tables;

use App\Models\Application;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Grid;
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
                $query = Application::query()->orderBy('id', 'desc');

                // If there's no logged candidate, return an empty result set (don't throw)
                if (! $guardId) {
                    // This forces no results but keeps a valid Builder instance
                    return $query->whereRaw('0 = 1');
                }

                return $query->where('candidate_id', $guardId);
            })
            ->heading('Inscrições realizadas')
            ->description('Use o filtro para localizar uma inscrição.')
            ->columns([
                Grid::make(4)
                    ->schema([
                        TextColumn::make('code')
                            ->label('Código')
                            ->searchable()
                            ->weight('bold')
                            ->size('sm')
                            ->columnSpan(1),

                        Stack::make([
                            TextColumn::make('process.title')
                                ->label('Processo Seletivo')
                                ->searchable()
                                ->size('sm'),

                            TextColumn::make('position.description')
                                ->label('Cód. Vaga')
                                ->searchable()
                                ->size('sm'),

                            TextColumn::make('quota.description')
                                ->label('Tipo')
                                ->size('sm'),
                        ])->columnSpan(2),

                        TextColumn::make('created_at')
                            ->label('Processo Seletivo')
                            ->size('xs')
                            ->extraAttributes(['class' => 'text-gray-500'])
                            ->html()
                            ->formatStateUsing(fn($state) => "Envio em:<br>" . $state->format('d/m/Y H:i'))
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
