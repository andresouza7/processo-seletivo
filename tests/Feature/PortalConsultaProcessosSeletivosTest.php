<?php

namespace Tests\Feature;

use App\Models\ProcessoSeletivo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;


class PortalConsultaProcessosSeletivosTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_consegue_ver_processos_seletivos(): void
    {
       
        ProcessoSeletivo::factory()->create([
            'publicado' => 'S',
            'titulo' => 'Processo Seletivo Teste',
            'numero' => '001/2025',
            'data_publicacao_inicio' => now(),
        ]);

        // Act
        $response = $this
            ->get(route('filament.app.resources.processo-seletivos.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSeeText('Consultar Processos Seletivos');
        // $response->assertSeeText('Processo Seletivo Teste');
    }

    public function test_candidato_filtra_inscricoes_realizadas(): void
    {
        // 🔹 Cria 5 processos seletivos com inscrições abertas
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
                'data_inscricao_inicio' => now()->subDays(10),
                'data_inscricao_fim' => now()->subDays(5),
                'titulo' => 'Finalizado - ' . fake()->word(),
            ]);

        // Act
        // 🔹 Faz requisição com o parâmetro de filtro
        $response = $this
            ->get(route('filament.app.resources.processo-seletivos.index', [
                'status' => 'inscricoes_abertas',
            ]));

        // Assert
        $response->assertStatus(200);

        // 🔹 Verifica que os 5 "abertos" aparecem
        foreach ($abertos as $processo) {
            $response->assertSeeText($processo->titulo);
        }

        // 🔹 Verifica que os 5 "finalizados" NÃO aparecem
        foreach ($finalizados as $processo) {
            $response->assertDontSeeText($processo->titulo);
        }
    }
    

    //🧩 Explicação
    //Etapa;	-O que faz
    //Criação dos registros;	-Usa a factory para criar 10 processos seletivos (5 abertos e 5 finalizados).
    //Filtro;	-Envia o parâmetro status=inscricoes_abertas via GET, simulando o uso do filtro da tabela Filament.
    // Asserts;	-Verifica se os títulos dos abertos estão na resposta e se os finalizados não aparecem.
}
