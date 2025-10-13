<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\Process;
use App\Models\ProcessType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProcessFactory extends Factory
{
    protected $model = Process::class;

    public function definition(): array
    {
        $today = Carbon::today();
        $oneMonthLater = $today->copy()->addMonth();

        $number = $this->faker->bothify('##/####');
        // $number = $this->faker->numberBetween(1, 99) . '/' . $this->faker->randomElement([2025, 2026]);
        $directory = str_replace('/', '_', $number);

        $type = ProcessType::inRandomOrder()->first()?->id ?? ProcessType::factory();

        return [
            'process_type_id' => $type,
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'number' => $number,
            'views' => $this->faker->numberBetween(0, 1000),
            'is_published' => true,
            'directory' => $directory,
            'publication_start_date' => $today,
            'publication_end_date' => $oneMonthLater,
            'application_start_date' => $today,
            'application_end_date' => $oneMonthLater,
            'has_fee_exemption' => false,
            'attachment_fields' => null,

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Attach positions to the process.
     */
    public function withPositions(int $count = 3): static
    {
        return $this->afterCreating(function ($process) use ($count) {
            Position::factory($count)->create([
                'process_id' => $process->id,
            ]);
        });
    }

    /**
     * Attach applications to the process.
     */
    public function withApplications(
        int $positionCount = 2,
        int $applicationCount = 3,
        int $fileCount = 1
    ): static {
        return $this->afterCreating(function ($process) use ($positionCount, $applicationCount, $fileCount) {

            // Create positions for the process
            $positions = Position::factory($positionCount)->create([
                'process_id' => $process->id,
            ]);

            // Retrieve existing candidates or create new ones
            $candidates = Candidate::query()->exists()
                ? Candidate::all()
                : Candidate::factory($applicationCount)->create();

            // Create applications with random position and candidate
            Application::factory()
                ->count($applicationCount)
                ->withFiles($fileCount)
                ->sequence(fn() => [
                    'process_id'   => $process->id,
                    'position_id'  => $positions->random()->id,
                    'candidate_id' => $candidates->random()->id,
                ])
                ->create();
        });
    }
}
