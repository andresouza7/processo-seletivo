<?php

namespace Tests\Feature;

use App\Models\Candidate;
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
    protected Candidate $candidate;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('gps');
        $this->user = User::factory()->create();
        $this->user->assignRole('gestor');
        $this->actingAs($this->user);
        Livewire::actingAs($this->user);

        $this->candidate = Candidate::factory()->create([
            'name' => 'Nome candidata teste',
            'mother_name' => 'Mãe da candidata teste',
            'cpf' => '23456781099',
            'rg' => '9874221-PA',
            'birth_date' => '1974-02-01',
            'sex' => 'M',
            'social_name' => 'nome social candidata',
            'gender_identity' => 'T',
            'phone' => '(41) 98533-9922',
            'email' => 'candidata_teste@gmail.com.br'
        ]);
    }

    public function test_gestor_acessa_pagina_candidatos()
    {
        $response = $this->get(route('filament.gps.resources.candidatos.index', $this->candidate->id));
        $response->assertStatus(200);
    }

    public function test_gestor_acessa_pagina_detalhes_dos_candidatos()
    {
        $response = $this->get(route('filament.gps.resources.candidatos.view', [
            'record' => $this->candidate->id,
        ]));

        $response->assertStatus(200);
        // 🔹 Verifica se todos os campos do candidato aparecem na página
        $response->assertSeeText('Nome candidata teste');
        $response->assertSeeText('Mãe da candidata teste');
        $response->assertSeeText('23456781099');
        $response->assertSeeText('9874221-PA');
        $response->assertSeeText('1974-02-01');
        $response->assertSeeText('M');
        $response->assertSeeText('nome social candidata');
        $response->assertSeeText('T');
        $response->assertSeeText('(41) 98533-9922');
        $response->assertSeeText('candidata_teste@gmail.com.br');
    }

    public function test_gestor_filtra_candidatos_por_status()
    {
        Filament::setCurrentPanel('gps');

        // 🔹 Cria candidatos com status diferentes
        $masculino = \App\Models\Candidate::factory()
            ->count(2)
            ->create(['sex' => 'M']);
        $nome_jose = \App\Models\Candidate::factory()
            ->count(2)
            ->create(['name' => 'José da Silva']);
        $email_registrado = \App\Models\Candidate::factory()
            ->count(1)
            ->create(['email' => 'qwerty@uol.com.br']);

        // 🔹 Testa filtro de masculino
        Livewire::test(ListInscricaoPessoas::class, [
            'tableFilters' => ['sex' => ['value' => 'M']]
        ])
        ->assertCanSeeTableRecords($masculino);

        // 🔹 Testa filtro de José da Silva
        Livewire::test(ListInscricaoPessoas::class, [
            'tableFilters' => ['name' => ['value' => 'José da Silva']]
        ])
        ->assertCanSeeTableRecords($nome_jose);
       
        // 🔹 Testa filtro de email
        Livewire::test(ListInscricaoPessoas::class, [
            'tableFilters' => ['email' => ['value' => 'qwerty@uol.com.br']]
        ])
        ->assertCanSeeTableRecords($email_registrado);
    }



}
