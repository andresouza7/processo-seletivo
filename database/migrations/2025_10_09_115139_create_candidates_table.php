<?php

use App\Models\Candidate;
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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('social_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('sexual_orientation')->nullable();
            $table->string('disability')->nullable();
            $table->string('race')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('community')->nullable();
            $table->string('gender_identity')->nullable();
            $table->string('sex')->nullable();
            $table->string('rg')->unique()->nullable();
            $table->char('cpf', 11)->unique();
            $table->string('postal_code', 20)->nullable();
            $table->string('district')->nullable();
            $table->string('address')->nullable();
            $table->string('address_number')->nullable();
            $table->string('address_complement')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->boolean('must_change_password')->default(false)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Candidate::factory()->create([
        //     'name' => 'Candidato',
        //     'cpf' => '12345678910',
        //     'password' => bcrypt('123456')
        // ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
