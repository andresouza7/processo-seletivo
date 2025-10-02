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
        Schema::create('processo_seletivo_anexo', function (Blueprint $table) {
            $table->increments('idprocesso_seletivo_anexo');
            
            $table->foreignId('idprocesso_seletivo')->constrained('processo_seletivo', 'idprocesso_seletivo');
            $table->unsignedBigInteger('idarquivo')->nullable();
            $table->string('descricao', 255)->nullable();
            $table->date('data_publicacao')->nullable();
            $table->integer('acessos');
            $table->timestamps();

            // Índice para a chave estrangeira
            // $table->index('idprocesso_seletivo');

            // Caso exista tabela de arquivos, pode ser FK opcional
            // $table->foreign('idarquivo')->references('id')->on('arquivos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processo_seletivo_anexo');
    }
};
