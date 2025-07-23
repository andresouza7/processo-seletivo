<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('inscricao', function (Blueprint $table) {
            $table->primary('idinscricao');
            $table->integer('idinscricao')->autoIncrement()->change();
        });

        Schema::table('inscricao_pessoa', function (Blueprint $table) {
            $table->primary('idpessoa');
            $table->integer('idpessoa')->autoIncrement()->change();
        });

        Schema::table('inscricao_vagas', function (Blueprint $table) {
            $table->primary('idinscricao_vaga');
            $table->integer('idinscricao_vaga')->autoIncrement()->change();
        });

        Schema::table('processo_seletivo', function (Blueprint $table) {
            $table->primary('idprocesso_seletivo');
            $table->integer('idprocesso_seletivo')->autoIncrement()->change();
        });

        Schema::table('processo_seletivo_anexo', function (Blueprint $table) {
            $table->primary('idprocesso_seletivo_anexo');
            $table->integer('idprocesso_seletivo_anexo')->autoIncrement()->change();
        });

        Schema::table('processo_seletivo_tipo', function (Blueprint $table) {
            $table->primary('idprocesso_seletivo_tipo');
            $table->integer('idprocesso_seletivo_tipo')->autoIncrement()->change();
        });

        Schema::table('recursos', function (Blueprint $table) {
            $table->primary('idrecurso');
            $table->integer('idrecurso')->autoIncrement()->change();
        });
    }

    public function down()
    {
        Schema::table('inscricao', function (Blueprint $table) {
            $table->dropPrimary(['idinscricao']);
            $table->integer('idinscricao')->default(0)->change();
        });

        Schema::table('inscricao_pessoa', function (Blueprint $table) {
            $table->dropPrimary(['idpessoa']);
            $table->integer('idpessoa')->default(0)->change();
        });

        Schema::table('inscricao_vagas', function (Blueprint $table) {
            $table->dropPrimary(['idinscricao_vaga']);
            $table->integer('idinscricao_vaga')->default(0)->change();
        });

        Schema::table('processo_seletivo', function (Blueprint $table) {
            $table->dropPrimary(['idprocesso_seletivo']);
            $table->integer('idprocesso_seletivo')->default(0)->change();
        });

        Schema::table('processo_seletivo_anexo', function (Blueprint $table) {
            $table->dropPrimary(['idprocesso_seletivo_anexo']);
            $table->integer('idprocesso_seletivo_anexo')->default(0)->change();
        });

        Schema::table('processo_seletivo_tipo', function (Blueprint $table) {
            $table->dropPrimary(['idprocesso_seletivo_tipo']);
            $table->integer('idprocesso_seletivo_tipo')->default(0)->change();
        });

        Schema::table('recursos', function (Blueprint $table) {
            $table->dropPrimary(['idrecurso']);
            $table->integer('idrecurso')->default(0)->change();
        });
    }
};
