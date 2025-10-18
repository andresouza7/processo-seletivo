<?php

namespace Tests\Feature;

use App\Filament\Candidato\Resources\Applications\Pages\ListApplications;
use App\Models\Application;
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

        $application = Application::factory()->create([
            'candidate_id' => $user->id
        ]);

        // dd($process->applications);
        Livewire::test(ListApplications::class)->assertCanSeeTableRecords([$application]);
    }
}
