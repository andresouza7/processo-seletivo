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

        User::factory()->count(5)->create();

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

        Quota::insert($tiposFixos);

        // ------------------------
        // 3️⃣ Tipos de Processo Seletivo
        // ------------------------
        $psTiposFixos = [
            ['description' => 'Processo Seletivo Simplificado', 'slug' => 'PSS'],
            ['description' => 'Concurso Público', 'slug' => 'CON'],
            ['description' => 'Edital Interno', 'slug' => 'EDI'],
            ['description' => 'Transferência', 'slug' => 'TRA'],
        ];

        ProcessType::insert($psTiposFixos);

        // ------------------------
        // 5️⃣ Processos Seletivos
        // ------------------------
        Process::factory()
            ->count(5)
            ->withApplications(3, 10, 2)
            ->create();
    }
}
