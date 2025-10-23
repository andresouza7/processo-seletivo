<?php

namespace App\Filament\Candidato\Pages;

use App\Filament\Components\AttachmentUpload;
use App\Models\Appeal;
use App\Models\AppealStage;
use App\Models\Application;
use App\Models\Process;
use App\Services\SelectionProcess\AppealService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\InteractsWithMedia;
use UnitEnum;
use Filament\Schemas\Components\Text;

class Recurso extends Page implements HasSchemas
{
    use InteractsWithSchemas, InteractsWithActions, InteractsWithFormActions;

    protected string $view = 'filament.candidato.pages.recurso';
    protected static ?string $slug = 'recurso/{record}';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];
    public ?Application $application = null;
    public ?AppealStage $stage = null;

    public $options = [];
    public $appeals = [];

    private $service;

    public function __construct()
    {
        $this->service = app(AppealService::class);
    }

    public function mount(AppealStage $record): void
    {
        $canAccess = $record->accepts_appeal || $record->has_result;

        abort_unless($canAccess, 403);

        $this->form->fill();

        $this->stage = $record;

        $this->options = $this->service->listAppealableApplications($record->process)
            ->mapWithKeys(fn($app) => [
                $app->id => "{$app->code} - {$app->position->description}",
            ]);

        $submitted = $this->service->listSubmittedAppeals();

        $results = $this->service->listAppealsWithResults();

        $this->appeals = $results->isNotEmpty() ? $results : $submitted;
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
                Section::make('Efetuar Recurso')
                    ->description('Selecione sua inscrição para enviar um recurso.')
                    ->schema([
                        $this->getSelectApplicationSection(),
                        $this->getAppealFormSection()
                    ]),
            ]);
    }

    private function getSelectApplicationSection()
    {
        return Select::make('application_id')
            ->label('Inscrição')
            ->live()
            ->options(fn() => $this->options)
            ->afterStateUpdated(function (callable $set, $state) {
                // Reset everything when deselecting
                $this->application = null;
                $set('text', null);

                // pega a inscrição selecionada pelo usuário
                $this->application = Application::with('process.appeal_stage')->find($state);
            });
    }

    private function getAppealFormSection()
    {
        return Group::make()
            ->columns(2)
            ->schema([
                Textarea::make('text')
                    ->label('Justificativa')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
                AttachmentUpload::make('anexo_candidato')
                    ->columnSpanFull()
                    ->required(false)
                    ->disk('local'),
                Actions::make([
                    Action::make('create')
                        ->label('Enviar Recurso')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Confirme o envio')
                        ->modalDescription('Tem certeza que deseja enviar este recurso? Não será possível reenviá-lo.')
                        ->modalSubmitActionLabel('Sim, enviar')
                        ->action(fn() => $this->create())
                ]),
            ])
            ->visible(fn() => $this->application);
    }

    public function create(): void
    {
        $this->validate();

        if (!$this->application) {
            return;
        }

        try {
            $this->service->createFromApplication($this->application, $this->data);

            Notification::make()
                ->title('Recurso registrado com sucesso')
                ->body('Recebemos seu recurso e ele está em análise.')
                ->success()
                ->send();

            redirect(static::getUrl(['record' => $this->stage]));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
