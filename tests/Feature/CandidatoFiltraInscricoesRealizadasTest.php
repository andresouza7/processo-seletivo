<?php

namespace Tests\Feature;

use App\Filament\Candidato\Resources\Inscricaos\Pages\ListInscricaos;
use App\Models\Candidate;
use App\Models\Process;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class CandidatoFiltraInscricoesRealizadasTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_candidato_filtra_inscricoes_realizadas(): void
    {
        $user = Candidate::factory()->create();
        $this->actingAs($user, 'candidato');

        $response = $this->get(route('filament.candidato.resources.inscricoes.index'));
        $response->assertStatus(200);

        $processo = Process::factory()->withApplications()->create();

        $application = $processo->applications()->first();

        // Permite filtrar pelo código da inscrição
        Livewire::test(ListInscricaos::class)
            ->searchTable($application->code)
            ->assertCanSeeTableRecords($processo->applications->where('code', $application->code));
    }
}
