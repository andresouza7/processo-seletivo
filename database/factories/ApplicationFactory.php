<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\Process;
use App\Models\Quota;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    protected $filesCount = 1; // default 1 file

    public function definition()
    {
        $needs_assistance = $this->faker->randomElement([true, false]);
        $quota = Quota::inRandomOrder()->first()?->id ?? Quota::factory();

        return [
            'code' => Application::generateUniqueCode(),
            'process_id' => Process::factory(),
            'position_id' => Position::factory(),
            'candidate_id' => Candidate::factory(),
            'quota_id' => $quota,
            'requires_assistance' => $needs_assistance,
            'assistance_details' => $needs_assistance ? $this->faker->optional()->sentence() : null,

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Set how many files to attach to the Inscricao.
     */
    public function withFiles(int $count = 1): static
    {
        return $this->afterCreating(function ($application) use ($count) {
            $content = file_get_contents(storage_path('app/public/template.pdf'));
            $process = $application->process;
            $type = $process->type->slug;
            $directory = $process->directory;
            $id = $application->id;

            for ($i = 1; $i <= $count; $i++) {
                $filepath = "{$type}/{$directory}/inscricoes/{$id}/template_{$i}.pdf";

                $application
                    ->addMediaFromString($content)
                    ->usingFileName($filepath)
                    ->toMediaCollection('default', 'local');
            }
        });
    }
}
