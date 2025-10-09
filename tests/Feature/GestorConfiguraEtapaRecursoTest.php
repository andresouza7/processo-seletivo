<?php

namespace Tests\Feature;

use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageEtapaRecurso;
use App\Models\AppealStage;
use App\Models\Process;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class GestorConfiguraEtapaRecursoTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Process $processo;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('gps');
        $this->user = User::factory()->create();
        $this->user->assignRole('gestor');
        $this->actingAs($this->user);
        Livewire::actingAs($this->user);

        $this->processo = Process::factory()->create();
    }

    public function test_gestor_acessa_pagina_etapa_recurso()
    {
        $response = $this->get(route('filament.gps.resources.processos.etapas_recurso', $this->processo->id));
        $response->assertStatus(200);
    }

    public function test_gestor_cadastra_e_consulta_etapa_recurso()
    {
        $startDate = now()->toDateString();
        $endDate = now()->addDay()->toDateString();

        $data = [
            'description' => 'Resultado Preliminar',
            'submission_start_date' => $startDate,
            'submission_end_date' => $endDate,
            'result_start_date' => $startDate,
            'result_end_date' => $endDate,
        ];

        Livewire::test(ManageEtapaRecurso::class, [
            'record' => $this->processo->id
        ])
            ->callAction(TestAction::make('create')->table(), $data)
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('appeal_stage', $data);

        $etapa = AppealStage::latest()->first();

        Livewire::test(ManageEtapaRecurso::class, [
            'record' => $this->processo->id
        ])
            ->assertCanSeeTableRecords($this->processo->appeal_stage)
            ->searchTable('Resultado Preliminar')
            ->assertCanSeeTableRecords([$etapa]);
    }
}
