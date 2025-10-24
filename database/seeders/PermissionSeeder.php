<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // PROCESSO SELETIVO
            'view process',
            'create process',
            'edit process',

            // ANEXOS
            'view attachment',
            'create attachment',
            'edit attachment',

            // INSCRIÇÕES
            'create application',
            'view application',
            'export applications',

            // VAGAS
            'create position',
            'view position',

            // ETAPA RECURSO
            'create appeal stage',

            // RECURSO
            'submit appeal',
            'view appeal',
            'evaluate appeal',
            'assign evaluator',

            // CANDIDATOS
            'view candidate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
