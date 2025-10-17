<?php

namespace App\Filament\Candidato\Pages\Auth;

use Filament\Auth\Pages\Register;
use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Text;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Events\Registered;
use App\Models\Candidate;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Canducci\Cep\Facades\Cep;
use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Icon;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

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
            Section::make()
                ->schema([
                    Text::make('ℹ Complete todas as seções para finalizar o seu cadastro.')
                        ->weight(FontWeight::SemiBold),
                ])
                ->compact()
                ->secondary(),

            ...self::getProfileSections(),

            Section::make('Acesso')
                ->schema(self::getAcessoSection()),
        ]);
    }

    public static function getProfileSections(?Candidate $record = null): array
    {
        return [
            Section::make('Identificação')
                ->description(fn() => $record ? 'Entre em contato com a Divisão de Processo Seletivo (DIPS) para alterar estes dados' : null)
                ->columns(2)
                ->schema(self::getIdentificacaoSection($record)),

            Section::make('Informações Sociais')
                ->columns(2)
                ->schema(self::getInformacoesSociaisSection($record))
                ->afterHeader([
                    Action::make('infoSocial')
                        ->label('Ajuda')
                        ->icon('heroicon-o-question-mark-circle')
                        ->modalHeading('Informações adicionais')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Fechar')
                        ->modalContent(view('filament.candidato.partials.informacoes-sociais-ajuda')),
                ]),

            Section::make('Endereço e Contato')
                ->schema(self::getContatoSection($record)),
        ];
    }

    public static function getIdentificacaoSection(?Candidate $record = null): array
    {
        return [
            TextInput::make('name')
                ->label('Nome')
                ->disabled(fn() => filled($record?->name))
                ->required(),

            TextInput::make('mother_name')
                ->label('Nome da Mãe')
                ->disabled(fn() => filled($record?->mother_name))
                ->required(),

            TextInput::make('cpf')
                ->label('CPF')
                ->unique('candidates', 'cpf', ignorable: $record)
                ->required()
                ->disabled(fn() => filled($record?->cpf))
                ->rules(['cpf'])
                ->maxLength(11),

            TextInput::make('rg')
                ->label('RG')
                ->maxLength(20)
                ->disabled(fn() => filled($record?->rg))
                ->required(),

            DatePicker::make('birth_date')
                ->label('Data de Nascimento')
                ->date()
                ->minDate('1950-01-01')
                ->maxDate(now())
                ->rules(['before_or_equal:today', 'after_or_equal:1950-01-01'])
                ->disabled(fn() => filled($record?->birth_date))
                ->required(),

            Select::make('sex')
                ->label('Sexo')
                ->options([
                    'M' => 'Masculino',
                    'F' => 'Feminino',

                ])
                ->reactive()
                ->disabled(fn() => filled($record?->sex))
                ->required(),
        ];
    }

    /////////// NOVO GRUPO DE INFORMAÇÕES
    public static function getInformacoesSociaisSection(?Candidate $record = null): array
    {
        return [
            // Identidade de gênero
            Select::make('gender_identity')
                ->label('Identidade de gênero')
                ->options([
                    'C'  => 'Cisgênero',
                    'T'  => 'Transgênero',
                    'NB' => 'Não-binário',
                    'TV' => 'Travesti',
                    'O'  => 'Outro',
                ])
                ->reactive()
                ->required()
                ->columnSpanFull(),

            TextInput::make('gender_identity_description')
                ->label('Especificar identidade de gênero (caso tenha selecionado "Outro")')
                ->columnSpanFull()
                ->visible(fn(Get $get) => $get('gender_identity') === 'O'),

            Checkbox::make('has_social_name')
                ->label('Usar nome social')
                ->reactive()
                ->columnSpanFull(),
                // ->visible(fn(Get $get) => in_array($get('gender_identity'), ['T', 'TV', 'NB', 'O'])),

            TextInput::make('social_name')
                ->label('Nome social')
                ->columnSpanFull()
                ->visible(fn(Get $get) => $get('has_social_name')),

            // Orientação sexual
            Select::make('sexual_orientation')
                ->label('Orientação sexual')
                ->options([
                    'HT' => 'Heterossexual',
                    'HM' => 'Homossexual',
                    'B'  => 'Bissexual',
                    'P'  => 'Pansexual',
                    'A'  => 'Assexual',
                ])
                ->default('HT')
                ->required()
                ->reactive()
                ->columnSpanFull(),

            // Pessoa com deficiência
            Checkbox::make('has_disability')
                ->label('Possui deficiência, transtorno do espectro autista, altas habilidades ou superdotação')
                ->reactive()
                ->columnSpanFull(),

            TextInput::make('disability_description')
                ->label('Especificar deficiência, transtorno ou condição')
                ->columnSpanFull()
                ->visible(fn(Get $get) => $get('has_disability')),

            // Raça/Cor
            Select::make('race')
                ->label('Raça/Cor (conforme classificação IBGE)')
                ->options([
                    'PT' => 'Preta',
                    'PD' => 'Parda',
                    'B'  => 'Branca',
                    'I'  => 'Indígena',
                    'A'  => 'Amarela',
                ])
                ->required()
                ->reactive(),

            // Estado civil
            Select::make('marital_status')
                ->label('Estado civil')
                ->options([
                    'C'  => 'Casado(a)',
                    'S'  => 'Solteiro(a)',
                    'D'  => 'Divorciado(a)',
                    'V'  => 'Viúvo(a)',
                    'U'  => 'União estável',
                    'SP' => 'Separado(a)',
                ])
                ->required()
                ->reactive(),

            // Comunidade
            Select::make('community')
                ->label('Pertence a alguma comunidade tradicional?')
                ->options([
                    'R' => 'Comunidade ribeirinha',
                    'Q' => 'Comunidade quilombola',
                    'I' => 'Comunidade indígena',
                    'T' => 'Comunidade tradicional (extrativista)',
                    'O' => 'Não se aplica',
                ])
                ->required()
                ->reactive(),
        ];
    }

    public static function getContatoSection(?Candidate $record = null): array
    {
        return [
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required(),

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
                                    $set('district', $cep->bairro);
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
