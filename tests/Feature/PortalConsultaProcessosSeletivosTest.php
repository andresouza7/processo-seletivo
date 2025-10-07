<?php

namespace Tests\Feature;

use App\Filament\App\Resources\ProcessoSeletivos\Pages\ListProcessoSeletivos;
use App\Models\ProcessoSeletivo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use Filament\Facades\Filament;

class PortalConsultaProcessosSeletivosTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_consegue_ver_processos_seletivos(): void
    {
        ProcessoSeletivo::factory()->create([
            'publicado' => 'S',
            'titulo' => 'Processo Seletivo Teste',
            'numero' => '001/2025',
        ]);

        $response = $this
            ->get(route('filament.app.resources.processo-seletivos.index'));

        $response->assertStatus(200);
        $response->assertSeeText('Consultar Processos Seletivos');
    }

    public function test_candidato_filtra_processos_seletivos_por_status(): void
    {
        Filament::setCurrentPanel('app');
        // 🔹 Cria 5 processos seletivos com inscrições abertas / em andamento
        $abertos = \App\Models\ProcessoSeletivo::factory()
            ->count(5)
            ->create([
                'publicado' => 'S',
                'data_inscricao_inicio' => now()->subDays(2),
                'data_inscricao_fim' => now()->addDays(2),
                'titulo' => 'Inscrição Aberta - ' . fake()->word(),
            ]);

        // 🔹 Cria 5 processos seletivos finalizados
        $finalizados = \App\Models\ProcessoSeletivo::factory()
            ->count(5)
            ->create([
                'publicado' => 'S',
                'data_publicacao_inicio' => now()->subDays(10),
                'data_publicacao_fim' => now()->subDays(5),
                'titulo' => 'Finalizado - ' . fake()->word(),
            ]);

        Livewire::test(ListProcessoSeletivos::class, [
            'tableFilters' => ['status' => ['value' => 'inscricoes_abertas']]
        ])
            ->assertCanSeeTableRecords($abertos);

        Livewire::test(ListProcessoSeletivos::class, [
            'tableFilters' => ['status' => ['value' => 'em_andamento']]
        ])
            ->assertCanSeeTableRecords($abertos);

        Livewire::test(ListProcessoSeletivos::class, [
            'tableFilters' => ['status' => ['value' => 'finalizados']]
        ])
            ->assertCanSeeTableRecords($finalizados);
    }
}
