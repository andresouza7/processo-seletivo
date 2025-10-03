<?php

namespace Database\Factories;

use App\Models\TipoVaga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipoVaga>
 */
class TipoVagaFactory extends Factory
{
    protected $model = TipoVaga::class;

    public function definition(): array
    {
        return [
            'descricao' => $this->faker->sentence(3), // ex: "Vaga administrativa"
        ];
    }
}
