<?php

namespace App\Filament\Candidato\Pages\Auth;

use App\Models\InscricaoPessoa;
use App\Models\Pessoa;
use Filament\Forms;
use Filament\Pages\Page;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Exception;
use Filament\Events\Auth\Registered;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Pages\Auth\Register;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Canducci\Cep\Facades\Cep;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;

class Cadastro extends Register
{
    protected ?string $maxWidth = '4xl';

    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction(),
            \Filament\Actions\Action::make('voltar')
                ->url('/')
                ->label('Voltar para o site')
                ->color('gray')
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Placeholder::make('')
                ->content(new HtmlString('<p>ℹ Complete todas as etapas para finalizar o seu cadastro.</p>')),

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
            TextInput::make('nome')->required(),
            TextInput::make('mae')->label('Nome da Mãe')->required(),
            $this->getCpfFormComponent(),
            TextInput::make('ci')
                ->label('RG')
                ->required(),
            DatePicker::make('data_nascimento')
                ->label('Data de Nascimento')
                ->date()
                ->minDate('1950-01-01')
                ->required(),

            Select::make('sexo')
                ->label('Sexo')
                ->options([
                    'M' => 'Masculino',
                    'F' => 'Feminino',
                    
                ])
                ->reactive()
                ->required(),
        ];
    }


    //////////////////
    ////////////////// sociais
    //////////////////
    //////////////////


    /////////// NOVO GRUPO DE INFORMAÇÕES
    protected function getInformacoesSociaisSection(): array
    {
        return [

           

            /////// SELECT: DADOS DE GÊNERO
            Select::make('identidade_genero')
                ->label('Identidade de gênero')
                ->options(fn (Get $get) => match ($get('sexo')) {
                    'M' => [
                        'C' => 'Cisgênero',
                        'T' => 'Transgênero',
                        'NB' => 'Não-binário',
                        'TV' => 'Travesti',
                        'NB' => 'Não-binário',
                        'O'  => 'Outro',
                    ],
                    'F' => [
                        'C' => 'Cisgênero',
                        'T' => 'Transgênero',
                        'NB' => 'Não-binário',
                        'TV' => 'Travesti',
                        'NB' => 'Não-binário',
                        'O'  => 'Outro',
                    ],
                    
                    default => []
                })
                
                ->reactive()
                ->columnSpanFull()
                ->required(),

            //////////////// AVISO DINÂMICO GENERO
            Placeholder::make('')
            ->content(fn (Get $get) => match ($get('identidade_genero')) {
                'C' => new HtmlString(
                    '<span style="color:grey;"><em>* pessoa que se identifica com o gênero que lhe foi atribuído ao nascer</em></span>'
                ),
                'T' => new HtmlString(
                    '<span style="color:grey;"><em>* pessoa que se identifica com um gênero diferente daquele que lhe foi atribuído ao nascer</em></span>'
                ),
                'NB' => new HtmlString(
                    '<span style="color:grey;"><em>* pessoa que não se identifica nem como homem e nem como mulher</em></span>'
                ),
                default => null,
            })
            ->visible(fn (Get $get) => in_array($get('identidade_genero'), ['C','T','NB']))
            ->columnSpanFull(),
                        
            //////////// INPUT ESPECIFICANDO GENERO: OUTROS    
            TextInput::make('identidade_genero_descricao')
                ->label('Minha identidade de genero é')                
                ->columnSpanFull()
                ->visible(fn(Get $get) => in_array($get('identidade_genero'), ['O'])),

            ///////// CHECKBOX PARA USO DE NOME SOCIAL (default: não usar)
            Checkbox::make('usar_nome_social')
                ->label('Usar nome social')
                ->reactive()
                ->columnSpanFull()
                ->visible(fn(Get $get) => in_array($get('identidade_genero'), ['T', 'TV', 'NB', 'O'])),

            ///////// TEXTBOX DO NOME SOCIAL
            TextInput::make('nome_social')
                ->columnSpanFull()
                ->visible(fn(Get $get) => $get('usar_nome_social')),

                
            /////// SELECT ORIENTACAO SEXUAL  
            Select::make('orientacao_sexual')
                ->label('Orientação sexual:')
                ->options([
                    'A' => 'Heterossexual',
                    'B' => 'Homossexual',
                    'C' => 'Bissexual',
                    'D' => 'Panssexual',
                    'E' => 'Assexual',
                ])
                ->reactive()
                ->columnSpanFull()
                ->required(),
            
            //////// AVISO DINAMICO ORIENTACAO
            Placeholder::make('')
            ->content(fn (Get $get) => match ($get('orientacao_sexual')) {
                'A' => new HtmlString(
                    '<span style="color:grey;"><em>* pessoa que se atrai ao gênero oposto</em></span>'
                ),
                'B' => new HtmlString(
                    '<span style="color:grey;"><em>* pessoa que se atrai ao mesmo gênero</em></span>'
                ),
                'C' => new HtmlString(
                    '<span style="color:grey;"><em>* pessoa que se atrai a ambos gêneros</em></span>'
                ),
                'D' => new HtmlString(
                    '<span style="color:grey;"><em>* pessoa que se atrai a todos os gêneros</em></span>'
                ),
                'E' => new HtmlString(
                    '<span style="color:grey;"><em>* pessoa que se não se atrai a nenhum gênero</em></span>'
                ),
                default => null,
            })
            ->visible(fn (Get $get) => in_array($get('orientacao_sexual'), ['A','B','C','D','E']))
            ->columnSpanFull(),
                
            ///////// CHECKBOX PARA DEFICIENCIA (default: não usar)
            Checkbox::make('deficiencia')
                ->label('Possui deficiência, transtorno global do desenvolvimento, altas habilidades ou superdotação?')
                ->reactive()
                ->columnSpanFull(),


            /////////////// SELECT: RAÇA
            Select::make('raca')
                ->label('Raça/Cor')
                ->options([
                    'A' => 'Negro de cor preta',
                    'B' => 'Negro de cor parda',
                    'C' => 'Branca',
                    'D' => 'Indígena',
                    'E' => 'Amarela',
                    
                ])
                ->reactive()
                ->required(),           
                

            ///////// TEXTBOX DA DEFICIENCIA
            TextInput::make('deficiencia_descricao')
                ->label('Caso possuia deficiência, favor especificar qual:')
                ->columnSpanFull()
                ->visible(fn(Get $get) => $get('deficiencia')),

            /////////////// SELECT: ESTADO CIVIL
                Select::make('estado_civil')
                ->label('Estado civil:')
                ->options([
                    'A' => 'Casado (a)',
                    'B' => 'Solteiro (a)',
                    'C' => 'Divorciado (a)',
                    'D' => 'Viúvo(a)',
                    'E' => 'União estável',
                    'F' => 'Separado(a)',
                    
                ])
                ->reactive()
                ->required(),

            /////////////// SELECT: COMUNIDADE
                Select::make('comunidade')
                ->label('Você é pertencente à comunidade:')
                ->options([
                    'A' => 'Comunidade Ribeirinha',
                    'B' => 'Comunidade Quilombola',
                    'C' => 'Comunidade Indígena',
                    'D' => 'Comunidade Tradicional (extrativistas)',
                    'E' => 'Não se aplica',
                    
                ])
                ->reactive()
                ->required(),

        ];
    }


    //////////////////
    ////////////////// dados de contato
    //////////////////
    //////////////////

    protected function getContatoSection(): array
    {
        return [
            $this->getEmailFormComponent(),
            TextInput::make('telefone')
                ->label('Telefone')
                ->mask('(99)99999-9999')
                ->required(),
            Grid::make([
                'default' => 1,
                'md' => 2,
            ])
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
                            } catch (\Exception $e) {
                                // Handle error silently
                            }
                        }),
                    TextInput::make('endereco')->label('Logradouro')->required(),
                    TextInput::make('bairro')->label('Bairro')->required(),
                    TextInput::make('numero')->label('Número')->required(),
                    TextInput::make('complemento')->label('Complemento'),
                    TextInput::make('cidade')->label('Cidade')->required(),
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
            ->unique('inscricao_pessoa', 'cpf')
            ->required()
            ->rules(['cpf'])
            ->maxLength(11);
    }

    protected function handleRegistration(array $data): Model
    {
        try {
            return DB::transaction(function () use ($data) {
                // $data['senha'] = $data['password']; 
                // unset($data['password']); 

                // $data['sexo'] = 'M';
                // $data['ci'] = '165465321';
                // $data['matricula'] = '1324654';
                // $data['endereco'] = 'Av professor tostes';
                // $data['bairro'] = 'buritizal';
                // $data['numero'] = '123';
                // $data['complemento'] = '.';
                // $data['cidade'] = 'Macapá';
                // $data['telefone'] = '99999-9999';
                $lastId = InscricaoPessoa::max('idpessoa') ?? 0; // If no rows, start at 0
                $data['idpessoa'] = $lastId + 1;
                $data['perfil'] = '';
                $data['situacao'] = 'S';
                $data['link_lattes'] = '';
                $data['resumo'] = '';
                $data['senha'] = '';

                return InscricaoPessoa::create($data);
            });
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
