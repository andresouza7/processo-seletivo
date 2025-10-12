<?php

namespace App\Filament\Candidato\Pages\Auth;

use Filament\Auth\Pages\Register;
use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Component;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Events\Registered;
use App\Models\Candidate;
use App\Models\Pessoa;
use Filament\Forms;
use Filament\Pages\Page;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Canducci\Cep\Facades\Cep;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;

class Cadastro extends Register
{
    protected Width|string|null $maxWidth = '4xl';

    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction(),
            Action::make('voltar')
                ->url('/')
                ->label('Voltar para o site')
                ->color('gray')
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('status')
                ->formatStateUsing(fn(string $state): string => new HtmlString('<p>ℹ Complete todas as etapas para finalizar o seu cadastro.</p>')),

            Section::make('Identificação')
                ->columns(2)
                ->schema($this->getIdentificacaoSection()),

            Section::make('Informações Sociais')
                ->columns(2)
                ->schema($this->getInformacoesSociaisSection()),

            Section::make('Contato')
                ->schema($this->getContatoSection()),

            Section::make('Acesso')
                ->schema($this->getAcessoSection()),
        ]);
    }

    protected function getIdentificacaoSection(): array
    {
        return [
            TextInput::make('name')->label('Nome')->required(),
            TextInput::make('mother_name')->label('Nome da Mãe')->required(),
            $this->getCpfFormComponent(),
            TextInput::make('rg')
                ->label('RG')
                ->maxLength(20)
                ->required(),
            DatePicker::make('birth_date')
                ->label('Data de Nascimento')
                ->date()
                ->minDate('1950-01-01')
                ->maxDate(now())
                ->rules(['before_or_equal:today', 'after_or_equal:1950-01-01'])
                ->required(),

            Select::make('sex')
                ->label('Sexo')
                ->options([
                    'M' => 'Masculino',
                    'F' => 'Feminino',

                ])
                ->reactive()
                ->required(),
        ];
    }

    /////////// NOVO GRUPO DE INFORMAÇÕES
    protected function getInformacoesSociaisSection(): array
    {
        return [
            /////// SELECT: DADOS DE GÊNERO
            Select::make('gender_identity')
                ->label('Identidade de gênero')
                ->options([
                    'C' => 'Cisgênero',
                    'T' => 'Transgênero',
                    'NB' => 'Não-binário',
                    'TV' => 'Travesti',
                    'NB' => 'Não-binário',
                    'O'  => 'Outro',
                ])

                ->reactive()
                ->columnSpanFull(),

            //////////////// AVISO DINÂMICO GENERO
            Placeholder::make('o que significa esta identidade de gênero')
                ->content(fn(Get $get) => match ($get('gender_identity')) {
                    'C' => new HtmlString('<span style="color:grey;"><em>* pessoa que se identifica com o gênero que lhe foi atribuído ao nascer</em></span>'),
                    'T' => new HtmlString('<span style="color:grey;"><em>* pessoa que se identifica com um gênero diferente daquele que lhe foi atribuído ao nascer</em></span>'),
                    'NB' => new HtmlString('<span style="color:grey;"><em>* pessoa que não se identifica nem como homem e nem como mulher</em></span>'),
                    default => null,
                })
                ->visible(fn(Get $get) => in_array($get('gender_identity'), ['C', 'T', 'NB']))
                ->columnSpanFull(),

            //////////// INPUT ESPECIFICANDO GENERO: OUTROS    
            TextInput::make('gender_identity_description')
                ->label('Me identifico como')
                ->columnSpanFull()
                ->visible(fn(Get $get) => in_array($get('gender_identity'), ['O'])),

            ///////// CHECKBOX PARA USO DE NOME SOCIAL (default: não usar)
            Checkbox::make('usar_nome_social')
                ->label('Usar nome social')
                ->reactive()
                ->columnSpanFull()
                ->visible(fn(Get $get) => in_array($get('gender_identity'), ['T', 'TV', 'NB', 'O'])),

            ///////// TEXTBOX DO NOME SOCIAL
            TextInput::make('social_name')
                ->columnSpanFull()
                ->visible(fn(Get $get) => $get('usar_nome_social')),


            /////// SELECT ORIENTACAO SEXUAL  
            Select::make('sexual_orientation')
                ->label('Orientação sexual:')
                ->options([
                    'HT' => 'Heterossexual',
                    'HM' => 'Homossexual',
                    'B' => 'Bissexual',
                    'P' => 'Panssexual',
                    'A' => 'Assexual',
                ])
                ->default('HT')
                ->reactive()
                ->columnSpanFull()
                ->required(),

            //////// AVISO DINAMICO ORIENTACAO
            Placeholder::make('o que significa esta orientaçao sexual')
                ->content(fn(Get $get) => match ($get('sexual_orientation')) {
                    'HT' => new HtmlString(
                        '<span style="color:grey;"><em>* pessoa que se atrai ao gênero oposto</em></span>'
                    ),
                    'HM' => new HtmlString(
                        '<span style="color:grey;"><em>* pessoa que se atrai ao mesmo gênero</em></span>'
                    ),
                    'B' => new HtmlString(
                        '<span style="color:grey;"><em>* pessoa que se atrai a ambos os gêneros</em></span>'
                    ),
                    'P' => new HtmlString(
                        '<span style="color:grey;"><em>* pessoa que se atrai a todos os gêneros</em></span>'
                    ),
                    'A' => new HtmlString(
                        '<span style="color:grey;"><em>* pessoa que não se atrai a nenhum gênero</em></span>'
                    ),
                    default => null,
                })
                ->visible(fn(Get $get) => in_array($get('sexual_orientation'), ['HT', 'HM', 'B', 'P', 'A']))
                ->columnSpanFull(),

            ///////// CHECKBOX PARA DEFICIENCIA (default: não usar)
            Checkbox::make('has_disability')
                ->label('Possui deficiência, transtorno global do desenvolvimento, altas habilidades ou superdotação?')
                ->reactive()
                ->columnSpanFull(),

            ///////// TEXTBOX DA DEFICIENCIA
            TextInput::make('disability_description')
                ->label('Caso possuia deficiência, favor especificar qual:')
                ->columnSpanFull()
                ->visible(fn(Get $get) => $get('has_disability')),

            /////////////// SELECT: RAÇA
            Select::make('race')
                ->label('Raça/Cor')
                ->options([
                    'NA' => 'Negro de cor preta',
                    'NB' => 'Negro de cor parda',
                    'B' => 'Branca',
                    'I' => 'Indígena',
                    'A' => 'Amarela',

                ])
                ->reactive()
                ->required(),

            /////////////// SELECT: ESTADO CIVIL
            Select::make('marital_status')
                ->label('Estado civil:')
                ->options([
                    'C' => 'Casado (a)',
                    'S' => 'Solteiro (a)',
                    'D' => 'Divorciado (a)',
                    'V' => 'Viúvo(a)',
                    'U' => 'União estável',
                    'SP' => 'Separado(a)',

                ])
                ->reactive()
                ->required(),

            /////////////// SELECT: COMUNIDADE
            Select::make('community')
                ->label('Você é pertencente à comunidade:')
                ->options([
                    'R' => 'Comunidade Ribeirinha',
                    'Q' => 'Comunidade Quilombola',
                    'I' => 'Comunidade Indígena',
                    'T' => 'Comunidade Tradicional (extrativistas)',
                    'O' => 'Não se aplica',

                ])
                ->reactive()
                ->required(),

        ];
    }

    protected function getContatoSection(): array
    {
        return [
            $this->getEmailFormComponent(),
            TextInput::make('phone')
                ->label('Telefone')
                ->mask('(99)99999-9999')
                ->required(),
            Grid::make([
                'default' => 1,
                'md' => 2,
            ])
                ->schema([
                    TextInput::make('postal_code')
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
                                    $set('address', $cep->logradouro);
                                    $set('district', $cep->district);
                                    $set('city', $cep->localidade);
                                }
                            } catch (Exception $e) {
                                // Handle error silently
                            }
                        }),
                    TextInput::make('address')->label('Logradouro')->required(),
                    TextInput::make('district')->label('Bairro')->required(),
                    TextInput::make('address_number')->label('Número')->required(),
                    TextInput::make('address_complement')->label('Complemento'),
                    TextInput::make('city')->label('Cidade')->required(),
                ])
        ];
    }

    protected function getAcessoSection(): array
    {
        return [
            Grid::make([
                'default' => 1,
                'md' => 2,
            ])->schema([
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ])
        ];
    }


    protected function getCpfFormComponent(): Component
    {
        return TextInput::make('cpf')
            ->label('CPF')
            ->unique('candidates', 'cpf')
            ->required()
            ->rules(['cpf'])
            ->maxLength(11);
    }

    protected function handleRegistration(array $data): Model
    {
        try {
            return Candidate::create($data);
        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('Failed to create record', ['error' => $e->getMessage()]);

            // Rethrow the exception to notify Filament of the failure
            throw new Exception("Record creation failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        // Filament::auth()->login($user);
        Auth::guard('candidato')->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }
}
