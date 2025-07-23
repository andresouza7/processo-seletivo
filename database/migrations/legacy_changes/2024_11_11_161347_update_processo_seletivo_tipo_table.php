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
        DB::statement('CREATE TABLE backup_processo_seletivo_tipo AS SELECT * FROM processo_seletivo_tipo');

        // Step 3: Enable strict mode back
        DB::statement('SET SESSION sql_mode = "STRICT_TRANS_TABLES"');

        // Step 6: Convert the table to utf8mb4 character set and collation
        DB::statement('ALTER TABLE processo_seletivo_tipo CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        Schema::table('processo_seletivo_tipo', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop the modified `processo_seletivo_tipo` table
        Schema::dropIfExists('processo_seletivo_tipo');

        // Step 2: Rename the backup table back to the original name
        DB::statement('RENAME TABLE backup_processo_seletivo_tipo TO processo_seletivo_tipo');
    }
};
