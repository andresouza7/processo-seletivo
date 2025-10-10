<?php

namespace Tests\Feature;

use App\Filament\Candidato\Resources\Inscricaos\Pages\ListInscricaos;
use App\Models\Candidate;
use App\Models\Process;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class CandidatoConsultaInscricoesRealizadasTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_candidato_consulta_inscricoes_realizadas(): void
    {
        $user = Candidate::factory()->create();
        $this->actingAs($user, 'candidato');

        $response = $this->get(route('filament.candidato.resources.inscricoes.index'));
        $response->assertStatus(200);

        $process = Process::factory()->withApplications()->create();
        Livewire::test(ListInscricaos::class)->assertCanSeeTableRecords($process->applications);
    }
}
