<?php

namespace Tests\Feature;

use App\Models\InscricaoPessoa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Filament\Facades\Filament;
use Livewire\Livewire;
use App\Filament\Gps\Resources\InscricaoPessoas\Pages\ListInscricaoPessoas;



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

    public function test_gestor_filtra_candidatos_por_status()
    {
        Filament::setCurrentPanel('gps');

        // 🔹 Cria candidatos com status diferentes
        $masculino = \App\Models\InscricaoPessoa::factory()
            ->count(2)
            ->create(['sexo' => 'M']);
        $nome_jose = \App\Models\InscricaoPessoa::factory()
            ->count(2)
            ->create(['nome' => 'José da Silva']);
        $email_registrado = \App\Models\InscricaoPessoa::factory()
            ->count(1)
            ->create(['email' => 'qwerty@uol.com.br']);


        // 🔹 Testa filtro de masculino
        Livewire::test(ListInscricaoPessoas::class, [
            'tableFilters' => ['sexo' => ['value' => 'M']]
        ])
        ->assertCanSeeTableRecords($masculino);


        // 🔹 Testa filtro de José da Silva
        Livewire::test(ListInscricaoPessoas::class, [
            'tableFilters' => ['nome' => ['value' => 'José da Silva']]
        ])
        ->assertCanSeeTableRecords($nome_jose);
       

        // 🔹 Testa filtro de email
        Livewire::test(ListInscricaoPessoas::class, [
            'tableFilters' => ['email' => ['value' => 'qwerty@uol.com.br']]
        ])
        ->assertCanSeeTableRecords($email_registrado);
    }



}
