<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Criar roles
        DB::table('roles')->insert([
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'avaliador', 'guard_name' => 'web'],
            ['name' => 'dips', 'guard_name' => 'web'],
            ['name' => 'ascom', 'guard_name' => 'web'],
            ['name' => 'prograd', 'guard_name' => 'web'],
        ]);

        // 2️⃣ Criar permissões
        $permissions = [
            // PROCESSO SELETIVO
            'consultar processo',
            'gerenciar processo',

            // ANEXOS
            'consultar anexo',
            'gerenciar anexo',

            // INSCRIÇÕES
            'consultar inscrição',

            // VAGAS
            'gerenciar vaga',
            'consultar vaga',

            // ETAPA RECURSO
            'gerenciar etapa de recurso',

            // RECURSO
            'consultar recurso',
            'avaliar recurso',
            'atribuir avaliador',

            // CANDIDATOS
            'consultar candidato',
        ];

        $now = Carbon::now();

        DB::table('permissions')->insert(
            collect($permissions)->map(fn($p) => [
                'name' => $p,
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray()
        );

        // 3️⃣ Atribuir permissões aos papéis
        $avaliador = Role::where('name', 'avaliador')->first();
        $avaliador->givePermissionTo([
            'consultar recurso',
            'avaliar recurso'
        ]);

        $dips = Role::where('name', 'dips')->first();
        $dips->givePermissionTo([
            'gerenciar processo',
            'gerenciar anexo',
            'gerenciar etapa de recurso',
            'gerenciar vaga',
            'atribuir avaliador',
            'avaliar recurso',
            'consultar inscrição',
            'consultar candidato',
        ]);

        $ascom = Role::where('name', 'ascom')->first();
        $ascom->givePermissionTo([
            'gerenciar processo',
            'gerenciar anexo',
            'gerenciar etapa de recurso',
        ]);

        $prograd = Role::where('name', 'prograd')->first();
        $prograd->givePermissionTo([
            'gerenciar processo',
            'gerenciar anexo',
            'gerenciar etapa de recurso',
            'gerenciar vaga',
            'atribuir avaliador',
            'consultar inscrição',
            'consultar candidato',
        ]);

        // 4️⃣ Criar usuários e atribuir roles
        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@ueap.edu.br',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole('admin');

        $dipsUser = User::factory()->create([
            'name' => 'dips',
            'email' => 'dips@ueap.edu.br',
            'password' => Hash::make('123456'),
        ]);
        $dipsUser->assignRole('dips');

        $ascomUser = User::factory()->create([
            'name' => 'ascom',
            'email' => 'ascom@ueap.edu.br',
            'password' => Hash::make('123456'),
        ]);
        $ascomUser->assignRole('ascom');

        $progradUser = User::factory()->create([
            'name' => 'prograd',
            'email' => 'prograd@ueap.edu.br',
            'password' => Hash::make('123456'),
        ]);
        $progradUser->assignRole('prograd');

        $avaliadorUser = User::factory()->create([
            'name' => 'avaliador',
            'email' => 'avaliador@ueap.edu.br',
            'password' => Hash::make('123456'),
        ]);
        $avaliadorUser->assignRole('avaliador');
    }

    public function down(): void
    {
        // Remover usuários
        User::whereIn('email', [
            'admin@ueap.edu.br',
            'ascom@ueap.edu.br',
            'prograd@ueap.edu.br',
        ])->delete();

        // Remover roles
        Role::whereIn('name', ['admin', 'avaliador', 'ascom', 'prograd'])->delete();

        // Remover permissões
        Permission::whereIn('name', [
            'consultar processo',
            'gerenciar processo',
            'consultar anexo',
            'gerenciar anexo',
            'gerenciar inscrição',
            'consultar inscrição',
            'gerenciar vaga',
            'consultar vaga',
            'gerenciar etapa de recurso',
            'consultar recurso',
            'avaliar recurso',
            'atribuir avaliador',
            'consultar candidato',
        ])->delete();
    }
};
