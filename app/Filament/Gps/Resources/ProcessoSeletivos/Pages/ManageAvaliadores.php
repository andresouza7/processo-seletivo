<?php

namespace App\Filament\Gps\Resources\ProcessoSeletivos\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use App\Filament\Gps\Resources\ProcessoSeletivos\ProcessoSeletivoResource;
use App\Models\Appeal;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\BulkAction;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManageAvaliadores extends ManageRelatedRecords
{
    protected static string $resource = ProcessoSeletivoResource::class;
    protected static ?string $title = 'Gerenciar Avaliadores';
    protected static string $relationship = 'appeals';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return 'Avaliadores';
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->inverseRelationship('evaluator')
            ->heading('Avaliadores')
            ->description('Atribua os recursos aos seus respectivos avaliadores')
            ->modelLabel('Avaliador')
            ->columns([
                TextColumn::make('id')
                    ->label('ID Recurso'),
                TextColumn::make('appeal_stage.description')
                    ->label('Etapa')
                    ->searchable(),
                TextColumn::make('application.position.description')
                    ->label('Vaga'),
            ])
            ->filters([
                Tables\Filters\Filter::make('pendentes')
                    ->label('Sem Avaliador')
                    ->query(fn(Builder $query) => $query->whereNull('evaluator_id'))
                    ->default(),
            ])
            ->recordActions([
                // ViewAction::make(),
                Actions\Action::make('atribuirAvaliador')
                    ->label('Atribuir Avaliador')
                    ->schema([
                        Forms\Components\Select::make('evaluator_id')
                            ->label('Usuário')
                            ->options(User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        $record->update([
                            'evaluator_id' => $data['evaluator_id'],
                        ]);
                        $this->sendAssociateNotification();
                    }),
            ])
            ->toolbarActions([
                // Bulk Assign Evaluator
                BulkAction::make('atribuirAvaliador')
                    ->label('Atribuir Avaliador')
                    ->icon('heroicon-o-user-plus')
                    ->schema([
                        Forms\Components\Select::make('evaluator_id')
                            ->label('Usuário')
                            ->options(User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->deselectRecordsAfterCompletion()
                    ->action(function (array $data, $records) {
                        Appeal::whereIn('id', $records->pluck('id'))
                            ->update(['evaluator_id' => $data['evaluator_id']]);
                        $this->sendAssociateNotification();
                    }),

                // Bulk Remove Evaluator
                BulkAction::make('removerAvaliador')
                    ->label('Remover Avaliador')
                    ->icon('heroicon-o-user-minus')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function ($records) {
                        Appeal::whereIn('id', $records->pluck('id'))
                            ->update(['evaluator_id' => null]);
                        $this->sendDissociateNotification();
                    }),
            ]);
    }

    private function sendAssociateNotification()
    {
        Notification::make()
            ->title('Salvo')
            ->body('Avaliador vinculado com sucesso.')
            ->success()
            ->send();
    }

    private function sendDissociateNotification()
    {
        Notification::make()
            ->title('Salvo')
            ->body('Avaliador desvinculado com sucesso.')
            ->success()
            ->send();
    }
}
