<?php

namespace App\Filament\Candidato\Pages;

use App\Models\Appeal;
use App\Models\Application;
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

class Recurso extends Page implements HasSchemas
{
    use InteractsWithSchemas, InteractsWithActions, InteractsWithFormActions;

    protected string $view = 'filament.candidato.pages.recurso';
    protected static ?string $slug = 'recurso';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;
    protected static string | UnitEnum | null $navigationGroup = 'Área do Candidato';
    protected static ?int $navigationSort = 2;

    public ?array $data = [];
    public ?Application $application = null;

    public $options = [];
    public $appeals = [];

    private $service;

    public function __construct()
    {
        $this->service = app(AppealService::class);
    }

    public function mount(): void
    {
        abort_unless(Auth::guard('candidato')->check(), 403);

        $this->form->fill();

        $this->options = Application::query()
            ->where('candidate_id', Auth::guard('candidato')->id())
            ->canAppeal()
            ->with('position')
            ->get()
            ->mapWithKeys(fn($app) => [
                $app->id => "{$app->code} - {$app->position->description}",
            ]);

        $results = Appeal::query()
            ->where('candidate_id', Auth::guard('candidato')->id())
            ->available()
            ->latest()
            ->get();

        $submitted = Appeal::query()
            ->where('candidate_id', Auth::guard('candidato')->id())
            ->submitted()
            ->latest()
            ->get();

        $this->appeals = $results->isNotEmpty() ? $results : $submitted;
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
                Section::make('Efetuar Recurso')
                    ->description('Selecione sua inscrição para abrir um recurso.')
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
                $set('process', null);
                $set('stage', null);
                $set('text', null);

                // pega a inscrição selecionada pelo usuário
                $this->application = Application::with('process.appeal_stage')->find($state);
                if (!$this->application) return;

                // pega a etapa de recurso ativa do processo pro qual o usuário se inscreveu
                // sabe-se que a última é a ativa pois no GPS a regra só deixa criar uma etapa por vez
                $stage = $this->application->activeAppealStage();
                if ($stage) {
                    $set('process', $this->application->process->title);
                    $set('stage', $stage->description);
                }
            });
    }

    private function getAppealFormSection()
    {
        return Group::make()
            ->columns(2)
            ->schema([
                TextInput::make('process')->label('Processo Seletivo')->disabled(),
                TextInput::make('stage')->label('Etapa')->disabled(),
                Textarea::make('text')
                    ->label('Justificativa')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('anexo_candidato')
                    ->columnSpanFull()
                    ->maxFiles(1)
                    ->rules(['file', 'mimes:pdf', 'max:2048']),
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
            ->visible(fn() => $this->application && $this->application->canAppeal());
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

            redirect()->route('filament.candidato.pages.recurso');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
