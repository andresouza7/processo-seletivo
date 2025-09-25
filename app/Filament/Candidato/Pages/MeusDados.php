<?php

namespace App\Filament\Candidato\Pages;

use App\Models\InscricaoPessoa;
use Canducci\Cep\Facades\Cep;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MeusDados extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Área do Candidato';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.candidato.pages.meus-dados';

    public InscricaoPessoa $record;
    public ?array $data = [];

    public function mount(): void
    {
        $record = InscricaoPessoa::where('idpessoa', Auth::guard('candidato')->id())->firstOrFail();
        $this->record = $record;
        $this->form->fill([
            ...$record->toArray(),
            'usar_nome_social' => !blank($record->nome_social),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('Dados Pessoais')
                    ->description('Entre em contato com a DIPS para alterar estes dados')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('nome')
                            ->label('Nome')
                            ->disabled()
                            ->required(),

                        Forms\Components\TextInput::make('mae')
                            ->label('Nome da Mãe')
                            ->disabled()
                            ->required(),

                        Forms\Components\TextInput::make('cpf')
                            ->label('CPF')
                            ->disabled()
                            ->required(),

                        Forms\Components\TextInput::make('ci')
                            ->label('RG')
                            ->disabled()
                            ->required(),

                        Forms\Components\DatePicker::make('data_nascimento')
                            ->label('Data de Nascimento')
                            ->disabled()
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->disabled()
                            ->email()
                            ->required(),

                        Forms\Components\Select::make('sexo')
                            ->label('Sexo')
                            ->options([
                                'F' => 'Feminino',
                                'M' => 'Masculino',
                                'O' => 'Outro'
                            ])
                            ->reactive()
                            ->required()
                            ->columnSpanFull(), // Ocupa linha inteira

                        Forms\Components\TextInput::make('identidade_genero')
                            ->label('Identidade de Gênero')
                            ->visible(fn($get) => $get('sexo') === 'O')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Checkbox::make('usar_nome_social')
                            ->label('Usar nome social')
                            ->reactive()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('nome_social')
                            ->label('Nome Social')
                            ->visible(fn($get) => $get('usar_nome_social'))
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Endereço e contato')
                    ->columns(2)
                    ->schema([

                        Forms\Components\TextInput::make('cep')
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
                            } catch (\Exception $e) {
                                // Handle error silently
                            }
                        }),
                        Forms\Components\TextInput::make('endereco')
                            ->label('Endereço')
                            ->required(),
                        Forms\Components\TextInput::make('numero')
                            ->label('Número')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('complemento')
                            ->label('Complemento'),
                        Forms\Components\TextInput::make('bairro')
                            ->label('Bairro')
                            ->required(),
                        Forms\Components\TextInput::make('cidade')
                            ->label('Cidade')
                            ->required(),
                        Forms\Components\TextInput::make('telefone')
                            ->label('Telefone')
                            ->required(),
                    ]),

                Actions::make([
                    Action::make('submit')
                        ->label('Salvar')
                        ->submit('save')
                        ->color('primary'),
                ])
            ]);
    }

    public function save()
    {
        $data = $this->form->getState();

        if (!$data['usar_nome_social']) {
            $data['nome_social'] = "";
        }
        if ($data['sexo'] !== 'O') {
            $data['identidade_genero'] = "";
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
