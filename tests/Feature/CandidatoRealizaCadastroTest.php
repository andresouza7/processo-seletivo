<?php

namespace Tests\Feature\Livewire;

use App\Filament\Candidato\Pages\Auth\Cadastro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tests\TestCase;
use App\Models\InscricaoPessoa;
use Livewire\Livewire;

use function PHPUnit\Framework\assertTrue;

class CandidatoRealizaCadastroTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CRIA O USUÁRIO CORRETAMENTE
        $user = InscricaoPessoa::factory()->create([
            'nome' => 'tester',
            'mae' => 'mae do tester',
            'ci' => '12344321-AP',
            'data_nascimento' => '1990-12-25',
            'cpf' => '12345678901',
            'password' => Hash::make('pwd'),
        ]);

        // LOGA PELO GUARD CORRETO
        $this->actingAs($user, 'web'); //OBS.: neste testCase nao é necessário login de nenhum tipo.

        // Usa a rota FILAMENT diretamente via URL ou route
        $response = $this->get(route('filament.candidato.auth.register'));

        // ASSERT DE SUCESSO
        $response->assertStatus(200);
        $response->assertSee('Inscrever-se'); 

    }

    public function test_candidato_entra_na_tela_de_cadastro(): void
    {
          
        // Usa a rota FILAMENT diretamente via URL ou route
        $response = $this->get(route('filament.candidato.auth.register'));
        // ASSERT DE SUCESSO
        $response->assertStatus(200);
        $response->assertSee('Inscrever-se'); 

    }

    public function test_candidato_tem_acesso_aos_campos(): void
    {
        Livewire::test(Cadastro::class)
            //identificação
            ->assertFormFieldIsEnabled('nome')
            ->assertFormFieldIsEnabled('mae')
            ->assertFormFieldIsEnabled('cpf')
            ->assertFormFieldIsEnabled('ci')
            ->assertFormFieldIsEnabled('data_nascimento')
            ->assertFormFieldIsEnabled('email')
            ->assertFormFieldIsEnabled('sexo')

            // informações sociais
            ->assertFormFieldIsEnabled('identidade_genero')
            ->assertFormFieldIsEnabled('orientacao_sexual')
            ->assertFormFieldIsEnabled('nome_social')

            // endereço
            ->assertFormFieldIsEnabled('cep')
            ->assertFormFieldIsEnabled('endereco')
            ->assertFormFieldIsEnabled('numero')
            ->assertFormFieldIsEnabled('complemento')
            ->assertFormFieldIsEnabled('bairro')
            ->assertFormFieldIsEnabled('cidade')
            ->assertFormFieldIsEnabled('bairro')
            ->assertFormFieldIsEnabled('telefone');
    }

    public function test_candidato_pode_usar_nome_social(): void
    {
        Livewire::test(Cadastro::class)
        // Primeiro define o campo de gênero como transgênero (ativa o checkbox)
        ->set('data.identidade_genero', 'T')

        // Marca o checkbox “usar nome social”
        ->set('data.usar_nome_social', true)

        // confirma que o campo está visível
        ->assertFormFieldIsVisible('nome_social')

        // Verifica se o estado do checkbox realmente está ativo
        ->assertSet('data.usar_nome_social', true)

        // Define o nome social
        ->set('data.nome_social', 'Maria Clara')

        // Verifica se o nome social foi corretamente setado
        ->assertSet('data.nome_social', 'Maria Clara');
    }

    public function test_validacoes_do_cadastro_funcionam_corretamente(): void
    {
        //PRIMEIRO TESTE: campos vazios
        Livewire::test(\App\Filament\Candidato\Pages\Auth\Cadastro::class)
            // Tenta registrar sem preencher nada
            ->call('register')
            // Deve falhar nas validações obrigatórias
            ->assertHasFormErrors([
                'nome' => 'required',
                'mae' => 'required',
                'cpf' => 'required',
                'ci' => 'required',
                'data_nascimento' => 'required',
                'sexo' => 'required',
                'email' => 'required',
                'telefone' => 'required',
                'cep' => 'required',
                'endereco' => 'required',
                'bairro' => 'required',
                'numero' => 'required',
                'cidade' => 'required',
                'password' => 'required',
                'passwordConfirmation' => 'required',
            ]);

        // SEGUNDO TESTE: testa formato incorreto de CPF e data inválida
        Livewire::test(\App\Filament\Candidato\Pages\Auth\Cadastro::class)
            ->set('data.nome', 'João Teste')
            ->set('data.mae', 'Maria Teste')
            ->set('data.ci', '12345')
            ->set('data.data_nascimento', '3020-01-01') // data futura (inválida)
            ->set('data.cpf', '12345678999') // formato incorreto (não passa regra 'cpf')
            ->set('data.email', 'email_invalido')
            ->set('data.telefone', '(00)12345-1234')
            ->set('data.cep', '00000-000')
            ->set('data.endereco', 'Rua Teste')
            ->set('data.bairro', 'Centro')
            ->set('data.numero', '100')
            ->set('data.cidade', 'Macapá')
            ->set('data.password', 'senha')
            ->set('data.passwordConfirmation', 'senha')
            ->set('data.sexo', 'M')
            ->call('register')
            ->assertHasFormErrors([
                'cpf',              // formato errado (regra 'cpf')
                'data_nascimento',  // fora do intervalo (regra minDate)
                'email',            // formato inválido
            ]);
    }



}
