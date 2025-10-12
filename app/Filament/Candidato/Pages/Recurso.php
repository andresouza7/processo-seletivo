<?php

namespace App\Filament\Candidato\Pages;

use App\Models\Appeal;
use App\Models\Application;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\InteractsWithMedia;
use UnitEnum;

class Recurso extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.candidato.pages.recurso';
    protected static ?string $slug = 'recurso';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;
    protected static string | UnitEnum | null $navigationGroup = 'Área do Candidato';
    protected static ?int $navigationSort = 2;

    public ?array $data = [];
    public ?Application $application = null;
    public ?Appeal $appeal = null;

    public function mount(): void
    {
        abort_unless(Auth::guard('candidato')->check(), 403);
        $this->form->fill();
    }

    private function canAppeal(?int $appId): bool
    {
        if (!$appId) {
            return false;
        }

        $app = Application::with('process.appeal_stage')->find($appId);
        if (!$app) {
            return false;
        }

        $stage = $app->process->appeal_stage()->latest()->first();
        if (!$stage || !$stage->can_appeal) {
            return false;
        }

        $appealExists = Appeal::where('appeal_stage_id', $stage->id)
            ->where('application_id', $app->id)
            ->exists();

        return !$appealExists || $stage->allow_many;
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
                Section::make('Central de Recursos')
                    ->description('Selecione sua inscrição para abrir ou acompanhar recursos.')
                    ->schema([
                        Select::make('application_id')
                            ->label('Inscrição')
                            ->live()
                            ->options(
                                fn() =>
                                Application::where('candidate_id', Auth::guard('candidato')->id())
                                    ->whereHas('process', function ($query) {
                                        $query->whereHas('appeal_stage', function ($subquery) {
                                            $today = now()->toDateString();
                                            $subquery->whereDate('submission_start_date', '<=', $today)
                                                ->whereDate('submission_end_date', '>=', $today);
                                        });
                                    })
                                    ->with('position')
                                    ->get()
                                    ->mapWithKeys(fn($app) => [
                                        $app->id => "{$app->code} - {$app->position->description}",
                                    ])
                            )
                            ->afterStateUpdated(function (callable $set, $state) {
                                // Reset everything when deselecting
                                $this->application = null;
                                $this->appeal = null;
                                $set('process', null);
                                $set('stage', null);
                                $set('text', null);

                                $this->application = Application::with('process.appeal_stage')->find($state);
                                if (!$this->application) return;

                                $stage = $this->application->process->appeal_stage()->latest()->first();
                                if ($stage) {
                                    $set('process', $this->application->process->title);
                                    $set('stage', $stage->description);

                                    $this->appeal = Appeal::where('application_id', $this->application->id)
                                        ->where('appeal_stage_id', $stage->id)
                                        ->first();

                                    if ($this->appeal) {
                                        $this->form->fill($this->appeal->toArray());
                                    }
                                }
                            }),

                        Group::make()
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
                                        ->action('create')
                                        ->color('primary'),
                                ]),
                            ])
                            ->visible(fn() => $this->application && $this->canAppeal($this->application->id)),
                    ]),
            ]);
    }

    public function create(): void
    {
        $this->validate();

        if (!$this->application) return;

        DB::transaction(function () {
            // Create the appeal
            $this->appeal = Appeal::create([
                'candidate_id'     => $this->application->candidate_id,
                'application_id'   => $this->application->id,
                'appeal_stage_id'  => $this->application->process->appeal_stage()->latest()->first()->id,
                'text'             => $this->data['text'],
            ]);

            // Attach uploaded file(s)
            if (!empty($this->data['anexo_candidato'])) {
                foreach ((array) $this->data['anexo_candidato'] as $file) {
                    $this->appeal
                        ->addMedia($file->getRealPath())
                        ->usingFileName($file->getClientOriginalName())
                        ->toMediaCollection('anexo_candidato', 'local');
                }
            }
        });

        Notification::make()
            ->title('Recurso registrado com sucesso')
            ->body('Recebemos seu recurso e ele está em análise.')
            ->success()
            ->send();
    }
}
