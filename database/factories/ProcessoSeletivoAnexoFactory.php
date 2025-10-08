<?php

namespace Database\Factories;

use App\Models\ProcessoSeletivo;
use App\Models\ProcessoSeletivoAnexo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProcessoSeletivoAnexo>
 */
class ProcessoSeletivoAnexoFactory extends Factory
{
    protected $model = ProcessoSeletivoAnexo::class;

    public function definition()
    {
        return [
            'idprocesso_seletivo' => ProcessoSeletivo::factory(), // create a related ProcessoSeletivo
            'idarquivo' => null, // optional, set manually if needed
            'descricao' => $this->faker->sentence(6),
            'data_publicacao' => $this->faker->date(),
            'acessos' => $this->faker->numberBetween(0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
