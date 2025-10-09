<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\Process;
use App\Models\ProcessAttachment;
use App\Models\ProcessType;
use App\Models\Quota;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Models
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ------------------------
        // 1️⃣ Usuários
        // ------------------------
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        User::factory()->count(10)->create();

        // ------------------------
        // 2️⃣ Tipos de Cotas
        // ------------------------
        $tiposFixos = [
            // ['description' => 'Ampla Concorrência'],
            ['description' => 'Cota Racial'],
            ['description' => 'Pessoa com Deficiência'],
            ['description' => 'Escola Pública'],
            ['description' => 'Interiorização'],
        ];

        foreach ($tiposFixos as $tipo) {
            Quota::firstOrCreate(['description' => $tipo['description']], $tipo);
        }

        // Quota::factory()->count(5)->create();

        // ------------------------
        // 3️⃣ Tipos de Processo Seletivo
        // ------------------------
        $psTiposFixos = [
            ['description' => 'Processo Seletivo Simplificado', 'slug' => 'PSS'],
            ['description' => 'Concurso Público', 'slug' => 'CON'],
            ['description' => 'Edital Interno', 'slug' => 'EDI'],
            ['description' => 'Transferência', 'slug' => 'TRA'],
        ];

        foreach ($psTiposFixos as $tipo) {
            ProcessType::firstOrCreate(['slug' => $tipo['slug']], $tipo);
        }

        // Process::factory()->count(3)->create();

        // ------------------------
        // 4️⃣ Pessoas inscritas
        // ------------------------
        Candidate::factory()->count(50)->create();

        // ------------------------
        // 5️⃣ Processos Seletivos
        // ------------------------
        $processos = Process::factory()
            ->count(5)
            ->withApplications(3,10,2)
            ->create();

        // ------------------------
        // 7️⃣ Anexos de Processos Seletivos
        // ------------------------
        foreach ($processos as $processo) {
            ProcessAttachment::factory()
                ->count(rand(1, 3))
                ->create([
                    'process_id' => $processo->id
                ]);
        }

        // ------------------------
        // 8️⃣ Inscrição Vaga
        // ------------------------
        foreach ($processos as $processo) {
            $vagas = Position::factory()->count(3)->create([
                'process_id' => $processo->id,
            ]);

            $pessoas = Candidate::all();

            // Cria inscrições
            Application::factory()
                ->count(10)
                ->withFiles(2)
                ->sequence(fn($sequence) => [
                    'process_id' => $processo->id,
                    'position_id' => $vagas->random()->id,
                    'candidate_id' => $pessoas->random()->id,
                ])
                ->create();
        }
    }
}
