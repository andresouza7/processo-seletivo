<?php

namespace Database\Factories;

use App\Models\InscricaoPessoa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\InscricaoVaga;
use App\Models\ProcessoSeletivoTipo;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProcessoSeletivo>
 */
class ProcessoSeletivoFactory extends Factory
{
    public function definition(): array
    {
        $today = Carbon::today();
        $oneMonthLater = $today->copy()->addMonth();

        $numero = $this->faker->bothify('##/####');
        $diretorio = str_replace('/', '_', $numero);

        $tipo = \App\Models\ProcessoSeletivoTipo::factory()->create();

        return [
            // 'idprocesso_seletivo_tipo' => $this->faker->numberBetween(1, 4),
            'idprocesso_seletivo_tipo' => $tipo->idprocesso_seletivo_tipo,
            'titulo' => $this->faker->sentence(4),
            'descricao' => $this->faker->paragraph(),
            'numero' => $this->faker->bothify('##/####'),
            'data_criacao' => $today,
            // 'situacao' => $this->faker->randomElement(['A', 'I']),
            'situacao' => 'A',
            'acessos' => $this->faker->numberBetween(0, 1000),
            // 'publicado' => $this->faker->randomElement(['S', 'N']),
            'publicado' => 'S',
            'diretorio' => $diretorio,
            'data_publicacao_inicio' => $today,
            'data_publicacao_fim' => $oneMonthLater,
            'data_inscricao_inicio' => $today,
            'data_inscricao_fim' => $oneMonthLater,
            'data_recurso_inicio' => $today,
            'data_recurso_fim' => $oneMonthLater,
            // 'psu' => $this->faker->randomElement(['S', 'N']),
            'created_at' => now(),
            'updated_at' => now(),
            // 'requer_anexos' => $this->faker->boolean(),
        ];
    }

    /**
     * Attach vagas to the processo seletivo.
     */
    public function withVagas(int $count = 3): static
    {
        return $this->afterCreating(function ($processo) use ($count) {
            InscricaoVaga::factory($count)->create([
                'idprocesso_seletivo' => $processo->idprocesso_seletivo,
            ]);
        });
    }

    public function withInscricoes(int $qtdVagas = 3, int $qtdInscricoes = 5, int $qtdFiles = 1): static
    {
        return $this->afterCreating(function ($processo) use ($qtdVagas, $qtdInscricoes, $qtdFiles) {
            // cria vagas para o processo
            $vagas = \App\Models\InscricaoVaga::factory($qtdVagas)->create([
                'idprocesso_seletivo' => $processo->idprocesso_seletivo,
            ]);

            // pega ou cria pessoas
            $pessoas = \App\Models\InscricaoPessoa::all();
            if ($pessoas->isEmpty()) {
                $pessoas = \App\Models\InscricaoPessoa::factory($qtdInscricoes)->create();
            }

            // cria N inscrições, cada uma com vaga + pessoa aleatória
            \App\Models\Inscricao::factory()
                ->count($qtdInscricoes)
                ->withFiles($qtdFiles)
                ->sequence(fn($sequence) => [
                    'idprocesso_seletivo' => $processo->idprocesso_seletivo,
                    'idinscricao_vaga' => $vagas->random()->idinscricao_vaga,
                    'idinscricao_pessoa' => $pessoas->random()->idpessoa,
                ])
                ->create();
        });
    }
}
