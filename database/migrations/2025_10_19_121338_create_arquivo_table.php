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
        Schema::create('arquivo', function (Blueprint $table) {
            $table->increments('idarquivo');
            $table->integer('size');
            $table->integer('width');
            $table->integer('height');
            $table->string('name');
            $table->string('mimetype');
            $table->string('descricao');
            $table->string('codname');
            $table->char('_del_', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arquivo');
    }
};
