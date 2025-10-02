<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricao_pessoa', function (Blueprint $table) {
            $table->id('idpessoa');
            $table->string('nome', 255)->nullable();
            $table->string('nome_social', 255)->nullable();
            $table->string('mae', 100)->nullable();
            $table->string('data_nascimento', 20)->nullable();
            $table->string('orientacao_sexual', 255)->nullable();
            $table->boolean('deficiencia')->default(0);
            $table->string('raca', 2)->nullable();
            $table->string('deficiencia_descricao', 255)->nullable();
            $table->string('estado_civil', 2)->nullable();
            $table->string('comunidade', 2)->nullable();
            $table->string('identidade_genero', 255)->nullable();
            $table->string('sexo', 255)->nullable();
            $table->string('ci', 255)->nullable();
            $table->char('cpf', 11);
            $table->string('matricula', 255)->nullable();
            $table->string('endereco', 255)->nullable();
            $table->string('cep', 20)->nullable();
            $table->string('bairro', 255)->nullable();
            $table->string('numero', 255)->nullable();
            $table->string('complemento', 255)->nullable();
            $table->string('cidade', 255)->nullable();
            $table->string('telefone', 255)->nullable();
            $table->string('email', 255);
            $table->string('senha', 255)->nullable();
            $table->string('perfil', 100);
            $table->char('situacao', 1);
            $table->string('link_lattes', 255)->nullable();
            $table->text('resumo')->nullable();
            $table->string('password', 255);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->boolean('must_change_password')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricao_pessoa');
    }
};
