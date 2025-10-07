<?php

namespace Tests\Feature;

use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageEtapaRecurso;
use App\Models\EtapaRecurso;
use App\Models\ProcessoSeletivo;
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
    protected ProcessoSeletivo $processo;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('gps');
        $this->user = User::factory()->create();
        $this->user->assignRole('gestor');
        $this->actingAs($this->user);
        Livewire::actingAs($this->user);

        $this->processo = ProcessoSeletivo::factory()->create();
    }

    public function test_gestor_acessa_pagina_etapa_recurso()
    {
        $response = $this->get(route('filament.gps.resources.processos.etapas_recurso', $this->processo->idprocesso_seletivo));
        $response->assertStatus(200);
    }

    public function test_gestor_cadastra_e_consulta_etapa_recurso()
    {
        $startDate = now()->toDateString();
        $endDate = now()->addDay()->toDateString();

        $data = [
            'descricao' => 'Resultado Preliminar',
            'data_inicio_recebimento' => $startDate,
            'data_fim_recebimento' => $endDate,
            'data_inicio_resultado' => $startDate,
            'data_fim_resultado' => $endDate,
        ];

        Livewire::test(ManageEtapaRecurso::class, [
            'record' => $this->processo->idprocesso_seletivo
        ])
            ->callAction(TestAction::make('create')->table(), $data)
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('etapa_recurso', $data);

        $etapa = EtapaRecurso::latest()->first();

        Livewire::test(ManageEtapaRecurso::class, [
            'record' => $this->processo->idprocesso_seletivo
        ])
            ->assertCanSeeTableRecords($this->processo->etapa_recurso)
            ->searchTable('Resultado Preliminar')
            ->assertCanSeeTableRecords([$etapa]);
    }
}
