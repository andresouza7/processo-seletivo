<?php

namespace Database\Factories;

use App\Models\ProcessoSeletivoTipo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProcessoSeletivoTipo>
 */
class ProcessoSeletivoTipoFactory extends Factory
{
    protected $model = ProcessoSeletivoTipo::class;

    public function definition(): array
    {
        return [
            'descricao' => $this->faker->words(2, true), // exemplo: "Edital Público"
            'chave'     => strtoupper($this->faker->lexify('???')), // ex: "ABC"
        ];
    }
}
