<?php

namespace Tests\Feature;

use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageAnexos;
use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageVagas;
use App\Models\Position;
use App\Models\Process;
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

    public function test_gestor_acessa_pagina_vagas()
    {
        $response = $this->get(route('filament.gps.resources.processos.vagas', $this->processo->id));
        $response->assertStatus(200);
    }

    public function test_gestor_cadastra_vaga()
    {
        Livewire::test(ManageVagas::class, [
            'record' => $this->processo->id
        ])
            ->callAction(TestAction::make('create')->table(), [
                'code' => 'E01',
                'description' => 'Bolsista'
            ])
            ->assertHasNoFormErrors();
    }

    public function test_gestor_consulta_vagas()
    {
        $vagas = Position::factory(5)->create([
            'id' => $this->processo->id,
        ]);
        $vaga = Position::factory()->create([
            'id' => $this->processo->id,
            'description' => 'bolsista'
        ]);

        Livewire::test(ManageVagas::class, [
            'record' => $this->processo->id
        ])
            ->assertCanSeeTableRecords($vagas)
            ->searchTable('bolsista')
            ->assertCanSeeTableRecords([$vaga]);
    }
}
