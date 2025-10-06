<?php

namespace App\Filament\Candidato\Pages;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Exception;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use App\Models\InscricaoPessoa;
use Canducci\Cep\Facades\Cep;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MeusDados extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';
    protected static string | \UnitEnum | null $navigationGroup = 'Área do Candidato';
    protected static ?int $navigationSort = 3;
    protected string $view = 'filament.candidato.pages.meus-dados';
    public InscricaoPessoa $record;
    public ?array $data = [];

    public function mount(): void
    {
        $record       = InscricaoPessoa::where('idpessoa', Auth::guard('candidato')->id())->firstOrFail();
        $this->record = $record;

        $this->form->fill([
            ...$record->toArray(),
            'usar_nome_social' => filled($record->nome_social),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Dados Pessoais')
                    ->description('Entre em contato com a DIPS para alterar estes dados')
                    ->columns(3)
                    ->schema([
                        TextInput::make('nome')
                            ->label('Nome')
                            ->disabled(fn () => filled($this->record->nome))
                            ->required(),

                        TextInput::make('mae')
                            ->label('Nome da Mãe')
                            ->disabled(fn () => filled($this->record->mae))
                            ->required(),

                        TextInput::make('cpf')
                            ->label('CPF')
                            ->disabled()
                            ->required(),

                        TextInput::make('ci')
                            ->label('RG')
                            ->disabled(fn () => filled($this->record->ci))
                            ->required(),

                        DatePicker::make('data_nascimento')
                            ->label('Data de Nascimento')
                            ->disabled(fn () => filled($this->record->data_nascimento))
                            ->required(),

                        TextInput::make('email')
                            ->label('Email')
                            ->disabled(fn () => filled($this->record->email))
                            ->email()
                            ->required(),

                        Select::make('sexo')
                            ->label('Sexo')
                            ->disabled()
                            ->options([
                                'M' => 'Masculino',
                                'F' => 'Feminino',
                            ])
                            ->required()
                            ->columnSpanFull(), // Ocupa linha inteira

                        TextInput::make('identidade_genero')
                            ->label('Identidade de Gênero')
                            ->options([
                                'C'  => 'Cisgênero',
                                'T'  => 'Transgênero',
                                'NB' => 'Não-binário',
                                'TV' => 'Travesti',
                                'O'  => 'Outro',
                            ])
                            ->required()
                            ->columnSpanFull(),

                        Checkbox::make('usar_nome_social')
                            ->label('Usar nome social')
                            ->reactive()
                            ->columnSpanFull(),

                        TextInput::make('nome_social')
                            ->label('Nome Social')
                            ->visible(fn ($get) => $get('usar_nome_social'))
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Endereço e contato')
                    ->columns(2)
                    ->schema([

                        TextInput::make('cep')
                            ->label('CEP')
                            ->required()
                            ->rules(['formato_cep'])
                            ->mask('99999-999')
                            ->debounce(1000)
                            ->afterStateUpdated(function (callable $set, $state, $livewire) {
                                if (blank($state)) return;

                                try {
                                    $cepData = Cep::find($state);
                                    $cep = $cepData->getCepModel();

                                    if ($cep) {
                                        $set('endereco', $cep->logradouro);
                                        $set('bairro', $cep->bairro);
                                        $set('cidade', $cep->localidade);
                                    }
                                } catch (Exception $e) {
                                    // Handle error silently
                                }
                            }),
                        TextInput::make('endereco')
                            ->label('Endereço')
                            ->required(),
                        TextInput::make('numero')
                            ->label('Número')
                            ->numeric()
                            ->required(),
                        TextInput::make('complemento')
                            ->label('Complemento'),
                        TextInput::make('bairro')
                            ->label('Bairro')
                            ->required(),
                        TextInput::make('cidade')
                            ->label('Cidade')
                            ->required(),
                        TextInput::make('telefone')
                            ->label('Telefone')
                            ->required(),
                    ]),

                Actions::make([
                    Action::make('submit')
                        ->label('Salvar')
                        ->submit('save')
                        ->color('primary'),
                ]),
            ]);
    }

    public function save()
    {
        $data = $this->form->getState();

        if (!$data['usar_nome_social']) {
            $data['nome_social'] = "";
        }

        $this->record->update($data);

        Notification::make()
            ->success()
            ->title('Tudo certo')
            ->body('Dados atualizados com sucesso!')
            ->send();

        $this->redirectRoute('filament.candidato.pages.dashboard');
    }
}
