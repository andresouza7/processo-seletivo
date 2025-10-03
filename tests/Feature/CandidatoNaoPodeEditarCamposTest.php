<?php

namespace Tests\Feature;

use App\Filament\Candidato\Pages\MeusDados;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\InscricaoPessoa;
use Livewire\Livewire;

class CandidatoNaoPodeEditarCamposTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidato_nao_pode_editar_campos(): void
    {
        // CRIA O USUÁRIO
        $user = InscricaoPessoa::factory()->create([
            'nome' => 'tester',
            'cpf' => '12345678901',
            'email' => 'tester@example.com',
            'ci' => 'MG123456',
            'password' => Hash::make('pwd'),
        ]);

        // LOGA PELO GUARD 'candidato'
        $this->actingAs($user, 'candidato');

        // ACESSA A PÁGINA
        $response = $this->get(route('filament.candidato.pages.meus-dados'));

        // VERIFICA SE A PÁGINA CARREGOU
        $response->assertStatus(200);

        // VERIFICA SE OS CAMPOS ESTÃO DESABILITADOS
        Livewire::test(MeusDados::class)
            ->assertFormFieldIsDisabled('nome')
            ->assertFormFieldIsDisabled('mae')
            ->assertFormFieldIsDisabled('cpf')
            ->assertFormFieldIsDisabled('ci')
            ->assertFormFieldIsDisabled('data_nascimento')
            ->assertFormFieldIsDisabled('email');
    }
}
