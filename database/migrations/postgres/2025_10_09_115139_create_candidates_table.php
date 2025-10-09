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
        Schema::disableForeignKeyConstraints();

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('social_name');
            $table->string('mother_name');
            $table->date('birth_date');
            $table->string('sexual_orientation');
            $table->boolean('has_disability')->default(false);
            $table->string('disability_description');
            $table->string('race');
            $table->string('marital_status', 2);
            $table->string('community');
            $table->string('gender_identity');
            $table->string('gender_identity_description');
            $table->string('sex', 2);
            $table->string('rg', 20)->unique();
            $table->char('cpf', 11)->unique();
            $table->string('postal_code', 20);
            $table->string('district');
            $table->string('number');
            $table->string('complement');
            $table->string('city');
            $table->string('phone');
            $table->bigInteger('email')->unique();
            $table->timestamp('email_verified_at');
            $table->string('remember_token');
            $table->boolean('must_change_password')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
