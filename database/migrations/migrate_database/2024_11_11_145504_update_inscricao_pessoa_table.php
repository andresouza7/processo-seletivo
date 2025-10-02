<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Disable strict mode temporarily
        DB::statement('SET SESSION sql_mode = ""');

        // Step 2: Create a backup of the original table
        DB::statement('CREATE TABLE backup_inscricao_pessoa AS SELECT * FROM inscricao_pessoa');

        // Step 3: Enable strict mode back
        DB::statement('SET SESSION sql_mode = "STRICT_TRANS_TABLES"');

        // Step 4: Alter columns to be nullable
        Schema::table('inscricao_pessoa', function (Blueprint $table) {
            $table->string('complemento')->nullable()->change();
            $table->text('resumo')->nullable()->change();
            $table->string('nome')->nullable()->change();
            $table->string('numero')->nullable()->change();
            $table->string('endereco')->nullable()->change();
            $table->string('bairro')->nullable()->change();
            $table->string('complemento')->nullable()->change();
            $table->string('cidade')->nullable()->change();
            $table->string('sexo')->nullable()->change();
            $table->string('ci')->nullable()->change();
            $table->string('matricula')->nullable()->change();
            $table->string('telefone')->nullable()->change();
            $table->string('senha')->nullable()->change();
        });

        // Step 5: Update date columns to NULL where date is less than '1000-01-01'
        DB::table('inscricao_pessoa')->where('complemento', '')->update(['complemento' => null]);
        DB::table('inscricao_pessoa')->where('resumo', '')->update(['resumo' => null]);
        DB::table('inscricao_pessoa')->where('nome_social', '')->update(['nome_social' => null]);
        DB::table('inscricao_pessoa')->where('mae', '')->update(['mae' => null]);
        DB::table('inscricao_pessoa')->where('data_nascimento', '')->update(['data_nascimento' => null]);
        DB::table('inscricao_pessoa')->where('identidade_genero', '')->update(['identidade_genero' => null]);
        DB::table('inscricao_pessoa')->where('endereco', '')->update(['endereco' => null]);
        DB::table('inscricao_pessoa')->where('numero', '')->update(['numero' => null]);
        DB::table('inscricao_pessoa')->where('bairro', '')->update(['bairro' => null]);
        DB::table('inscricao_pessoa')->where('complemento', '')->update(['complemento' => null]);
        DB::table('inscricao_pessoa')->where('cidade', '')->update(['cidade' => null]);
        DB::table('inscricao_pessoa')->where('numero', '')->update(['numero' => null]);
        DB::table('inscricao_pessoa')->where('sexo', '')->update(['sexo' => null]);
        DB::table('inscricao_pessoa')->where('ci', '')->update(['ci' => null]);
        DB::table('inscricao_pessoa')->where('matricula', '')->update(['matricula' => null]);
        DB::table('inscricao_pessoa')->where('telefone', '')->update(['telefone' => null]);
        DB::table('inscricao_pessoa')->where('link_lattes', '')->update(['link_lattes' => null]);
        DB::table('inscricao_pessoa')->where('orientacao_sexual', '')->update(['orientacao_sexual' => null]);
        DB::table('inscricao_pessoa')->where('senha', '')->update(['senha' => null]);

        // Step 6: Convert the table to utf8mb4 character set and collation
        DB::statement('ALTER TABLE inscricao_pessoa CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // Step 7: Convert text columns encoding (e.g., `titulo`)
        $columns = [
            'nome',
            'nome_social',
            'mae',
            'orientacao_sexual',
            'identidade_genero',
            'endereco',
            'bairro',
            'numero',
            'complemento',
            'cidade',
            'perfil',
            'link_lattes'
        ];

        // Loop through each column and run the conversion twice
        foreach ($columns as $column) {
            DB::statement("UPDATE inscricao_pessoa SET {$column} = CONVERT(CAST(CONVERT({$column} USING latin1) AS BINARY) USING utf8mb4)");
            DB::statement("UPDATE inscricao_pessoa SET {$column} = CONVERT(CAST(CONVERT({$column} USING latin1) AS BINARY) USING utf8mb4)");
        }

        // Step 8: Update password field
        Schema::table('inscricao_pessoa', function (Blueprint $table) {
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
        });

        Schema::table('inscricao_pessoa', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop the modified `processo_seletivo` table
        Schema::dropIfExists('inscricao_pessoa');

        // Step 2: Rename the backup table back to the original name
        DB::statement('RENAME TABLE backup_inscricao_pessoa TO inscricao_pessoa');
    }
};
