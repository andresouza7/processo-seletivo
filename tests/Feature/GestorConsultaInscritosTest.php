<?php

namespace Tests\Feature;

use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageInscritos;
use App\Models\Application;
use App\Models\Process;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Support\Str;

class GestorConsultaInscritosTest extends TestCase
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

        $this->processo = Process::factory()->withApplications()->create();
    }

    public function test_gestor_acessa_pagina_inscritos()
    {
        $response = $this->get(route('filament.gps.resources.processos.inscritos', $this->processo->id));
        $response->assertStatus(200);
    }

    public function test_gestor_consulta_inscricoes()
    {
        $application = Application::factory()->create([
            'id' => $this->processo->id,
            'code' => '123456'
        ]);

        Livewire::test(ManageInscritos::class, [
            'record' => $this->processo->id
        ])
            ->assertCanSeeTableRecords($this->processo->applications)
            ->searchTable('123456')
            ->assertCanSeeTableRecords([$application]);
    }

    // gestor visualiza inscricao
    public function test_gestor_visualiza_inscricao()
    
    {}

    // consegue abrir o link doas anexos da inscricao

    // consegue exportar inscricoes para planilha em excel
}
