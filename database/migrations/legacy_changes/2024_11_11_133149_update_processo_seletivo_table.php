<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UpdateProcessoSeletivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Step 1: Disable strict mode temporarily
        DB::statement('SET SESSION sql_mode = ""');

        // Step 2: Create a backup of the original table
        DB::statement('CREATE TABLE backup_processo_seletivo AS SELECT * FROM processo_seletivo');

        // Step 3: Enable strict mode back
        DB::statement('SET SESSION sql_mode = "STRICT_TRANS_TABLES"');

        // Step 4: Alter columns to be nullable
        Schema::table('processo_seletivo', function (Blueprint $table) {
            $table->date('data_publicacao_inicio')->nullable()->change();
            $table->date('data_publicacao_fim')->nullable()->change();
            $table->date('data_inscricao_inicio')->nullable()->change();
            $table->date('data_inscricao_fim')->nullable()->change();
            $table->date('data_recurso_inicio')->nullable()->change();
            $table->date('data_recurso_fim')->nullable()->change();

            $table->string('titulo')->nullable()->change();
            $table->longText('descricao')->nullable()->change();
        });

        // Step 5: Update date columns to NULL where date is less than '1000-01-01'
        DB::table('processo_seletivo')->where('data_publicacao_inicio', '<', '1000-01-01')->update(['data_publicacao_inicio' => null]);
        DB::table('processo_seletivo')->where('data_publicacao_fim', '<', '1000-01-01')->update(['data_publicacao_fim' => null]);
        DB::table('processo_seletivo')->where('data_inscricao_inicio', '<', '1000-01-01')->update(['data_inscricao_inicio' => null]);
        DB::table('processo_seletivo')->where('data_inscricao_fim', '<', '1000-01-01')->update(['data_inscricao_fim' => null]);
        DB::table('processo_seletivo')->where('data_recurso_inicio', '<', '1000-01-01')->update(['data_recurso_inicio' => null]);
        DB::table('processo_seletivo')->where('data_recurso_fim', '<', '1000-01-01')->update(['data_recurso_fim' => null]);

        DB::table('processo_seletivo')->where('titulo', '')->update(['titulo' => null]);
        DB::table('processo_seletivo')->where('descricao', '')->update(['descricao' => null]);

        // Step 6: Convert the table to utf8mb4 character set and collation
        DB::statement('ALTER TABLE processo_seletivo CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // Step 7: Convert text columns encoding (e.g., `titulo`)
        DB::statement("UPDATE processo_seletivo SET titulo = CONVERT(CAST(CONVERT(titulo USING latin1) AS BINARY) USING utf8mb4)");
        DB::statement("UPDATE processo_seletivo SET descricao = CONVERT(CAST(CONVERT(descricao USING latin1) AS BINARY) USING utf8mb4)");
        DB::statement("UPDATE processo_seletivo SET titulo = CONVERT(CAST(CONVERT(titulo USING latin1) AS BINARY) USING utf8mb4)");
        DB::statement("UPDATE processo_seletivo SET descricao = CONVERT(CAST(CONVERT(descricao USING latin1) AS BINARY) USING utf8mb4)");

        Schema::table('processo_seletivo', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Step 1: Drop the modified `processo_seletivo` table
        Schema::dropIfExists('processo_seletivo');

        // Step 2: Rename the backup table back to the original name
        DB::statement('RENAME TABLE backup_processo_seletivo TO processo_seletivo');
    }
}
