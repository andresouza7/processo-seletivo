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
        DB::statement('CREATE TABLE backup_inscricao AS SELECT * FROM inscricao');

        // Step 3: Enable strict mode back
        DB::statement('SET SESSION sql_mode = "STRICT_TRANS_TABLES"');

        // Step 4: Alter columns to be nullable
        Schema::table('inscricao', function (Blueprint $table) {
            $table->longText('qual_atendimento')->nullable()->change();
            $table->longText('observacao')->nullable()->change();
            $table->longText('local_prova')->nullable()->change();
            $table->string('ano_enem')->nullable()->change();
        });

        // Step 5: Update date columns to NULL where date is less than '1000-01-01'
        DB::table('inscricao')->where('qual_atendimento', '')->update(['qual_atendimento' => null]);
        DB::table('inscricao')->where('observacao', '')->update(['observacao' => null]);
        DB::table('inscricao')->where('local_prova', '')->update(['local_prova' => null]);
        DB::table('inscricao')->where('ano_enem', '')->update(['ano_enem' => null]);

        // Step 6: Convert the table to utf8mb4 character set and collation
        DB::statement('ALTER TABLE inscricao CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // Step 7: Convert text columns encoding (e.g., `titulo`)
        $columns = [
            'qual_atendimento',
            'observacao',
            'local_prova',
            'ano_enem'
        ];

        // Loop through each column and run the conversion twice
        foreach ($columns as $column) {
            DB::statement("UPDATE inscricao SET {$column} = CONVERT(CAST(CONVERT({$column} USING latin1) AS BINARY) USING utf8mb4)");
            DB::statement("UPDATE inscricao SET {$column} = CONVERT(CAST(CONVERT({$column} USING latin1) AS BINARY) USING utf8mb4)");
        }

        Schema::table('inscricao', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop the modified `processo_seletivo` table
        Schema::dropIfExists('inscricao');

        // Step 2: Rename the backup table back to the original name
        DB::statement('RENAME TABLE backup_inscricao TO inscricao');
    }
};
