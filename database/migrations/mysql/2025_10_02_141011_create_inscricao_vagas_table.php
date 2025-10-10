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
        Schema::create('inscricao_vagas', function (Blueprint $table) {
            $table->id('idinscricao_vaga');
            
            $table->foreignId('idprocesso_seletivo')->constrained('processo_seletivo', 'idprocesso_seletivo');
            $table->string('codigo', 255)->nullable();
            $table->string('descricao', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscricao_vagas');
    }
};
