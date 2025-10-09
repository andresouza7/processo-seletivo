<?php

namespace Tests\Feature;

use App\Filament\Candidato\Pages\MeusDados;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\InscricaoPessoa;
use Livewire\Livewire;


class CandidatoNaoPodeEditarDadosPessoaisTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

      // CRIA O USUÁRIO
        $user = InscricaoPessoa::factory()->create([
            'name' => 'tester',
            'cpf' => '12345678901',
            'email' => 'tester@example.com',
            'rg' => 'MG123456',
            'password' => Hash::make('pwd'),
        ]);
        // LOGA PELO GUARD 'candidato'
        $this->actingAs($user, 'candidato');
        // ACESSA A PÁGINA
        $response = $this->get(route('filament.candidato.pages.meus-dados'));
        // VERIFICA SE A PÁGINA CARREGOU
        $response->assertStatus(200);
    }


    public function candidato_tem_que_preencher_campos_requeridos()
    {
        Livewire::test(MeusDados::class)
            ->set('data.nome', '')
            ->set('data.mae', '')
            ->set('data.cpf', '')
            ->set('data.ci', '')
            ->set('data.data_nascimento', '')
            ->set('data.email', '')
            ->set('data.sexo', '')
            ->set('data.identidade_genero', '')
            ->call('save') // chama o método de gravação
            ->assertHasErrors([
                'data.nome' => 'required',
                'data.mae' => 'required',
                'data.cpf' => 'required',
                'data.ci' => 'required',
                'data.data_nascimento' => 'required',
                'data.email' => 'required',
                'data.sexo' => 'required',
                'data.identidade_genero' => 'required',
            ]);
    }

    public function test_candidato_pode_editar_campos_genero_e_contato(): void
    {
        // VERIFICA SE OS CAMPOS ESTÃO HABILITADOS
        Livewire::test(MeusDados::class)
            ->assertFormFieldIsEnabled('gender_identity')
            ->assertFormFieldIsEnabled('social_name')
            ->assertFormFieldIsEnabled('postal_code')
            ->assertFormFieldIsEnabled('address')
            ->assertFormFieldIsEnabled('address_number')
            ->assertFormFieldIsEnabled('address_complement')
            ->assertFormFieldIsEnabled('district')
            ->assertFormFieldIsEnabled('city')
            ->assertFormFieldIsEnabled('district')
            ->assertFormFieldIsEnabled('phone');
    }

    public function test_candidato_nao_pode_editar_campos_de_identificacao(): void
    {
        // VERIFICA SE OS CAMPOS ABAIXO ESTÃO DESABILITADOS
        Livewire::test(MeusDados::class)
            ->assertFormFieldIsDisabled('name')
            ->assertFormFieldIsDisabled('mother_name')
            ->assertFormFieldIsDisabled('cpf')
            ->assertFormFieldIsDisabled('rg')
            ->assertFormFieldIsDisabled('birth_date')
            ->assertFormFieldIsDisabled('email');
    }

   
}
