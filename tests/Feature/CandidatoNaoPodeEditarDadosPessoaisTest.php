<?php

namespace Tests\Feature;

use App\Filament\Candidato\Pages\MeusDados;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Candidate;
use Livewire\Livewire;


class CandidatoNaoPodeEditarDadosPessoaisTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

      // CRIA O USUÁRIO
        $user = Candidate::factory()->create([
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
            ->set('data.name', '')
            ->set('data.mother_name', '')
            ->set('data.cpf', '')
            ->set('data.rg', '')
            ->set('data.birth_date', '')
            ->set('data.email', '')
            ->set('data.sex', '')
            ->set('data.gender_identity', '')
            ->call('save') // chama o método de gravação
            ->assertHasErrors([
                'data.name' => 'required',
                'data.mother_name' => 'required',
                'data.cpf' => 'required',
                'data.rg' => 'required',
                'data.birth_date' => 'required',
                'data.email' => 'required',
                'data.sex' => 'required',
                'data.gender_identity' => 'required',
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
