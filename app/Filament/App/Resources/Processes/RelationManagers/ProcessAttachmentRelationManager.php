<?php

namespace App\Filament\App\Resources\Processes\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcessAttachmentRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    public function table(Table $table): Table
    {

        $today = now()->toDateString();

        return $table
            ->recordTitleAttribute('description')
            ->heading('Publicações')
            ->description('Use o campo de busca para filtrar uma informação')
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn($query) => $query->published())
            ->columns([
                Stack::make([
                    TextColumn::make('created_at')
                        ->formatStateUsing(fn($record) => sprintf(
                            "<div class='text-xs font-semibold flex gap-3'><span>%s</span></div>",
                            Carbon::parse($record->created_at)->format('d/m/Y'),
                        ))->html()
                        ->color('gray'),
                    TextColumn::make('description')
                        ->label('Descrição')
                        ->color('primary')
                        ->url(fn($record) => $record->file_url, shouldOpenInNewTab: true)
                        ->searchable(),
                ])
            ]);
    }
}
