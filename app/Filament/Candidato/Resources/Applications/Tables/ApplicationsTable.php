<?php

namespace App\Filament\Candidato\Resources\Applications\Tables;

use App\Models\Application;
use App\Services\SelectionProcess\ApplicationService;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->where('candidate_id', Auth::guard('candidato')->id())
            )
            ->defaultSort('id', 'desc')
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
                            ->formatStateUsing(fn($state) => "Inscrito em:<br>" . $state->format('d/m/Y H:i'))
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
