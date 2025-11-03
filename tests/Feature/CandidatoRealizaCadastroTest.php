<?php

namespace Tests\Feature\Livewire;

use App\Filament\Candidato\Pages\Auth\Cadastro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tests\TestCase;
use App\Models\Candidate;
use Livewire\Livewire;

use function PHPUnit\Framework\assertTrue;

class CandidatoRealizaCadastroTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CRIA O USUÁRIO CORRETAMENTE
        $user = Candidate::factory()->create([
            'name' => 'tester',
            'mother_name' => 'mae do tester',
            'rg' => '12344321-AP',
            'birth_date' => '1990-12-25',
            'cpf' => '12345678901',
            'password' => Hash::make('pwd'),
        ]);

        // LOGA PELO GUARD CORRETO
        $this->actingAs($user, 'web'); //OBS.: neste testCase nao é necessário login de nenhum tipo.

        // Usa a rota FILAMENT diretamente via URL ou route
        $response = $this->get(route('filament.candidato.auth.register')); 
    }

    public function test_pagina_de_inscricao_esta_disponivel_em_portugues()
    {
        // Verifica se o locale está definido corretamente
        $currentLocale = app()->getLocale();
        $expectedLocale = 'pt_BR';

        $this->assertEquals(
            $expectedLocale,
            $currentLocale,
            "O locale atual é '{$currentLocale}', mas o teste requer '{$expectedLocale}'. 
            Verifique se o arquivo .env.testing contém APP_LOCALE=pt_BR"
        );
    }

    public function test_candidato_entra_na_tela_de_cadastro(): void
    {
          
        // Usa a rota FILAMENT diretamente via URL ou route
        $response = $this->get(route('filament.candidato.auth.register'));
        // ASSERT DE SUCESSO
        $response->assertStatus(200);
        $response->assertSee('Inscrever-se', 'A página deve conter o texto "Inscrever-se" em português.');

    }

    public function test_candidato_tem_acesso_aos_campos(): void
    {
        Livewire::test(Cadastro::class)
            //identificação
            ->assertFormFieldIsEnabled('name')
            ->assertFormFieldIsEnabled('mother_name')
            ->assertFormFieldIsEnabled('cpf')
            ->assertFormFieldIsEnabled('rg')
            ->assertFormFieldIsEnabled('birth_date')
            ->assertFormFieldIsEnabled('email')
            ->assertFormFieldIsEnabled('sex')

            // informações sociais
            ->assertFormFieldIsEnabled('gender_identity')
            ->assertFormFieldIsEnabled('sexual_orientation')
            ->assertFormFieldIsEnabled('social_name')

            // endereço
            ->assertFormFieldIsEnabled('postal_code')
            ->assertFormFieldIsEnabled('address')
            ->assertFormFieldIsEnabled('address_number')
            ->assertFormFieldIsEnabled('address_complement')
            ->assertFormFieldIsEnabled('district')
            ->assertFormFieldIsEnabled('city')
            ->assertFormFieldIsEnabled('district')
            ->assertFormFieldIsEnabled('phone');
    }

    public function test_candidato_pode_usar_nome_social(): void
    {
        Livewire::test(Cadastro::class)
        // Primeiro define o campo de gênero como transgênero (ativa o checkbox)
        ->set('data.gender_identity', 'T')

        // Marca o checkbox “usar nome social”
        ->set('data.has_social_name', true)

        // confirma que o campo está visível
        ->assertFormFieldIsVisible('social_name')

        // Verifica se o estado do checkbox realmente está ativo
        ->assertSet('data.has_social_name', true)

        // Define o nome social
        ->set('data.social_name', 'Maria Clara')

        // Verifica se o nome social foi corretamente setado
        ->assertSet('data.social_name', 'Maria Clara');
    }

    public function test_validacoes_do_cadastro_funcionam_corretamente(): void
    {
        //PRIMEIRO TESTE: campos vazios
        Livewire::test(\App\Filament\Candidato\Pages\Auth\Cadastro::class)
            // Tenta registrar sem preencher nada
            ->call('register')
            // Deve falhar nas validações obrigatórias
            ->assertHasFormErrors([
                'name' => 'required',
                'mother_name' => 'required',
                'cpf' => 'required',
                'rg' => 'required',
                'birth_date' => 'required',
                'sex' => 'required',
                // 'email' => 'required',
                'phone' => 'required',
                'postal_code' => 'required',
                'address' => 'required',
                'district' => 'required',
                'address_number' => 'required',
                'city' => 'required',
                'password' => 'required',
                'passwordConfirmation' => 'required',
            ]);

        // TESTE: testa formato incorreto de CPF e data inválida
      //  Livewire::test(\App\Filament\Candidato\Pages\Auth\Cadastro::class)
        //    ->set('data.name', 'João Teste')
        //    ->set('data.mother_name', 'Maria Teste')
        //    ->set('data.rg', '12345')
       //     ->set('data.birth_date', '3020-01-01') // data futura (inválida)
       //     ->set('data.cpf', '12345678999') // formato incorreto (não passa regra 'cpf')
       //     ->set('data.email', 'email_invalido')
        //    ->set('data.phone', '(00)12345-1234')
         //   ->set('data.postal_code', '00000-000')
         //   ->set('data.address', 'Rua Teste')
          //  ->set('data.distric', 'Centro')
          //  ->set('data.address_number', '100')
         //   ->set('data.city', 'Macapá')
          //  ->set('data.password', 'senha')
           // ->set('data.passwordConfirmation', 'senha')
          //  ->set('data.sex', 'M')
         //   ->call('register')
          //  ->assertHasFormErrors([
          //      'cpf',              // formato errado (regra 'cpf')
          //      'birth_date',  // fora do intervalo (regra minDate)
           //     'email',            // formato inválido
           // ]);
    }
}
