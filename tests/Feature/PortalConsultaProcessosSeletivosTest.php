<?php

namespace Tests\Feature;

use App\Filament\App\Resources\ProcessoSeletivos\Pages\ListProcessoSeletivos;
use App\Models\Process;
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
        Process::factory()->create([
            'is_published' => true,
            'title' => 'Processo Seletivo Teste',
            'number' => '001/2025',
        ]);

        $response = $this
            ->get(route('filament.app.resources.processo-seletivos.index', ['status' => 'em_andamento']));

        $response->assertStatus(200);
        $response->assertSeeText('Consultar Processos Seletivos');
    }

    public function test_candidato_filtra_processos_seletivos_por_status(): void
    {
        Filament::setCurrentPanel('app');
        // 🔹 Cria 5 processos seletivos com inscrições abertas / em andamento
        $abertos = \App\Models\Process::factory()
            ->count(5)
            ->create([
                'is_published' => true,
                'application_start_date' => now()->subDays(2),
                'application_end_date' => now()->addDays(2),
                'title' => 'Inscrição Aberta - ' . fake()->word(),
            ]);

        // 🔹 Cria 5 processos seletivos finalizados
        $finalizados = \App\Models\Process::factory()
            ->count(5)
            ->create([
                'is_published' => true,
                'publication_start_date' => now()->subDays(10),
                'publication_end_date' => now()->subDays(5),
                'title' => 'Finalizado - ' . fake()->word(),
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
