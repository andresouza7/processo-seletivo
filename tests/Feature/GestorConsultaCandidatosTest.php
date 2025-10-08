<?php

namespace Tests\Feature;

use App\Models\InscricaoPessoa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Filament\Facades\Filament;
use Livewire\Livewire;



class GestorConsultaCandidatosTest extends TestCase
{
    
    use RefreshDatabase;

    protected User $user;
    protected InscricaoPessoa $inscricao_pessoa;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('gps');
        $this->user = User::factory()->create();
        $this->user->assignRole('gestor');
        $this->actingAs($this->user);
        Livewire::actingAs($this->user);

        $this->inscricao_pessoa = InscricaoPessoa::factory()->create();
    }

    public function test_gestor_acessa_pagina_candidatos()
    {
        $response = $this->get(route('filament.gps.resources.candidatos.index', $this->inscricao_pessoa->id_inscricaopessoa));
        $response->assertStatus(200);
    }


}
