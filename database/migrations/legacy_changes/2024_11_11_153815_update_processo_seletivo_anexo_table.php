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
        DB::statement('CREATE TABLE backup_processo_seletivo_anexo AS SELECT * FROM processo_seletivo_anexo');

        // Step 3: Enable strict mode back
        DB::statement('SET SESSION sql_mode = "STRICT_TRANS_TABLES"');

        // Step 4: Alter columns to be nullable
        Schema::table('processo_seletivo_anexo', function (Blueprint $table) {
            $table->string('descricao')->nullable()->change();
        });

        // Step 5: Update date columns to NULL where date is less than '1000-01-01'
        DB::table('processo_seletivo_anexo')->where('descricao', '')->update(['descricao' => null]);

        // Step 6: Convert the table to utf8mb4 character set and collation
        DB::statement('ALTER TABLE processo_seletivo_anexo CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // Step 7: Convert text columns encoding (e.g., `titulo`)
        $columns = [
            'descricao',
        ];

        // Loop through each column and run the conversion twice
        foreach ($columns as $column) {
            DB::statement("UPDATE processo_seletivo_anexo SET {$column} = CONVERT(CAST(CONVERT({$column} USING latin1) AS BINARY) USING utf8mb4)");
            DB::statement("UPDATE processo_seletivo_anexo SET {$column} = CONVERT(CAST(CONVERT({$column} USING latin1) AS BINARY) USING utf8mb4)");
        }

        Schema::table('processo_seletivo_anexo', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop the modified `processo_seletivo` table
        Schema::dropIfExists('processo_seletivo_anexo');

        // Step 2: Rename the backup table back to the original name
        DB::statement('RENAME TABLE backup_processo_seletivo_anexo TO processo_seletivo_anexo');
    }
};
