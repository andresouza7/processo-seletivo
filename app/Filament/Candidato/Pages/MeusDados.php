<?php

namespace App\Filament\Candidato\Pages;

use App\Filament\Candidato\Pages\Auth\Cadastro;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Exception;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use App\Models\Candidate;
use BackedEnum;
use Canducci\Cep\Facades\Cep;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class MeusDados extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUserCircle;
    protected static string | UnitEnum | null $navigationGroup = 'Área do Candidato';
    protected static ?int $navigationSort = 3;
    protected string $view = 'filament.candidato.pages.meus-dados';
    public Candidate $record;
    public ?array $data = [];

    public function mount(): void
    {
        $record       = Candidate::where('id', Auth::guard('candidato')->id())->firstOrFail();
        $this->record = $record;

        $this->form->fill([
            ...$record->toArray(),
            'has_social_name' => filled($record->social_name),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                ...Cadastro::getProfileSections($this->record),
                ...$this->formActions(),
            ]);
    }

    /**
     * Form Actions
     */
    private function formActions(): array
    {
        return [
            Actions::make([
                Action::make('submit')
                    ->label('Salvar')
                    ->submit('save') // method to call
                    ->color('primary'),
            ]),
        ];
    }

    public function save()
    {
        $data = $this->form->getState();

        $data['social_name'] = $data['has_social_name'] ? $data['social_name'] : null;
        $data['disability_description'] = $data['has_disability'] ? $data['disability_description'] : null;

        $this->record->update($data);

        Notification::make()
            ->success()
            ->title('Tudo certo')
            ->body('Dados atualizados com sucesso!')
            ->send();

        $this->redirectRoute('filament.candidato.pages.dashboard');
    }
}
