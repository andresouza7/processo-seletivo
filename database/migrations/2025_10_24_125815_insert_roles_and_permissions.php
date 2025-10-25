<?php

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
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
        DB::table('roles')->insert(
            collect(RolesEnum::cases())->map(fn($role) => [
                'name' => $role->value,
                'guard_name' => 'web',
            ])->toArray()
        );

        // 2️⃣ Criar permissões
        $now = Carbon::now();

        DB::table('permissions')->insert(
            collect(PermissionsEnum::cases())->map(fn($perm) => [
                'name' => $perm->value,
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray()
        );

        // 3️⃣ Atribuir permissões aos papéis
        Role::where('name', RolesEnum::AVALIADOR)->first()?->givePermissionTo([
            PermissionsEnum::CONSULTAR_RECURSO,
            PermissionsEnum::AVALIAR_RECURSO,
        ]);

        Role::where('name', RolesEnum::DIPS)->first()?->givePermissionTo([
            PermissionsEnum::GERENCIAR_PROCESSO,
            PermissionsEnum::GERENCIAR_ANEXO,
            PermissionsEnum::GERENCIAR_ETAPA_RECURSO,
            PermissionsEnum::GERENCIAR_VAGA,
            PermissionsEnum::ATRIBUIR_AVALIADOR,
            PermissionsEnum::AVALIAR_RECURSO,
            PermissionsEnum::CONSULTAR_INSCRICAO,
            PermissionsEnum::CONSULTAR_CANDIDATO,
        ]);

        Role::where('name', RolesEnum::ASCOM)->first()?->givePermissionTo([
            PermissionsEnum::GERENCIAR_PROCESSO,
            PermissionsEnum::GERENCIAR_ANEXO,
            PermissionsEnum::GERENCIAR_ETAPA_RECURSO,
        ]);

        Role::where('name', RolesEnum::PROGRAD)->first()?->givePermissionTo([
            PermissionsEnum::GERENCIAR_PROCESSO,
            PermissionsEnum::GERENCIAR_ANEXO,
            PermissionsEnum::GERENCIAR_ETAPA_RECURSO,
            PermissionsEnum::GERENCIAR_VAGA,
            PermissionsEnum::ATRIBUIR_AVALIADOR,
            PermissionsEnum::CONSULTAR_INSCRICAO,
            PermissionsEnum::CONSULTAR_CANDIDATO,
        ]);

        // 4️⃣ Criar usuários e atribuir roles
        $users = [
            ['name' => 'admin', 'email' => 'admin@ueap.edu.br', 'role' => RolesEnum::ADMIN],
            ['name' => 'dips', 'email' => 'dips@ueap.edu.br', 'role' => RolesEnum::DIPS],
            ['name' => 'ascom', 'email' => 'ascom@ueap.edu.br', 'role' => RolesEnum::ASCOM],
            ['name' => 'prograd', 'email' => 'prograd@ueap.edu.br', 'role' => RolesEnum::PROGRAD],
            ['name' => 'avaliador', 'email' => 'avaliador@ueap.edu.br', 'role' => RolesEnum::AVALIADOR],
        ];

        foreach ($users as $data) {
            $user = User::factory()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('123456'),
            ]);

            $user->assignRole($data['role']);
        }
    }

    public function down(): void
    {
        // 1️⃣ Remover usuários criados
        User::whereIn('email', [
            'admin@ueap.edu.br',
            'dips@ueap.edu.br',
            'ascom@ueap.edu.br',
            'prograd@ueap.edu.br',
            'avaliador@ueap.edu.br',
        ])->delete();

        // 2️⃣ Limpar roles e permissões
        DB::statement('TRUNCATE TABLE roles RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE permissions RESTART IDENTITY CASCADE');
    }
};
