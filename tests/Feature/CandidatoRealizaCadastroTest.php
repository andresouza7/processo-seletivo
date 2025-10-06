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

    public function test_candidato_entra_na_tela_de_cadastro(): void
    {
        // CRIA O USUÁRIO CORRETAMENTE
        $user = InscricaoPessoa::factory()->create([
            'nome' => 'tester',
            'mae' => 'mae do tester',
            'ci' => '12344321-AP',
            'data_nascimento' => '1990-12-25',
            'cpf' => '12345678901',
            'password' => Hash::make('pwd'),
        ]);

        //OBS.: neste testCase nao é necessário login de nenhum tipo.

        // LOGA PELO GUARD CORRETO
        $this->actingAs($user, 'web');

        // Usa a rota FILAMENT diretamente via URL ou route
        $response = $this->get(route('filament.candidato.auth.register'));

        // ASSERT DE SUCESSO
        $response->assertStatus(200);
        $response->assertSee('Inscrever-se');
        
    }

    //////////////////
    ////////////////// outro teste

    public function test_candidato_tem_acesso_aos_campos(): void
    {
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
        $this->actingAs($user, 'web');

        // Usa a rota FILAMENT diretamente via URL ou route
        $response = $this->get(route('filament.candidato.auth.register'));

        // ASSERT DE SUCESSO
        $response->assertStatus(200);
        $response->assertSee('Inscrever-se');

        Livewire::test(Cadastro::class)
            //identificação
            ->assertFormFieldIsEnabled('nome')
            ->assertFormFieldIsEnabled('mae')
            ->assertFormFieldIsEnabled('cpf')
            ->assertFormFieldIsEnabled('ci')
            ->assertFormFieldIsEnabled('data_nascimento')
            ->assertFormFieldIsEnabled('email')
            ->assertFormFieldIsEnabled('sexo')

            //informações sociais
            ->assertFormFieldIsEnabled('identidade_genero')
            ->assertFormFieldIsEnabled('orientacao_sexual')
            ->assertFormFieldIsEnabled('nome_social')

            //
            ->assertFormFieldIsEnabled('cep')
            ->assertFormFieldIsEnabled('endereco')
            ->assertFormFieldIsEnabled('numero')
            ->assertFormFieldIsEnabled('complemento')
            ->assertFormFieldIsEnabled('bairro')
            ->assertFormFieldIsEnabled('cidade')
            ->assertFormFieldIsEnabled('bairro')
            ->assertFormFieldIsEnabled('telefone');
    }
}
