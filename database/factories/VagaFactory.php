<?php

namespace Database\Factories;

use App\Models\ProcessoSeletivo;
use App\Models\Vaga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vaga>
 */
class VagaFactory extends Factory
{
    protected $model = Vaga::class;

    public function definition()
    {
        $possui_taxa = $this->faker->boolean();
        return [
            'nome' => $this->faker->word,
            'possui_taxa' => $possui_taxa,
            'valor_taxa' => $possui_taxa ? $this->faker->numberBetween(80, 150) : null
        ];
    }

    public function forProcesso(ProcessoSeletivo $processo)
    {
        return $this->state(function (array $attributes) use ($processo) {
            return [
                'psel_id' => $processo->id,
            ];
        });
    }
}
