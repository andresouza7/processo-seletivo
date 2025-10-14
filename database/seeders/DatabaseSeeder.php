<?php

namespace Database\Seeders;

use App\Models\Process;
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
        // Usuários
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
        // Tipos de Cotas
        // ------------------------
        $tiposFixos = [
            ['description' => 'Ampla Concorrência'],
            ['description' => 'Cota Racial'],
            ['description' => 'Pessoa com Deficiência'],
            ['description' => 'Escola Pública'],
            ['description' => 'Interiorização'],
        ];

        Quota::insert($tiposFixos);

        // ------------------------
        // Tipos de Processo Seletivo
        // ------------------------
        $psTiposFixos = [
            ['description' => 'Processo Seletivo Simplificado', 'slug' => 'pss'],
            ['description' => 'Processo Seletivo', 'slug' => 'ps'],
            ['description' => 'Editais', 'slug' => 'editais'],
            ['description' => 'Pós-Graduação', 'slug' => 'pos'],
        ];

        ProcessType::insert($psTiposFixos);

        // ------------------------
        // Processos Seletivos
        // ------------------------
        Process::factory()
            ->count(5)
            ->withApplications(3, 4, 0) // vagas, inscricoes, anexos
            ->create();
    }
}
