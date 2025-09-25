<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inscricao', function (Blueprint $table) {
            // First ensure the column exists
            // then add the foreign key constraint with cascade delete
            $table->foreign('idprocesso_seletivo')
                ->references('idprocesso_seletivo')
                ->on('processo_seletivo')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscricao', function (Blueprint $table) {
            // Drop the foreign key before rolling back
            $table->dropForeign(['idprocesso_seletivo']);
        });
    }
};
