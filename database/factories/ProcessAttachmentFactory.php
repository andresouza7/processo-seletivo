<?php

namespace Database\Factories;

use App\Models\Process;
use App\Models\ProcessAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessAttachmentFactory extends Factory
{
    protected $model = ProcessAttachment::class;

    public function definition()
    {
        return [
            'process_id' => Process::factory(), // create a related ProcessoSeletivo
            'idarquivo' => null, // optional, set manually if needed
            'description' => $this->faker->sentence(6),
            'publication_date' => $this->faker->date(),
            'views' => $this->faker->numberBetween(0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
