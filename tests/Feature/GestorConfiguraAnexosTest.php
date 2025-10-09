<?php

namespace Tests\Feature;

use App\Filament\Gps\Resources\ProcessoSeletivos\Pages\ManageAnexos;
use App\Models\InscricaoPessoa;
use App\Models\ProcessoSeletivo;
use App\Models\ProcessoSeletivoAnexo;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GestorConfiguraAnexosTest extends TestCase
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

    public function test_gestor_acessa_pagina_anexos()
    {
        $response = $this->get(route('filament.gps.resources.processos.anexos', $this->processo->idprocesso_seletivo));
        $response->assertStatus(200);
    }

    public function test_gestor_publica_anexo()
    {
        Storage::fake('media');

        $file = UploadedFile::fake()->createWithContent('documento.pdf', '%PDF-1.4 fake content here')->size(1024);

        // Valida campos obrigatórios
        Livewire::test(ManageAnexos::class, [
            'record' => $this->processo->idprocesso_seletivo
        ])
            ->assertActionExists(TestAction::make('create')->table())
            ->callAction(TestAction::make('create')->table(), [
                'description' => 'um anexo do concurso',
                'arquivo' => $file
            ])
            ->assertHasNoFormErrors();

        // Valida que o arquivo foi adicionado na tabela do media library
        $anexo = ProcessoSeletivoAnexo::latest()->first();
        $mediaItem = $anexo->getFirstMedia();
        $this->assertNotNull($mediaItem);

        // Valida que o arquivo foi salvo no diretorio certo no filesystem
        $customPath = (new \App\Services\MediaLibrary\CustomPathGenerator())->getPath($mediaItem) . $mediaItem->file_name;
        Storage::disk($mediaItem->disk)->assertExists($customPath);
    }

    public function test_gestor_consulta_anexos()
    {
        $anexos = ProcessoSeletivoAnexo::factory(5)->create([
            'idprocesso_seletivo' => $this->processo->idprocesso_seletivo,
        ]);
        $anexo = ProcessoSeletivoAnexo::factory()->create([
            'idprocesso_seletivo' => $this->processo->idprocesso_seletivo,
            'description' => 'um anexo'
        ]);

        Livewire::test(ManageAnexos::class, [
            'record' => $this->processo->idprocesso_seletivo
        ])
            ->assertCanSeeTableRecords($anexos)
            ->searchTable('um anexo')
            ->assertCanSeeTableRecords([$anexo]);
    }
}
