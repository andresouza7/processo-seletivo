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
        Schema::table('inscricao_pessoa', function (Blueprint $table) {
            $table->string('identidade_genero_descricao')->after('identidade_genero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscricao_pessoa', function (Blueprint $table) {
            $table->dropColumn([
                'identidade_genero_descricao',                
            ]);
        });
    }
};
