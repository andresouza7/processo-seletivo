<?php

namespace App\Filament\Gps\Resources\Processes\Pages;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Gps\Resources\Processes\ProcessResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageAppealStage extends ManageRelatedRecords
{
    protected static string $resource = ProcessResource::class;
    protected static ?string $title = 'Gerenciar Etapas de Appeal';
    protected static ?string $navigationLabel = 'Etapas de Recurso';
    protected static ?string $breadcrumb = 'Etapas de Recurso';
    protected static string $relationship = 'appeal_stage';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')
                    ->label('Descrição')
                    ->required()
                    ->placeholder('Ex: Resultado preliminar')
                    ->columnSpanFull(),
                Fieldset::make('Período de recebimento de recursos')
                    ->schema([
                        DatePicker::make('submission_start_date')
                            ->label('Data Início')
                            ->required()
                            ->helperText('Período para recebimento dos recursos'),
                        DatePicker::make('submission_end_date')
                            ->label('Data Fim')
                            ->required(),
                    ]),
                Fieldset::make('Período de divulgação dos resultados')
                    ->schema([
                        DatePicker::make('result_start_date')
                            ->label('Data Início')
                            ->required()
                            ->helperText('Período para consulta dos resultados'),
                        DatePicker::make('result_end_date')
                            ->label('Data Fim')
                            ->required(),
                    ]),
                Checkbox::make('has_attachments')
                    ->label('Requer envio de anexos?')
                    ->helperText('Será disponibilizado campo de upload de pdf ao usuário')
                    ->columnSpanFull(),
                Checkbox::make('allow_many')
                    ->label('Permitir mais de um recurso por candidato')
                    ->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->heading('Etapas')
            ->modelLabel('Etapa de Recurso')
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('description')->label('Descrição'),
                TextColumn::make('submission_start_date')->label('Início')->date('d/m/Y'),
                TextColumn::make('submission_end_date')->label('Fim')->date('d/m/Y'),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->before(function (CreateAction $action) {
                        $processo = $this->getRecord();

                        $exists = $processo->appeal_stage()
                            ->whereDate('submission_end_date', '>=', now())->exists();

                        if ($exists) {
                            Notification::make()
                                ->title('Já existe uma etapa em andamento.')
                                ->body('Você só pode criar outra após esta ser finalizada.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),

            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
