<?php

namespace Database\Factories;

use App\Models\Inscricao;
use App\Models\InscricaoPessoa;
use App\Models\InscricaoVaga;
use App\Models\ProcessoSeletivo;
use App\Models\TipoVaga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inscricao>
 */
class InscricaoFactory extends Factory
{
    protected $model = Inscricao::class;

    protected $filesCount = 1; // default 1 file

    public function definition()
    {
        $atendimento = $this->faker->randomElement(['S', 'N']);
        $tipo = TipoVaga::factory()->create();

        return [
            'cod_inscricao' => Inscricao::generateUniqueCode(),
            'idprocesso_seletivo' => ProcessoSeletivo::factory(),
            'idinscricao_vaga' => InscricaoVaga::factory(),
            'idinscricao_pessoa' => InscricaoPessoa::factory(),
            'idtipo_vaga' =>$tipo->id_tipo_vaga,
            'data_hora' => now(),
            'necessita_atendimento' => $atendimento,
            'qual_atendimento' => $atendimento === 'S' ? $this->faker->optional()->sentence() : null,
            'observacao' => $this->faker->optional()->paragraph(),
            'local_prova' => $this->faker->optional()->city(),
            'ano_enem' => null,
            'bonificacao' => null,
        ];
    }

    /**
     * Set how many files to attach to the Inscricao.
     */
    public function withFiles(int $count = 1): static
    {
        return $this->afterCreating(function ($inscricao) use ($count) {
            $content = file_get_contents(storage_path('app/public/template.pdf'));
            $processoSeletivo = $inscricao->processo_seletivo;
            $tipo = optional($processoSeletivo->tipo)->chave;
            $diretorio = $processoSeletivo->diretorio;
            $id = $inscricao->idinscricao;

            for ($i = 1; $i <= $count; $i++) {
                $filepath = "{$tipo}/{$diretorio}/inscricoes/{$id}/template_{$i}.pdf";

                $inscricao
                    ->addMediaFromString($content)
                    ->usingFileName($filepath)
                    ->toMediaCollection('documentos_requeridos');
            }
        });
    }
}
