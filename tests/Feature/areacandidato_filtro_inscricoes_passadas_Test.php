<?php

namespace Tests\Feature;

use App\Filament\Candidato\Resources\InscricaoResource\Pages\ListInscricaos;
use App\Models\InscricaoPessoa;
use App\Models\ProcessoSeletivo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class areacandidato_filtro_inscricoes_passadas_Test extends TestCase
{
    use RefreshDatabase;
    
    public function test_candidato_filtra_inscricoes_realizadas(): void
    {
        $user = InscricaoPessoa::factory()->create();
        $this->actingAs($user, 'candidato');

        $response = $this->get(route('filament.candidato.resources.inscricoes.index'));
        $response->assertStatus(200);

        $processo = ProcessoSeletivo::factory()->withInscricoes()->create();

        $inscricao = $processo->inscricoes()->first();

        // Permite filtrar pelo código da inscrição
        Livewire::test(ListInscricaos::class)
            ->searchTable($inscricao->cod_inscricao)
            ->assertCanSeeTableRecords($processo->inscricoes->where('cod_inscricao', $inscricao->cod_inscricao));
    }
}
