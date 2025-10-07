<?php

namespace Tests\Feature;

use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageAnexos;
use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageVagas;
use App\Models\InscricaoVaga;
use App\Models\ProcessoSeletivo;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class GestorConfiguraVagasTest extends TestCase
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

    public function test_gestor_acessa_pagina_vagas()
    {
        $response = $this->get(route('filament.gps.resources.processos.vagas', $this->processo->idprocesso_seletivo));
        $response->assertStatus(200);
    }

    public function test_gestor_cadastra_vaga()
    {
        Livewire::test(ManageVagas::class, [
            'record' => $this->processo->idprocesso_seletivo
        ])
            ->callAction(TestAction::make('create')->table(), [
                'codigo' => 'E01',
                'descricao' => 'Bolsista'
            ])
            ->assertHasNoFormErrors();
    }

    public function test_gestor_consulta_vagas()
    {
        $vagas = InscricaoVaga::factory(5)->create([
            'idprocesso_seletivo' => $this->processo->idprocesso_seletivo,
        ]);
        $vaga = InscricaoVaga::factory()->create([
            'idprocesso_seletivo' => $this->processo->idprocesso_seletivo,
            'descricao' => 'bolsista'
        ]);

        Livewire::test(ManageVagas::class, [
            'record' => $this->processo->idprocesso_seletivo
        ])
            ->assertCanSeeTableRecords($vagas)
            ->searchTable('bolsista')
            ->assertCanSeeTableRecords([$vaga]);
    }
}
