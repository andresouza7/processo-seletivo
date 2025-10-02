<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('inscricao_pessoa', function (Blueprint $table) {
            $table->string('situacao', 10)->change(); // define como varchar(10)
        });
    }
    
    public function down()
    {
        Schema::table('inscricao_pessoa', function (Blueprint $table) {
            $table->text('situacao')->change(); // volta para text
        });
    }
    
};
